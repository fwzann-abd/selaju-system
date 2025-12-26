# Account & Device Session Management

## Overview
Perubahan dari `participants` menjadi `accounts` dengan penambahan device session management untuk kontrol login multi-device.

## Database Changes

### 1. Renamed Tables
- `participants` → `accounts`

### 2. Renamed Columns (Foreign Keys)
Di semua tabel berikut, `participant_id` telah diubah menjadi `account_id`:
- `sejajans`
- `sejajan_orders`
- `sejajan_cart_items`
- `perpossagar_authors`
- `students`

### 3. New Table: `device_sessions`
Table untuk tracking device yang login:

```sql
CREATE TABLE device_sessions (
    id UUID PRIMARY KEY,
    account_id UUID NOT NULL,
    device_id VARCHAR(255) UNIQUE NOT NULL,
    device_name VARCHAR(255),
    device_type VARCHAR(255), -- web, mobile, tablet
    browser VARCHAR(255),
    os VARCHAR(255),
    ip_address INET,
    user_agent TEXT,
    location VARCHAR(255),
    last_activity TIMESTAMP,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(uuid) ON DELETE CASCADE
);
```

### 4. Updated personal_access_tokens
`tokenable_type` diupdate dari `App\Models\Participant` ke `App\Models\Account`

## Model Changes

### New Models
1. **Account** (`app/Models/Account.php`)
   - Primary model untuk user accounts
   - Menggantikan Participant
   - Includes device session management methods

2. **DeviceSession** (`app/Models/DeviceSession.php`)
   - Model untuk device session tracking

### Updated Models
Models berikut telah diupdate untuk menggunakan `account_id` dan relationship ke `Account`:
- `Sejajan`
- `SejajanOrder`
- `SejajanCartItem`
- `PerpossagarAuthor`
- `Student`

### Backward Compatibility
**Participant** model masih tersedia sebagai alias/extends dari Account untuk backward compatibility:

```php
class Participant extends Account
{
    protected $table = 'accounts';
}
```

## Device Session Management

### Configuration
Lihat `config/auth.php`:

```php
'max_devices_per_account' => env('MAX_DEVICES_PER_ACCOUNT', 4),
```

Default: 1 account dapat login di 4 devices.

### Usage Examples

#### Check if account has reached max devices
```php
$account = Account::find($id);
if ($account->hasReachedMaxDevices()) {
    // Remove oldest session
    $account->removeOldestDeviceSession();
}
```

#### Create new device session
```php
$deviceSession = $account->deviceSessions()->create([
    'device_id' => 'unique-device-identifier',
    'device_name' => 'Chrome on Windows',
    'device_type' => 'web',
    'browser' => 'Chrome 120',
    'os' => 'Windows 10',
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'location' => 'Jakarta, Indonesia',
    'last_activity' => now(),
    'is_active' => true,
]);
```

#### Get active device sessions
```php
$activeSessions = $account->activeDeviceSessions;
```

#### Update last activity
```php
$deviceSession->touch();
```

## Controller Updates

### Updated Validations
Semua validasi yang menggunakan `participants` table telah diupdate ke `accounts`:

- `ParticipantController::store()` - unique validation
- `ParticipantController::update()` - unique validation
- `RegisterController::register()` - unique validation
- `api.php` routes - `/me` endpoint validation

### Updated Column References
- `SejajanProductController` - menggunakan `account_id`
- `SejajanController` - menggunakan `account` relationship
- `RegisterController` - update student `account_id`

## Migration Files

1. `2025_12_26_075154_rename_participants_table_to_accounts.php`
   - Rename table tanpa kehilangan data

2. `2025_12_26_075204_rename_participant_id_to_account_id_in_all_tables.php`
   - Rename foreign key columns
   - Update foreign key constraints
   - Update personal_access_tokens tokenable_type

3. `2025_12_26_075206_create_device_sessions_table.php`
   - Create device_sessions table dengan indexes

## Testing

### Verify Data Migration
```bash
php artisan tinker
```

```php
// Check accounts table
\App\Models\Account::count();

// Check foreign keys updated
$sejajan = \App\Models\Sejajan::first();
$sejajan->account_id; // Should show UUID

// Check relationships work
$account = \App\Models\Account::first();
$account->school->name;
$account->sejajans;

// Check backward compatibility
$participant = \App\Models\Participant::first();
$participant->name; // Should work

// Test device sessions
$session = $account->deviceSessions()->create([
    'device_id' => 'test-001',
    'device_name' => 'Test Device',
    'device_type' => 'web',
    'is_active' => true,
]);

$account->activeDeviceSessions()->count();
```

## Rollback

Jika perlu rollback:

```bash
php artisan migrate:rollback --step=3
```

Ini akan:
1. Drop device_sessions table
2. Rename account_id kembali ke participant_id
3. Rename accounts table kembali ke participants

## Important Notes

⚠️ **Data Safety**: Semua data existing tetap aman karena menggunakan `RENAME` dan `UPDATE`, bukan `DROP` dan `CREATE`.

✅ **Backward Compatibility**: Participant model masih bisa digunakan untuk menghindari breaking changes pada code yang belum diupdate.

🔐 **Security**: Device sessions bisa digunakan untuk:
- Limit jumlah device per account
- Track device yang sedang login
- Logout device tertentu
- Monitoring suspicious login activity

## Next Steps (Optional)

1. **Implement Device Session pada Login**
   - Auto create device session saat login
   - Check max devices limit
   - Auto logout oldest device jika sudah max

2. **Device Management UI**
   - Tampilan daftar devices yang login
   - Logout device tertentu
   - Rename device

3. **Notification**
   - Email notification saat login dari device baru
   - Alert jika ada suspicious activity

4. **Analytics**
   - Track device types (web, mobile, tablet)
   - Track browser/OS distribution
   - Track location-based access
