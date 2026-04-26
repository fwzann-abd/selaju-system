# Entity Relationship Diagram (ERD) - Selaju LMS System

> Dokumentasi database schema untuk Selaju LMS System.
> 
> Last updated: April 2026

---

## 📋 Table of Contents

1. [User Management](#1-user-management)
2. [Account & School](#2-account--school)
3. [LMS Tables](#3-lms-tables)
4. [System Tables](#4-system-tables)

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

### `user_group_permissions`
Permission mapping untuk user groups.

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

## 2. Account & School

### `accounts`
User accounts untuk peserta (siswa/guru).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| uuid | uuid | PK | Primary key |
| nomor_participant | varchar(255) | UNIQUE, NULLABLE | Participant number |
| username | varchar(255) | UNIQUE, NOT NULL | Username |
| birth_date | date | NULLABLE | Date of birth |
| no_telp | varchar(255) | NULLABLE | Phone number |
| email | varchar(255) | UNIQUE, NOT NULL | Email address |
| email_verified_at | timestamp | NULLABLE | Email verification timestamp |
| photo | varchar(255) | NULLABLE | Profile photo path |
| password | varchar(255) | NOT NULL | Hashed password |
| is_active | boolean | DEFAULT TRUE | Active status |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `schools`
Data sekolah/institusi.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| slug | varchar(255) | UNIQUE, NOT NULL | URL-friendly identifier |
| name | varchar(255) | NOT NULL | School name |
| account_id | uuid | NULLABLE, FK | Foreign key to accounts |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `generations`
Data angkatan/generasi.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| name | varchar(255) | NOT NULL | Generation name |
| start_years | integer | NOT NULL | Start year |
| end_years | integer | NOT NULL | End year |
| is_active | boolean | DEFAULT FALSE | Active status |
| is_current | boolean | DEFAULT FALSE | Current generation |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `students`
Data siswa.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| account_id | uuid | NULLABLE, FK | Foreign key to accounts |
| school_id | uuid | NULLABLE, FK | Foreign key to schools |
| generation_id | uuid | NULLABLE, FK | Foreign key to generations |
| name | varchar(255) | NOT NULL | Full name |
| student_number | varchar(255) | NULLABLE | Student number |
| national_id | varchar(255) | NULLABLE | National ID (NISN) |
| gender | varchar(255) | NULLABLE | Gender |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `teachers`
Data guru.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| account_id | uuid | NULLABLE, FK | Foreign key to accounts |
| school_id | uuid | NULLABLE, FK | Foreign key to schools |
| name | varchar(255) | NOT NULL | Full name |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `device_sessions`
Device session management per account.

### `personal_access_tokens`
Sanctum tokens untuk API authentication.

---

## 3. LMS Tables

### `classrooms`
Data kelas.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| teacher_id | uuid | NULLABLE, FK | Foreign key to teachers (wali kelas) |
| name | varchar(255) | NOT NULL | Class name |
| grade | varchar(255) | NULLABLE | Grade level |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `classroom_students`
Pivot table siswa-kelas.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| classroom_id | uuid | NOT NULL, FK | Foreign key to classrooms |
| student_id | uuid | NOT NULL, FK | Foreign key to students |
| student_position_id | uuid | NULLABLE, FK | Foreign key to student_positions |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `student_positions`
Jabatan siswa (ketua kelas, dll).

### `subjects`
Mata pelajaran.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| name | varchar(255) | NOT NULL | Subject name |
| type | varchar(255) | NULLABLE | Type (Umum/Jurusan) |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `rooms`
Ruangan kelas/lab.

### `schedules`
Jadwal KBM.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | uuid | PK | Primary key |
| classroom_id | uuid | NOT NULL, FK | Foreign key to classrooms |
| teacher_id | uuid | NOT NULL, FK | Foreign key to teachers |
| subject_id | uuid | NOT NULL, FK | Foreign key to subjects |
| room_id | uuid | NULLABLE, FK | Foreign key to rooms |
| day | varchar(255) | NOT NULL | Day of week |
| start_time | time | NOT NULL | Start time |
| end_time | time | NOT NULL | End time |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### `attendances`
Presensi siswa.

### `materials`
Materi pembelajaran.

### `course_materials`
Materi kursus dengan file upload.

---

## 4. System Tables

### `menus`
Menu navigation system.

### `modules`
System modules/features.

### `module_accesses`
Permission control untuk module.

### `cache` / `cache_locks`
Laravel cache storage.

### `jobs` / `job_batches` / `failed_jobs`
Queue system.

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

### LMS Flow
```
schools ←┐
         │
accounts → generations
    ↓
    ├→ students → classroom_students → classrooms
    │                                      ↓
    └→ teachers ──────────────────→ schedules
                                      ↓
                                  subjects
                                  rooms
                                  attendances
                                  materials
```

---

## 🔑 Key Points

1. **UUID Usage**: Sebagian besar tabel menggunakan UUID sebagai primary key.
2. **Audit Trail**: Banyak tabel memiliki `created_by`, `updated_by` untuk tracking.
3. **Polymorphic Tokens**: `personal_access_tokens` menggunakan polymorphic relationship.
4. **Cascade Deletes**: Relasi parent-child menggunakan cascade delete.

---

**Generated from:** `/database/migrations/`  
**Framework:** Laravel 12  
**Database Engine:** MySQL 8.0+
