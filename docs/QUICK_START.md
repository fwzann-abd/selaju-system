# Quick Start Guide - LMS Melesat

## 1️⃣ Clear Cache & Config ✅
```bash
cd c:\folder-v2
php artisan cache:clear
php artisan config:clear
```

## 2️⃣ Build Frontend ✅
```bash
npm run build
```

**Output Expected:**
```
✓ 62 modules transformed.
✓ built in 2.45s
```

## 3️⃣ Start Development Server 🚀
```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000**

## 4️⃣ Access Admin Dashboard
```
http://localhost:8000/login
```

Login dengan super_admin credentials Anda.

## 5️⃣ Navigate to LMS Melesat ✨
```
http://localhost:8000/admin/lms/classrooms
```

Atau gunakan sidebar:
- **Selaju Team** → **LMS Melesat** → **Daftar Kelas**

---

## ✅ What You Should See

### Sidebar
```
├─ Dashboard
├─ Pengguna
├─ Sejajan
├─ Perpossagar
├─ Selaju
├─ Webex
├─ Eplin
├─ Artikel
├─ Sekolah
├─ LMS Melesat  ← NEW!
│  ├─ Daftar Kelas
│  ├─ Data Guru
│  ├─ Data Siswa
│  └─ Jadwal KBM
└─ Pengaturan
```

### Daftar Kelas Page
```
┌─────────────────────────────────────────┐
│ Daftar Kelas                            │
├─────────────────────────────────────────┤
│ [Search Box]  [+ Tambah Kelas]          │
├─────────────────────────────────────────┤
│ No │ Nama Kelas │ Tingkat │ Jurusan │ Wali Kelas │ Aksi │
├────┼────────────┼─────────┼─────────┼────────────┼──────┤
│ 1  │ 12 IPA 1   │    12   │  IPA    │ Mr. Ahmed  │ ✏️ 🗑️ │
│ 2  │ 12 IPS 2   │    12   │  IPS    │ Ms. Sarah  │ ✏️ 🗑️ │
└────┴────────────┴─────────┴─────────┴────────────┴──────┘
```

---

## 🧪 Test API Directly

### Option 1: Using Postman
1. Import/create new request
2. Method: GET
3. URL: `http://localhost:8000/api/lms/classrooms`
4. Headers:
   ```
   Authorization: Bearer {your_sanctum_token}
   Accept: application/json
   ```
5. Send

### Option 2: Using curl
```bash
curl -X GET http://localhost:8000/api/lms/classrooms \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Expected Response
```json
{
  "data": [
    {
      "id": "uuid-here",
      "name": "Kelas 12 IPA 1",
      "tingkat": "12",
      "jurusan": "IPA",
      "rombel": "1",
      "slug": "kelas-12-ipa-1",
      "teacher_id": "uuid-here",
      "academic_year": "2025/2026",
      "created_at": "2026-04-07T10:00:00Z",
      "updated_at": "2026-04-07T10:00:00Z",
      "teacher": {
        "id": "uuid-here",
        "name": "Mr. Ahmed"
      }
    }
  ],
  "total": 1
}
```

---

## 🌙 Dark Mode
Click user avatar → Dark mode toggle works on all LMS pages

## 📱 Mobile Responsive
Test on mobile devices - sidebar collapses, table scrolls horizontally

## 🔔 Real-time Notifications
If Reverb is enabled, new materials uploaded by teachers will show toast notifications

---

## 🛑 Troubleshooting

### Server won't start?
```bash
# Kill existing process
lsof -ti:8000 | xargs kill -9

# Or use different port
php artisan serve --port=8001
```

### Styles not loading?
```bash
# Rebuild frontend
npm run build

# Or use dev mode
npm run dev
# (then visit http://localhost:5173 or use `npm run dev` as proxy)
```

### API returns 401 Unauthorized
- Check if you're logged in
- Verify Sanctum token in localStorage
- Check browser dev tools → Application → Cookies

### No classrooms showing?
- This is normal if no classrooms exist
- You can create via API or admin panel
- The page is working correctly!

---

## 📊 Database Queries

### Get all classrooms with teacher info
```sql
SELECT c.*, t.name as teacher_name 
FROM classrooms c 
LEFT JOIN teachers t ON c.teacher_id = t.id 
ORDER BY c.created_at DESC;
```

### Insert test classroom
```sql
INSERT INTO classrooms 
  (id, name, tingkat, jurusan, rombel, slug, teacher_id, academic_year)
VALUES 
  (UUID(), 'Kelas 12 IPA 1', '12', 'IPA', '1', 'kelas-12-ipa-1', '{teacher_uuid}', '2025/2026');
```

---

## 🎉 Success Checklist

- [ ] Server running on localhost:8000
- [ ] Can login to dashboard
- [ ] See "LMS Melesat" in sidebar
- [ ] Click and navigate to Daftar Kelas
- [ ] See search bar and table
- [ ] API endpoint `/api/lms/classrooms` responds
- [ ] Can delete/edit classrooms (buttons click)
- [ ] Dark mode toggle works
- [ ] Page is responsive on mobile

---

## 📚 Files Reference

| File | Purpose |
|------|---------|
| `resources/views/layout/sidebar.blade.php` | Menu integration |
| `resources/views/admin/lms/classrooms/index.blade.php` | Main page |
| `app/Http/Controllers/Api/ClassroomController.php` | API logic |
| `app/Http/Controllers/Admin/Lms/ClassroomController.php` | Page controller |
| `routes/web.php` | Web routes |
| `resources/js/lms-listener.js` | Real-time events |

---

## 🚀 You're All Set!

Everything is configured and ready. Just follow the 5 steps above and start using LMS Melesat!

If you have any questions, check the documentation in:
- `LMS_MELESAT_IMPLEMENTATION.md` - Full technical details
- `FINAL_STATUS.md` - Complete feature list
- `SETUP_VERIFICATION.md` - Verification checklist
