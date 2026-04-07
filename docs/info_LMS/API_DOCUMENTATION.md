# Melesatt LMS – API Documentation

Berikut adalah dokumentasi menyeluruh dari rute RESTful API yang melayani ekosistem modul Melesatt LMS. Rute-rute ini diamankan menggunakan **Laravel Sanctum**.

---

## 1. Authentication (Global)
Akses endpoint tanpa pengecekan role, bertujuan untuk memfasilitasi masuk & keluarnya user dari sistem API.

| Method | Endpoint | Middleware | Keterangan |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/lms/login` | *None* | Meminta Token otorisasi Sanctum. |
| `POST` | `/api/lms/logout` | `auth:sanctum` | Menghancurkan Token pada sesi saat ini. |

#### Request Login:
```json
{
  "login": "guru1@sekolah.com",
  "password": "password123"
}
```

#### Response Success (200 OK):
```json
{
  "message": "Login berhasil",
  "token": "1|abc123token...",
  "role": "teacher",
  "user_data": { ... }
}
```

---

## 2. Super Admin Area (Master Data)
Rute-rute operasi penuh untuk Admin.
**Middleware Target:** `['auth:sanctum', 'role:super_admin']`

> **Catatan API Resource:** Semua route CRUD di bawah mengikuti standar Resource Router Laravel (index, store, show, update, destroy).

### 2.1. User Management
*   `GET /api/lms/accounts` (List all accounts)
*   `GET /api/lms/teachers` (List teachers with their tied accounts)
*   `GET /api/lms/students` (List students with their tied accounts)

*(Menerima verbs `POST`, `PUT/{id}`, `DELETE/{id}`, dan `GET/{id}` pada endpoint yang sama sesuai standar arsitektur REST).*

### 2.2. Academic Data
*   `GET /api/lms/classrooms` (Manajemen data master kelas, tingkat, dan angkatan).
*   `GET /api/lms/subjects` (Manajemen data master mata pelajaran).
*   `GET /api/lms/student-positions` (Manajemen master jabatan anggota kelas).

### 2.3. Jadwal KBM (Schedules)
*   `POST /api/lms/schedules`
    *   **Body JSON:** `classroom_id` (uuid), `teacher_id` (uuid), `subject_id` (int), `day` (string), `start_time` (H:i), `end_time` (H:i).

### 2.4. Sinkronisasi Data Siswa ke Kelas
Endpoint ini secara khusus merapihkan data formasi *pivot*.
*   `POST /api/lms/classrooms/assign-student`
    *   **Deskripsi:** Memasukkan siswa ke dalam kelas, dan menambahkan posisinya (KM, Sekretaris). Menjalankan `syncWithoutDetaching`.
    *   **Body JSON:**
```json
{
  "classroom_id": "9b1c23d4-...",
  "students": [
    { "student_id": "9a1c...", "student_position_id": 1 },
    { "student_id": "9b2c...", "student_position_id": 3 }
  ]
}
```

---

## 3. Teacher Area (Akses Pengajar)
Area operasi personal guru pengampu kelas.
**Middleware Target:** `['auth:sanctum', 'role:teacher']`

| Method | Endpoint | Fungsi |
| :--- | :--- | :--- |
| `GET` | `/api/lms/teacher/schedules` | Menarik daftar Jadwal Mengajar untuk *Wali Kelas/Guru tersebut*. |
| `GET` | `/api/lms/teacher/materials` | Menarik daftar seluruh materi yang diupload Sang Guru. |
| `POST` | `/api/lms/teacher/materials` | Mengupload materi (Form-Data: *title, file, schedule_id*). |
| `PUT` | `/api/lms/teacher/materials/{id}`| Edit data materi atau *re-upload* file fisiknya. |
| `DELETE` | `/api/lms/teacher/materials/{id}`| Hapus materi dan _destroy file Storage_. |
| `POST` | `/api/lms/teacher/attendances` | Input data absen massal kelas. |

#### Request Guru Mengisi Absensi (StoreAttendanceRequest):
```json
{
  "schedule_id": 1,
  "date": "2026-04-06",
  "attendances": [
    { "student_id": "uuid-1", "status": "present" },
    { "student_id": "uuid-2", "status": "absent" }
  ]
}
```

---

## 4. Student Area (Akses Siswa)
Area interaktif murid untuk berpartisipasi pasif pada KBM.
**Middleware Target:** `['auth:sanctum', 'role:student']`

| Method | Endpoint | Fungsi |
| :--- | :--- | :--- |
| `GET` | `/api/lms/student/schedules` | Melihat relasi gabungan kelas & harinya sendiri. |
| `GET` | `/api/lms/student/materials` | Menarik index total rangkuman Materi Pembelajaran. |
| `GET` | `/api/lms/student/materials/{id}`| Membaca URL Download dari file materi. |
| `GET` | `/api/lms/student/attendances` | Menelusuri History Recap/Riwayat Kehadiran pribadi. |

---

## 5. WebSockets Channel (Real-time Broadcast)
Integrasi **Laravel Reverb**.

| Tipe Channel | Nama Channel | Fungsi Auth Middleware | Event Terkait |
| :--- | :--- | :--- | :--- |
| `Private` | `classroom.{classroom_id}` | Mengecek eksistensi `$user->student` di dalam tabel spesifik *classroom_students*. | `MaterialUploaded` |

#### Data *Payload* yang dimuntahkan Channel `MaterialUploaded`:
```json
{
   "title": "Materi Aljabar Lanjut",
   "subject_name": "Matematika",
   "classroom_name": "12 PPL 1",
   "file_link": "http://localhost:8000/storage/materials/xYa...pdf"
}
```
