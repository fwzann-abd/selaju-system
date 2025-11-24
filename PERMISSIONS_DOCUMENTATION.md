# Dokumentasi Sistem Menu, Module, dan Permissions

## 📋 Ringkasan

Sistem menu dan permissions telah berhasil diimplementasikan dengan struktur berikut:
- **Menus**: Menu utama untuk sidebar
- **Modules**: Sub-menu dan halaman yang terkait dengan menu
- **Module Accesses**: Akses spesifik (view, create, edit, delete) untuk setiap module
- **User Groups**: Grup pengguna (Super Admin, Admin, Editor)
- **User Group Permissions**: Mapping permissions untuk setiap user group

---

## 🗄️ Struktur Database

### Tables

1. **menus**
   - Menus utama untuk sidebar aplikasi
   - Fields: id, code, name, icon, row_order, status
   - Relasi: hasMany modules

2. **modules**
   - Sub-menu dan halaman yang terkait dengan menu
   - Fields: id, menu_id, identifiers, name, url, icon, row_order, is_active
   - Relasi: belongsTo menu, hasMany moduleAccess

3. **modules_access** (table: `modules_access`)
   - Akses spesifik untuk setiap module
   - Fields: id, module_id, type, identifiers, name
   - Relasi: belongsTo module

4. **user_groups**
   - Grup pengguna untuk role-based access control
   - Fields: id, name, status
   - Relasi: hasMany permissions, hasMany users

5. **user_group_permissions**
   - Mapping permissions untuk setiap user group
   - Fields: id, user_group_id, module_access_id, status
   - Relasi: belongsTo userGroup, belongsTo moduleAccess

6. **users** (modified)
   - Tambahan kolom: user_group_id (nullable, uuid)
   - Relasi: belongsTo userGroup

---

## 📊 Data Seeded

### Menus (3 items)
1. **Dashboard** - Menu untuk dashboard dan analytics
2. **Konten** - Menu untuk artikel dan kategori artikel
3. **Pengaturan** - Menu untuk pengaturan sistem

### Modules (7 items)
 Dashboard Utama (`/admin`)
 Analytics (`/admin/analytics`)
 Artikel (`/admin/articles`)
 Kategori Artikel (`/admin/article-categories`)
 Manajemen Pengguna (`/admin/users`)
 Role & Permission (`/admin/roles`)
 Manajemen Menu (`/admin/menus`)

### User Groups (3 items)
1. **Super Admin**
   - Akses PENUH ke semua module dan action

2. **Admin**
   - Akses ke semua module KECUALI:
     - Menu Management
     - Role & Permission

3. **Editor**
   - Akses hanya ke:
     - Artikel (view, create, edit, delete)

### Users (1 default user)
- **Email**: dev@gncs.dev
- **Password**: programmer123
- **Group**: Super Admin

---

## 🔌 API Endpoints

### Menu API (Protected - requires authentication)

**GET /api/menus/sidebar**
- Mendapatkan menu dengan modules yang accessible untuk user yang authenticated
- Response:
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "code": "dashboard",
      "name": "Dashboard",
      "icon": "fa-chart-line",
      "row_order": 1,
      "status": true,
      "modules": [
        {
          "id": "uuid",
          "menu_id": "uuid",
          "identifiers": "dashboard-main",
          "name": "Dashboard Utama",
          "url": "/admin",
          "icon": "fa-home",
          "row_order": 1,
          "is_active": true
        }
      ]
    }
  ]
}
```

**GET /api/menus**
- Mendapatkan semua menus dengan modules

**GET /api/menus/{id}**
- Mendapatkan detail menu spesifik

---

## 🔐 Permission System

### Struktur Permission Check

Setiap module memiliki 4 akses dasar:
- **view**: Melihat halaman
- **create**: Membuat data baru
- **edit**: Mengubah data
- **delete**: Menghapus data

Identifiers format: `{module-identifiers}-{action}`
Contoh:
- `dashboard-main-view`
- `artikel-list-create`
- `user-management-edit`

### Flow Check Permission

1. User login → Fetch user group
2. Get user group permissions (module_access yang status=true)
3. Filter modules berdasarkan permission user
4. Return hanya modules yang user bisa akses

---

## 📝 Models

### User Model
```php
public function userGroup()
{
    return $this->belongsTo(UserGroup::class);
}
```

### UserGroup Model
```php
public function permissions()
{
    return $this->hasMany(UserGroupPermission::class);
}
```

### Menu Model
```php
public function modules()
{
    return $this->hasMany(Module::class);
}
```

### Module Model
```php
public function menu()
{
    return $this->belongsTo(Menu::class);
}
```

---

## 🚀 Implementasi Frontend

Untuk menggunakan sidebar di frontend (Nuxt/Vue), Anda bisa memanggil:

```javascript
// Fetch menus untuk user
const response = await fetch('/api/menus/sidebar', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
});
const { data } = await response.json();

// data berisi menus dengan modules yang sudah di-filter
// Loop menus dan modules untuk render sidebar
```

---

## 🔄 Migration & Seeding

### Jalankan Fresh Migration dengan Seed
```bash
php artisan migrate:fresh --seed
```

### Atau Jalankan Seeders Secara Individual
```bash
php artisan db:seed --class=MenuSeeder
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=UserGroupSeeder
```

---

## 📌 Catatan Penting

1. **Sidebar Dependency**: Sidebar akan hanya menampilkan menus yang memiliki minimal 1 module yang accessible untuk user
2. **Super Admin**: Memiliki akses penuh dan otomatis approved untuk semua module accesses
3. **Permission Status**: Hanya permissions dengan status=true yang dihitung sebagai akses valid
4. **Module Status**: Hanya modules dengan is_active=true yang ditampilkan

---

## ✅ Testing

### Test dengan cURL
```bash
# Login dan dapatkan token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"dev@gncs.dev","password":"programmer123"}'

# Get menus with authorization
curl -X GET http://localhost:8000/api/menus/sidebar \
  -H "Authorization: Bearer {token}"
```

---

Semua struktur dan seeding sudah selesai! ✨
