# Entity Relationship Diagram (ERD) - Selaju System

> Dokumentasi database schema untuk Selaju System berdasarkan file migrasi Laravel.
> 
> Last updated: November 30, 2025

---

## 📋 Table of Contents

1. [User Management](#1-user-management)
2. [Participant & School](#2-participant--school)
3. [Perpossagar (Library)](#3-perpossagar-library)
4. [Sejajan (E-commerce)](#4-sejajan-e-commerce)
5. [Content Management](#5-content-management)
6. [System Tables](#6-system-tables)

---

## 1. User Management

### `users`
Admin user accounts untuk backend system.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Primary key |
| name | varchar(255) | NOT NULL | Full name |
| email | varchar(255) | UNIQUE, NOT NULL | Email address |
| email_verified_at | timestamp | NULLABLE | Email verification timestamp |
| password | varchar(255) | NOT NULL | Hashed password |
| remember_token | varchar(100) | NULLABLE | Remember me token |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `user_groups`
Role groups untuk user admin.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| name | varchar(255) | NOT NULL | Group name |
| status | boolean | DEFAULT TRUE | Active status |
| created_by | uuid | NULLABLE | Creator user ID |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |
| updated_by | uuid | NULLABLE | Last updater user ID |

### `password_reset_tokens`
Token untuk reset password.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| email | varchar(255) | PK | User email |
| token | varchar(255) | NOT NULL | Reset token |
| created_at | timestamp | NULLABLE | Token creation timestamp |

### `sessions`
Session management.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | varchar(255) | PK | Session ID |
| user_id | bigint | NULLABLE, INDEX, FK | Foreign key to users |
| ip_address | varchar(45) | NULLABLE | IP address |
| user_agent | text | NULLABLE | Browser user agent |
| payload | longtext | NOT NULL | Session data |
| last_activity | integer | INDEX | Last activity timestamp |

---

## 2. Participant & School

### `participants`
User accounts untuk participant (siswa/umum).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| uuid | uuid | PK | Primary key (uses 'id' column name in some migrations) |
| nomor_participant | varchar(255) | UNIQUE, NULLABLE | Participant number |
| school_id | uuid | NULLABLE, FK | Foreign key to schools |
| generation_id | uuid | NOT NULL, FK | Foreign key to generations |
| username | varchar(255) | UNIQUE, NOT NULL | Username |
| name | varchar(255) | NOT NULL | Full name |
| birth_date | date | NULLABLE | Date of birth |
| no_telp | varchar(255) | NULLABLE | Phone number |
| email | varchar(255) | UNIQUE, NOT NULL | Email address |
| email_verified_at | timestamp | NULLABLE | Email verification timestamp |
| photo | varchar(255) | NULLABLE | Profile photo path |
| password | varchar(255) | NOT NULL | Hashed password |
| is_active | boolean | DEFAULT TRUE | Active status |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Foreign Keys:**
- `school_id` → `schools(id)` ON DELETE SET NULL
- `generation_id` → `generations(id)` ON DELETE CASCADE

### `schools`
Data sekolah/institusi.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| slug | varchar(255) | UNIQUE, NOT NULL | URL-friendly identifier |
| name | varchar(255) | NOT NULL | School name |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `generations`
Data angkatan/generasi participant.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| name | varchar(255) | NOT NULL | Generation name |
| start_years | integer | NOT NULL | Start year |
| end_years | integer | NOT NULL | End year |
| is_active | boolean | DEFAULT FALSE | Active status |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `personal_access_tokens`
Sanctum tokens untuk API authentication.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Primary key |
| tokenable_type | varchar(255) | NOT NULL | Polymorphic type |
| tokenable_id | varchar(255) | NOT NULL, INDEX | Polymorphic ID (supports UUID) |
| name | varchar(255) | NOT NULL | Token name |
| token | varchar(64) | UNIQUE, NOT NULL | Hashed token |
| abilities | text | NULLABLE | Token abilities/scopes |
| last_used_at | timestamp | NULLABLE | Last usage timestamp |
| expires_at | timestamp | NULLABLE | Expiration timestamp |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

---

## 3. Perpossagar (Library)

### `books_category`
Kategori buku.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| uuid | uuid | PK | Primary key |
| name | varchar(255) | NOT NULL | Category name |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `authors`
Author/penulis buku (dari participant).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| uuid | uuid | PK | Primary key |
| participant_id | uuid | NOT NULL, FK | Foreign key to participants |
| author_at | timestamp | NULLABLE | Author registration timestamp |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Foreign Keys:**
- `participant_id` → `participants(id)` ON DELETE CASCADE

### `books`
Data buku.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| uuid | uuid | PK | Primary key |
| author_id | uuid | NOT NULL, FK | Foreign key to authors |
| title | varchar(255) | NOT NULL | Book title |
| slug | varchar(255) | UNIQUE, NOT NULL | URL-friendly identifier |
| subtitle | varchar(255) | NULLABLE | Book subtitle |
| desc | text | NULLABLE | Book description |
| language | varchar(255) | NULLABLE | Book language |
| photo | varchar(255) | NULLABLE | Cover image path |
| color_hex | varchar(10) | NULLABLE | Theme color for book card |
| filename | varchar(255) | NULLABLE | PDF/ebook file path |
| is_approved | boolean | DEFAULT FALSE | Approval status |
| published_at | timestamp | NULLABLE | Publication timestamp |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Foreign Keys:**
- `author_id` → `authors(uuid)` ON DELETE CASCADE

### `books_categories_pivots`
Many-to-many relationship antara books dan categories.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| uuid | uuid | PK | Primary key |
| category_id | uuid | NOT NULL, FK | Foreign key to books_category |
| book_id | uuid | NOT NULL, FK | Foreign key to books |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Foreign Keys:**
- `category_id` → `books_category(uuid)` ON DELETE CASCADE
- `book_id` → `books(uuid)` ON DELETE CASCADE

---

## 4. Sejajan (E-commerce)

### `sejajans`
Toko/warung online milik participant.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| participant_id | uuid | NOT NULL, INDEX, FK | Foreign key to participants |
| name | varchar(255) | NOT NULL | Shop name |
| slug | varchar(255) | UNIQUE, NOT NULL | URL-friendly identifier |
| description | text | NULLABLE | Shop description |
| photo | varchar(255) | NULLABLE | Shop logo/photo path |
| is_active | boolean | DEFAULT TRUE | Active status |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Foreign Keys:**
- `participant_id` → `participants(id)` ON DELETE CASCADE

### `sejajan_categories`
Kategori produk per toko.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| sejajan_id | uuid | NOT NULL, INDEX, FK | Foreign key to sejajans |
| name | varchar(255) | NOT NULL | Category name |
| slug | varchar(255) | NOT NULL | URL-friendly identifier |
| description | text | NULLABLE | Category description |
| order | integer | DEFAULT 0 | Display order |
| is_active | boolean | DEFAULT TRUE | Active status |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Unique Constraint:** `(sejajan_id, slug)`

**Foreign Keys:**
- `sejajan_id` → `sejajans(id)` ON DELETE CASCADE

### `sejajan_products`
Produk yang dijual di toko.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| sejajan_id | uuid | NOT NULL, INDEX, FK | Foreign key to sejajans |
| category_id | uuid | NULLABLE, FK | Foreign key to sejajan_categories |
| name | varchar(255) | NOT NULL | Product name |
| slug | varchar(255) | NOT NULL | URL-friendly identifier |
| description | text | NULLABLE | Product description |
| price | decimal(12,2) | NOT NULL | Product price |
| stock | integer | DEFAULT 0 | Available stock |
| photo | varchar(255) | NULLABLE | Product photo path |
| is_active | boolean | DEFAULT TRUE | Active status |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Unique Constraint:** `(sejajan_id, slug)`

**Foreign Keys:**
- `sejajan_id` → `sejajans(id)` ON DELETE CASCADE
- `category_id` → `sejajan_categories(id)` ON DELETE SET NULL

### `sejajan_cart_items`
Shopping cart items.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| participant_id | uuid | NOT NULL, INDEX, FK | Foreign key to participants |
| sejajan_id | uuid | NOT NULL, INDEX, FK | Foreign key to sejajans |
| sejajan_product_id | uuid | NOT NULL, INDEX, FK | Foreign key to sejajan_products |
| qty | integer | DEFAULT 1 | Quantity |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Unique Constraint:** `(participant_id, sejajan_product_id)`

**Foreign Keys:**
- `participant_id` → `participants(id)` ON DELETE CASCADE
- `sejajan_id` → `sejajans(id)` ON DELETE CASCADE
- `sejajan_product_id` → `sejajan_products(id)` ON DELETE CASCADE

### `sejajan_orders`
Order/pesanan dari pembeli.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| sejajan_id | uuid | NOT NULL, INDEX, FK | Foreign key to sejajans |
| participant_id | uuid | NOT NULL, INDEX, FK | Foreign key to participants (buyer) |
| status | enum | DEFAULT 'pending' | Order status |
| total_price | decimal(12,2) | DEFAULT 0 | Total order price |
| notes | text | NULLABLE | Order notes |
| pickup_time | timestamp | NULLABLE | Pickup schedule |
| location_pickup | varchar(255) | NULLABLE | Pickup location |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Status Values:** `pending`, `paid`, `processing`, `ready`, `completed`, `cancelled`

**Foreign Keys:**
- `sejajan_id` → `sejajans(id)` ON DELETE CASCADE
- `participant_id` → `participants(id)` ON DELETE CASCADE

### `sejajan_order_items`
Items dalam order.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| sejajan_order_id | uuid | NOT NULL, INDEX, FK | Foreign key to sejajan_orders |
| sejajan_product_id | uuid | NOT NULL, INDEX, FK | Foreign key to sejajan_products |
| qty | integer | DEFAULT 1 | Quantity ordered |
| price | decimal(12,2) | NOT NULL | Price per unit at order time |
| subtotal | decimal(12,2) | NOT NULL | qty × price |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

**Foreign Keys:**
- `sejajan_order_id` → `sejajan_orders(id)` ON DELETE CASCADE
- `sejajan_product_id` → `sejajan_products(id)` ON DELETE CASCADE

---

## 5. Content Management

### `menus`
Menu navigation system.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| code | varchar(255) | NULLABLE | Menu code |
| name | varchar(255) | NOT NULL | Menu name |
| icon | varchar(255) | NULLABLE | Icon class/path |
| row_order | integer | NULLABLE | Display order |
| status | boolean | DEFAULT TRUE | Active status |
| created_by | uuid | NULLABLE | Creator user ID |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |
| updated_by | uuid | NULLABLE | Last updater user ID |
| deleted_at | timestamp | NULLABLE | Soft delete timestamp |
| deleted_by | uuid | NULLABLE | Deleter user ID |

### `modules`
System modules/features.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| menu_id | uuid | NULLABLE, FK | Foreign key to menus |
| identifiers | varchar(255) | UNIQUE, NOT NULL | Module identifier |
| name | varchar(255) | NOT NULL | Module name |
| url | varchar(255) | NULLABLE | Module URL |
| icon | varchar(255) | NULLABLE | Icon class/path |
| row_order | integer | NULLABLE | Display order |
| is_active | boolean | DEFAULT TRUE | Active status |
| created_by | uuid | NULLABLE | Creator user ID |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |
| updated_by | uuid | NULLABLE | Last updater user ID |

**Foreign Keys:**
- `menu_id` → `menus(id)` ON DELETE SET NULL

### `module_accesses`
Permission control untuk module (belum dibaca migrasinya, tapi ada di list).

### `user_group_permissions`
Permission mapping untuk user groups (belum dibaca migrasinya, tapi ada di list).

### `article_categories`
Kategori artikel.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| name | varchar(255) | NOT NULL | Category name |
| slug | varchar(255) | UNIQUE, NOT NULL | URL-friendly identifier |
| description | text | NULLABLE | Category description |
| status | boolean | DEFAULT TRUE | Active status |
| row_order | integer | NULLABLE | Display order |
| created_by | uuid | NULLABLE | Creator user ID |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |
| updated_by | uuid | NULLABLE | Last updater user ID |
| deleted_at | timestamp | NULLABLE | Soft delete timestamp |
| deleted_by | uuid | NULLABLE | Deleter user ID |

### `articles`
Artikel/blog posts.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| category_id | uuid | NOT NULL, FK | Foreign key to article_categories |
| title | varchar(255) | NOT NULL | Article title |
| slug | varchar(255) | UNIQUE, NOT NULL | URL-friendly identifier |
| content | text | NOT NULL | Article content |
| excerpt | text | NULLABLE | Short excerpt |
| featured_image | varchar(255) | NULLABLE | Featured image path |
| status | boolean | DEFAULT TRUE | Published status |
| created_by | uuid | NULLABLE | Creator user ID |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |
| updated_by | uuid | NULLABLE | Last updater user ID |
| deleted_at | timestamp | NULLABLE | Soft delete timestamp |
| deleted_by | uuid | NULLABLE | Deleter user ID |

**Foreign Keys:**
- `category_id` → `article_categories(id)` ON DELETE CASCADE

---

## 6. System Tables

### `cache`
Laravel cache storage.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| key | varchar(255) | PK | Cache key |
| value | mediumtext | NOT NULL | Cached value |
| expiration | integer | NOT NULL | Expiration timestamp |

### `cache_locks`
Cache lock mechanism.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| key | varchar(255) | PK | Lock key |
| owner | varchar(255) | NOT NULL | Lock owner |
| expiration | integer | NOT NULL | Expiration timestamp |

### `jobs`
Queue jobs.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Primary key |
| queue | varchar(255) | INDEX | Queue name |
| payload | longtext | NOT NULL | Job payload |
| attempts | tinyint | NOT NULL | Attempt count |
| reserved_at | integer | NULLABLE | Reserved timestamp |
| available_at | integer | NOT NULL | Available timestamp |
| created_at | integer | NOT NULL | Creation timestamp |

### `job_batches`
Batch job tracking.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | varchar(255) | PK | Batch ID |
| name | varchar(255) | NOT NULL | Batch name |
| total_jobs | integer | NOT NULL | Total jobs |
| pending_jobs | integer | NOT NULL | Pending jobs |
| failed_jobs | integer | NOT NULL | Failed jobs |
| failed_job_ids | longtext | NOT NULL | Failed job IDs |
| options | mediumtext | NULLABLE | Batch options |
| cancelled_at | integer | NULLABLE | Cancellation timestamp |
| created_at | integer | NOT NULL | Creation timestamp |
| finished_at | integer | NULLABLE | Finish timestamp |

### `failed_jobs`
Failed queue jobs.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint | PK, AUTO_INCREMENT | Primary key |
| uuid | varchar(255) | UNIQUE | Job UUID |
| connection | text | NOT NULL | Queue connection |
| queue | text | NOT NULL | Queue name |
| payload | longtext | NOT NULL | Job payload |
| exception | longtext | NOT NULL | Exception details |
| failed_at | timestamp | NOT NULL | Failure timestamp |

---

## 📊 Entity Relationships Summary

### Core User Flow
```
users (admin)
  ↓
user_groups → user_group_permissions
  ↓
module_accesses ← modules ← menus
```

### Participant Flow
```
schools ←┐
         │
participants → generations
    ↓
    ├→ authors → books → books_categories_pivots → books_category
    │
    ├→ sejajans → sejajan_products → sejajan_categories
    │       ↓              ↓
    │       └──────────────┴→ sejajan_orders → sejajan_order_items
    │
    └→ sejajan_cart_items
```

### Content Management
```
article_categories → articles
menus → modules → module_accesses
```

---

## 🔑 Key Points

1. **UUID Usage**: Sebagian besar tabel menggunakan UUID sebagai primary key untuk better scalability dan security.

2. **Soft Deletes**: Tables seperti `menus`, `articles`, dan `article_categories` menggunakan soft deletes (`deleted_at`).

3. **Audit Trail**: Banyak tabel memiliki `created_by`, `updated_by`, `deleted_by` untuk tracking.

4. **Polymorphic Tokens**: `personal_access_tokens` menggunakan polymorphic relationship dengan `tokenable_id` sebagai varchar untuk support UUID.

5. **Multi-tenancy Pattern**: 
   - **Sejajan**: Setiap participant bisa punya toko sendiri
   - **Perpossagar**: Setiap participant bisa jadi author

6. **Cascade Deletes**: Relasi parent-child menggunakan cascade delete untuk data consistency.

---

## 📝 Migration Timeline

- **2024-11-12**: Initial users & base system
- **2024-11-20**: User groups, permissions, modules, articles
- **2025-11-13**: Schools & participants
- **2025-11-14**: Sejajan e-commerce system
- **2025-11-17**: Generations system
- **2025-11-23**: Perpossagar library system
- **2025-11-24**: Sejajan categories
- **2025-11-29**: Cart system & pickup location

---

## 🚀 Usage Examples

### Query Participant dengan Toko
```sql
SELECT p.name, s.name as shop_name
FROM participants p
LEFT JOIN sejajans s ON p.id = s.participant_id
WHERE p.is_active = TRUE;
```

### Query Buku dengan Author
```sql
SELECT b.title, p.name as author_name, bc.name as category
FROM books b
JOIN authors a ON b.author_id = a.uuid
JOIN participants p ON a.participant_id = p.id
JOIN books_categories_pivots bcp ON b.uuid = bcp.book_id
JOIN books_category bc ON bcp.category_id = bc.uuid
WHERE b.is_approved = TRUE;
```

### Query Orders dengan Items
```sql
SELECT 
    o.id as order_id,
    p.name as buyer_name,
    s.name as shop_name,
    o.total_price,
    o.status
FROM sejajan_orders o
JOIN participants p ON o.participant_id = p.id
JOIN sejajans s ON o.sejajan_id = s.id
WHERE o.status != 'cancelled';
```

---

**Generated from:** `/database/migrations/`  
**Framework:** Laravel 11  
**Database Engine:** MySQL 8.0+

