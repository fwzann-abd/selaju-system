# Fitur Generasi (Generations)

## 📋 Deskripsi
Fitur Generasi memungkinkan admin untuk mengelola data generasi peserta (seperti Generasi Z, Millennial, dll). Setiap peserta yang mendaftar akan otomatis ditetapkan ke generasi yang sedang aktif.

## 🏗️ Struktur Database

### Tabel: `generations`
```sql
- id (UUID, Primary Key)
- name (String) - Nama generasi (Contoh: "Generasi Z")
- start_years (Integer) - Tahun mulai generasi (Contoh: 1997)
- end_years (Integer) - Tahun akhir generasi (Contoh: 2012)
- is_active (Boolean) - Status aktif generasi (default: false)
- created_at (Timestamp)
- updated_at (Timestamp)
```

### Tabel: `participants` (Modified)
Ditambahkan kolom:
- `generation_id` (UUID, NOT NULL) - Foreign key ke `generations.id`
  - Ditempatkan setelah kolom `school_id`
  - Constraint: ON DELETE CASCADE

## 🚀 Fitur Utama

### 1. List Generasi
- **URL:** `/admin/generations`
- **Fitur:**
  - Menampilkan daftar semua generasi
  - Search by nama generasi
  - Pagination (12 item per halaman)
  - Indikator status (Aktif/Nonaktif)
  - Edit dan Delete action

### 2. Tambah Generasi
- **URL:** `/admin/generations/create`
- **Form Fields:**
  - Nama Generasi (required)
  - Tahun Mulai (required)
  - Tahun Akhir (required)
  - Jadikan Generasi Aktif (checkbox)
- **Fitur:**
  - Jika checkbox aktif dicentang, otomatis deactivate generasi lainnya
  - Validasi input (nama max 255 char, tahun >= 1900)

### 3. Edit Generasi
- **URL:** `/admin/generations/{generation}/edit`
- **Fitur:**
  - Pre-populate data generasi
  - Same form fields sebagai create
  - Jika mengubah ke aktif, otomatis deactivate generasi lainnya
  - Validasi sama seperti create

### 4. Hapus Generasi
- **URL:** DELETE `/admin/generations/{generation}`
- **Fitur:**
  - Confirmation dialog sebelum delete
  - Cascade delete ke participants (jika diperlukan di masa depan)

### 5. Toggle Status Generasi (NEW)
- **URL:** PATCH `/admin/generations/{generation}/toggle-active`
- **Fitur:**
  - Click tombol status (Aktif/Nonaktif) untuk toggle
  - Jika generasi nonaktif di-click → menjadi Aktif (dan deactivate lainnya)
  - Jika generasi aktif di-click → menjadi Nonaktif
  - Tidak perlu ke halaman edit, bisa langsung dari list view
  - Visual feedback: button berubah warna saat di-hover

## 🔄 Auto-Assignment Generasi

Ketika peserta baru mendaftar, mereka akan otomatis ditetapkan ke generasi yang sedang `is_active = true`.

**Proses:**
1. Admin membuat generasi baru (mis. "Generasi 2024")
2. Admin men-check checkbox "Jadikan Generasi Aktif" saat create/edit
3. Semua generasi lain otomatis menjadi nonaktif
4. Ketika peserta baru mendaftar, mereka otomatis masuk ke generasi aktif ini

## 💾 Migrations

### Applied Migrations:
1. **2025_11_17_020946_create_generations_table**
   - Membuat tabel generations dengan schema lengkap

2. **2025_11_17_021003_add_generation_id_to_participants_table**
   - Menambah kolom generation_id ke participants
   - Membuat foreign key constraint

3. **2025_11_17_022526_populate_generation_id_for_existing_participants**
   - Mengisi generation_id untuk existing participants
   - Membuat default generation "Generasi Saat Ini" jika tidak ada
   - Mark default generation sebagai aktif

4. **2025_11_17_022550_make_generation_id_not_nullable_on_participants**
   - Mengubah generation_id menjadi NOT NULL

## 🎨 UI/UX

### List View
- Tabel dengan 6 kolom: Nama | Tahun Mulai | Tahun Akhir | Status | Diperbarui | Aksi
- Status Aktif: Green badge dengan checkmark
- Status Nonaktif: Gray badge dengan dot
- Edit & Delete buttons dengan icon

### Form Views
- Grid layout 2 column untuk input fields
- Checkbox dengan label penjelasan untuk is_active
- Button "Simpan/Perbarui Generasi" dan "Batal"
- Error messages validation

## 📝 Model

### Generation.php
```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

protected $fillable = ['name', 'start_years', 'end_years', 'is_active'];
protected $casts = [
    'start_years' => 'integer',
    'end_years' => 'integer',
    'is_active' => 'boolean',
];
```

## 🎯 Integration Points

### Controller Logic
- `store()`: Ketika is_active = true, deactivate semua generasi lain
- `update()`: Ketika is_active = true, deactivate semua generasi lain
- `index()`: Sort by start_years DESC (terbaru dulu)

### Registration Flow (Future)
- Saat peserta mendaftar: `$participant->generation_id = Generation::where('is_active', true)->first()->id`

## 🎯 Routes (RESTful)
```
GET    /admin/generations              → List
GET    /admin/generations/create       → Create form
POST   /admin/generations              → Store
GET    /admin/generations/{id}/edit    → Edit form
PUT    /admin/generations/{id}         → Update
DELETE /admin/generations/{id}         → Delete
PATCH  /admin/generations/{id}/toggle-active → Toggle active status (NEW)
```

## ✅ Testing Checklist
- [x] Migrations applied successfully
- [x] Routes registered
- [x] Model created with HasUuids
- [x] Controller logic for active status
- [x] Views created (index, create, edit)
- [x] Pagination working
- [x] Search functionality
- [x] Validation messages
- [ ] Integration dengan registration form
- [ ] API endpoint (optional)

## 🔄 Future Enhancements
- [ ] Add API endpoint untuk fetch active generation
- [ ] Add generation selection di registration form
- [ ] Dashboard stats untuk count participants per generation
- [ ] Bulk actions (activate, deactivate multiple)
- [ ] Generation history/timeline view
