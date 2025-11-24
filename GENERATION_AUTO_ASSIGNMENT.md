# Auto-Assignment Generasi ke Peserta Baru

## 📋 Deskripsi
Ketika peserta baru melakukan registrasi, mereka otomatis ditetapkan ke generasi yang sedang aktif (`is_active = true`). Tidak perlu manual input generation_id saat registrasi.

## ⚙️ Implementasi

### Backend - RegisterController
**File:** `app/Http/Controllers/Api/RegisterController.php`

**Logic:**
```php
// Get active generation
$activeGeneration = Generation::where('is_active', true)->first();

// Fallback: jika tidak ada generasi aktif, buat satu
if (!$activeGeneration) {
    $activeGeneration = Generation::create([
        'name' => 'Generasi Saat Ini',
        'start_years' => date('Y'),
        'end_years' => date('Y'),
        'is_active' => true,
    ]);
}

// Set generation_id saat create participant
$participant = Participant::create([
    // ... other fields ...
    'generation_id' => $activeGeneration->id,
]);
```

### Model - Participant
**File:** `app/Models/Participant.php`

**Perubahan:**
- Tambah `generation_id` ke array `$fillable` agar bisa di-set via mass assignment

```php
protected $fillable = [
    'id',
    'nomor_participant',
    'school_id',
    'generation_id',  // ← Added
    'username',
    'name',
    'birth_date',
    'no_telp',
    'email',
    'photo',
    'password',
    'is_active',
];
```

## 🔄 Flow Registrasi

```
1. Peserta Submit Form Registrasi
   ↓
2. RegisterController::register() dipanggil
   ↓
3. Validasi input (name, email, password, username, dll)
   ↓
4. Cari Generation dengan is_active = true
   ├─ Jika ditemukan → gunakan generation_id itu
   └─ Jika tidak ada → buat default "Generasi Saat Ini" + set active
   ↓
5. Create Participant dengan generation_id otomatis
   ↓
6. Return response dengan participant data + token
```

## 📝 API Response
```json
{
    "message": "Participant registered successfully. Please select a school.",
    "participant": {
        "id": "019a8fc0-f66e-7200-8917-726024c13e3e",
        "name": "Test User",
        "email": "testuser2025@test.com",
        "username": "testuser2025",
        "generation_id": "19cbde56-0141-47e4-bf73-5e2d3c42058d",
        "is_active": true,
        "created_at": "2025-11-17T02:59:39.000000Z"
    },
    "token": "12|moaYpEwvGVkCtsRcazSKUO7NWfVWL5Dfyl2rZ9zg6d77643"
}
```

## ✅ Testing

### Test Case 1: Ada Active Generation
1. Pastikan ada 1 generation dengan `is_active = true` (misal "Angkatan 26")
2. POST ke `/api/register` dengan data peserta baru
3. ✓ Peserta ter-assign ke generation aktif

### Test Case 2: Tidak Ada Active Generation
1. Delete/deactivate semua generation
2. POST ke `/api/register`
3. ✓ Sistem auto-buat default generation "Generasi Saat Ini"
4. ✓ Peserta ter-assign ke generation yang baru dibuat

### Test Case 3: Multiple Generation Ada
1. Buat 3 generation, set hanya 1 sebagai active
2. Register peserta baru
3. ✓ Peserta hanya ter-assign ke yang active (bukan semua)

## 🎯 Integration Points

| Component | Perubahan | Status |
|-----------|-----------|--------|
| Tabel `participants` | Kolom `generation_id` NOT NULL | ✅ Sudah |
| Model `Participant` | Tambah `generation_id` ke fillable | ✅ Sudah |
| Model `Generation` | Tidak ada perubahan | ✅ Sudah ada |
| RegisterController | Logic untuk set generation_id | ✅ Sudah |
| Migrations | Migration untuk generation dan FK | ✅ Sudah |

## 📌 Notes

- **Fallback Protection**: Jika tidak ada active generation saat register, sistem auto-create default generation
- **No Manual Input**: Peserta tidak perlu memilih generation saat registrasi
- **Admin Control**: Admin bisa mengubah active generation kapan saja di `/admin/generations`
- **Backward Compatible**: Existing registration flow tidak berubah (generation_id handled di backend)

## 🐛 Error Handling

Jika user mendapat error "Field 'generation_id' doesn't have a default value":
1. Pastikan migration sudah di-run dengan `php artisan migrate`
2. Pastikan ada minimal 1 generation di database
3. Pastikan RegisterController sudah di-update dengan logic di atas
4. Clear cache: `php artisan config:cache`

## 📊 Database Check

```sql
-- Check active generation
SELECT * FROM generations WHERE is_active = true;

-- Check participant dengan generation
SELECT id, name, email, generation_id FROM participants ORDER BY created_at DESC LIMIT 5;

-- Check FK constraint
SHOW CREATE TABLE participants\G
```

## 🚀 Future Enhancement

- [ ] Allow peserta memilih generation saat registrasi (optional, default ke active)
- [ ] API endpoint untuk fetch active generation
- [ ] Admin notification saat generation change
- [ ] Analytics: berapa peserta per generation
