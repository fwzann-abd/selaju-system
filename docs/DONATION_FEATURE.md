# Donation Feature Documentation

## Overview
Fitur donasi untuk aplikasi Selaju dengan integrasi payment gateway DOKU yang mendukung berbagai metode pembayaran.

## Database Schema

### Table: `donations`
| Column | Type | Description |
|--------|------|-------------|
| id | UUID | Primary key |
| user_id | UUID (nullable) | Foreign key ke users |
| donor_name | String | Nama donatur |
| donor_ig | String (nullable) | Instagram handle donatur |
| amount | Decimal(15,2) | Jumlah donasi |
| message | Text (nullable) | Pesan dari donatur |
| payment_method | String | Metode: qris, virtual_account, manual_transfer |
| payment_status | String | Status: pending, paid, failed, expired |
| transaction_id | String (nullable) | ID transaksi dari DOKU |
| payment_data | JSON (nullable) | Data pembayaran dari DOKU |
| paid_at | Timestamp (nullable) | Waktu pembayaran berhasil |
| created_at, updated_at | Timestamp | Laravel timestamps |
| deleted_at | Timestamp (nullable) | Soft delete |

### Table: `manual_transfers`
| Column | Type | Description |
|--------|------|-------------|
| id | UUID | Primary key |
| donation_id | UUID | Foreign key ke donations |
| bank_name | String | Nama bank |
| account_number | String | Nomor rekening |
| account_holder_name | String | Nama pemilik rekening |
| transfer_amount | Decimal(15,2) | Jumlah transfer |
| transfer_proof | String (nullable) | Path ke bukti transfer |
| transfer_date | Timestamp (nullable) | Tanggal transfer |
| verification_status | String | Status: pending, verified, rejected |
| verification_notes | Text (nullable) | Catatan verifikasi |
| verified_by | UUID (nullable) | Foreign key ke users |
| verified_at | Timestamp (nullable) | Waktu verifikasi |
| created_at, updated_at | Timestamp | Laravel timestamps |

## API Endpoints

### Public Endpoints

#### GET `/api/donations`
Menampilkan leaderboard donatur dan statistik.
- **Query Parameters:**
  - `limit` (optional): Jumlah data (default: 15)
- **Response:**
```json
{
  "data": {
    "donations": [
      {
        "id": "uuid",
        "donor_name": "Nama Donatur",
        "amount": "50000.00",
        "message": "Semangat terus!",
        "paid_at": "2026-01-07T10:30:00.000000Z"
      }
    ],
    "statistics": {
      "total_amount": "1500000.00",
      "total_donors": 15
    }
  }
}
```

#### POST `/api/donations`
Membuat donasi baru.
- **Request Body:**
```json
{
  "amount": 50000,
  "donor_name": "Agung Gunawan",
  "donor_ig": "agung4202",
  "message": "Semangat untuk tim SELAJU!",
  "payment_method": "qris" // qris | virtual_account | manual_transfer
}
```
- **Response:**
```json
{
  "message": "Donasi berhasil dibuat",
  "data": {
    "donation": {
      "id": "uuid",
      "amount": "50000.00",
      "payment_method": "qris",
      "payment_status": "pending"
    },
    "payment_data": {
      "transaction_id": "TRX-xxx",
      "qris_string": "...",
      "qris_url": "https://...",
      "expired_at": "2026-01-07T11:00:00Z"
    }
  }
}
```

#### GET `/api/donations/{id}`
Menampilkan detail donasi.

#### POST `/api/donations/manual-transfer`
Upload bukti transfer manual.
- **Request:** Multipart form data
  - `donation_id`: UUID
  - `bank_name`: String
  - `account_number`: String
  - `account_holder_name`: String
  - `transfer_amount`: Decimal
  - `transfer_proof`: File (image, max 2MB)
  - `transfer_date`: Date

#### POST `/api/payment/callback`
Webhook dari DOKU untuk update status pembayaran.

## Environment Configuration

Tambahkan konfigurasi berikut ke `.env`:

```env
# DOKU Payment Gateway Configuration
DOKU_CLIENT_ID=your_client_id_here
DOKU_SECRET_KEY=your_secret_key_here
DOKU_ENV=sandbox
DOKU_CALLBACK_URL="${FRONTEND_URL}/payment/callback"
DOKU_REDIRECT_URL="${FRONTEND_URL}/payment/success"
```

### Mendapatkan Credentials DOKU

1. Daftar merchant di [DOKU Dashboard](https://dashboard.doku.com)
2. Buat project baru
3. Pilih Non-SNAP integration
4. Copy Client ID dan Secret Key
5. Set environment ke `sandbox` untuk testing, `production` untuk live

## DOKU Integration

### Package Installation

```bash
composer require doku/doku-php-library
```

**Note:** Saat ini installation pending karena dependency issue (PHP GD extension). Untuk sementara, implementasi menggunakan dummy data.

### Non-SNAP API Documentation
- [DOKU GitHub Library](https://github.com/PTNUSASATUINTIARTHA-DOKU/doku-php-library)
- [Payment Page Integration](https://developers.doku.com/accept-payments/direct-api/non-snap/cards/payment-page-integration-guide)

### Implementation Notes

Controller `DonationController` memiliki method:
- `generateQrisPayment()`: Generate QRIS payment (TODO: implement actual DOKU API)
- `generateVirtualAccountPayment()`: Generate VA payment (TODO: implement actual DOKU API)
- `paymentCallback()`: Handle callback dari DOKU (TODO: implement signature verification)

## Models

### Donation Model
**Location:** `app/Models/Donation.php`

**Relationships:**
- `belongsTo`: User
- `hasOne`: ManualTransfer

**Scopes:**
- `pending()`: Filter status pending
- `paid()`: Filter status paid
- `byPaymentMethod($method)`: Filter by payment method

**Methods:**
- `markAsPaid()`: Update status ke paid
- `markAsFailed()`: Update status ke failed

### ManualTransfer Model
**Location:** `app/Models/ManualTransfer.php`

**Relationships:**
- `belongsTo`: Donation
- `belongsTo`: User (as verifier)

**Scopes:**
- `pending()`: Filter status pending
- `verified()`: Filter status verified

**Methods:**
- `verify($userId, $notes)`: Verify transfer dan mark donation as paid
- `reject($userId, $notes)`: Reject transfer dan mark donation as failed

## Form Requests

### StoreDonationRequest
**Validation Rules:**
- `amount`: required, numeric, min:1000
- `donor_name`: required, string, max:255
- `donor_ig`: nullable, string, max:255
- `message`: nullable, string, max:1000
- `payment_method`: required, in:qris,virtual_account,manual_transfer

### StoreManualTransferRequest
**Validation Rules:**
- `donation_id`: required, exists:donations,id
- `bank_name`: required, string, max:255
- `account_number`: required, string, max:255
- `account_holder_name`: required, string, max:255
- `transfer_amount`: required, numeric, min:1000
- `transfer_proof`: required, image, max:2048, mimes:jpg,jpeg,png
- `transfer_date`: required, date

## File Storage

Transfer proofs disimpan di: `storage/app/public/donations/transfer-proofs/`

Pastikan storage link sudah dibuat:
```bash
php artisan storage:link
```

## Testing

Run migration:
```bash
php artisan migrate
```

Test API dengan:
```bash
# Get leaderboard
curl http://localhost:8000/api/donations

# Create donation
curl -X POST http://localhost:8000/api/donations \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 50000,
    "donor_name": "Test User",
    "payment_method": "qris"
  }'
```

## TODO

1. ✅ Database migrations
2. ✅ Models dengan relationships
3. ✅ API endpoints
4. ✅ Form validation
5. ⏳ Install DOKU library (blocked by PHP GD extension)
6. ⏳ Implement actual DOKU payment generation
7. ⏳ Implement DOKU signature verification
8. ⏳ Admin panel untuk verify manual transfers
9. ⏳ Email notifications
10. ⏳ Unit & feature tests

## Frontend Integration

Endpoint yang sudah tersedia untuk frontend:
- Donasi: `GET /api/donations` (leaderboard)
- Create donasi: `POST /api/donations`
- Upload bukti: `POST /api/donations/manual-transfer`
- Detail donasi: `GET /api/donations/{id}`
