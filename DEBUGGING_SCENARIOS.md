# Dokumentasi Debugging & Troubleshooting

Dokumen ini berisi skenario debugging yang pernah terjadi selama development dan cara penyelesaiannya.

---

## 🐛 Scenario 1: Real-time Notification Tidak Muncul

### Problem
Ketika buyer membuat order baru, seller tidak menerima notifikasi real-time di dashboard meskipun Reverb server sudah berjalan.

### Symptoms
- Order berhasil dibuat di database
- Reverb server berjalan di port 8080
- Browser console tidak menunjukkan error
- Tidak ada notifikasi muncul di halaman seller

### Root Cause Analysis

#### Step 1: Cek Log Laravel
```bash
tail -f storage/logs/laravel.log
```
**Ditemukan error:**
```
[2025-12-01] local.ERROR: Pusher error: cURL error 7: Failed to connect to localhost port 8080
```

#### Step 2: Cek Queue Worker
```bash
ps aux | grep "queue:work"
```
**Hasil:** Queue worker tidak berjalan atau mati setelah process 1 job (flag `--once`)

### Debugging Process

1. **Identifikasi masalah broadcasting:**
   - Event menggunakan `ShouldBroadcast` → masuk ke queue
   - Queue worker tidak berjalan → event tidak ter-broadcast

2. **Cek konfigurasi:**
   ```php
   // app/Events/NewOrderReceived.php
   class NewOrderReceived implements ShouldBroadcast // ❌ Masuk queue
   ```

3. **Test manual broadcast:**
   ```bash
   php artisan tinker
   broadcast(new \App\Events\NewOrderReceived($order));
   ```
   **Hasil:** Error karena tidak ada queue worker

### Solution

#### Fix 1: Ubah Event ke ShouldBroadcastNow
```php
// app/Events/NewOrderReceived.php
class NewOrderReceived implements ShouldBroadcastNow // ✅ Langsung broadcast
```

#### Fix 2: Jalankan Queue Worker Permanen
```bash
# Terminal terpisah
php artisan queue:work
```

#### Fix 3: Tambahkan Logging untuk Debug
```php
// app/Http/Controllers/Api/SejajanOrderController.php
use Illuminate\Support\Facades\Log;

Log::info('Broadcasting new order', [
    'order_id' => $order->id,
    'seller_channel' => 'orders.seller.' . $order->sejajan->participant_id,
]);
```

### Verification
✅ Order baru → Notifikasi muncul real-time
✅ Log Laravel menunjukkan broadcast berhasil
✅ Console browser menunjukkan event diterima

---

## 🐛 Scenario 2: Update Status Order Return 404

### Problem
Ketika seller mencoba update status order dari "pending" ke "processing", muncul error 404 Not Found.

### Symptoms
```json
{
  "message": "The route api/sejajans/ajay/orders/NaN/status could not be found."
}
```

### Root Cause Analysis

#### Step 1: Cek Network Request
```javascript
// Request URL di browser console
PUT /api/sejajans/ajay/orders/NaN/status
```
**Ditemukan:** Order ID adalah `NaN` (Not a Number)

#### Step 2: Cek Frontend Code
```typescript
// File: app/pages/apps/sejajan/profile/mart/[slug]/orders/index.vue
await updateStatusMutation.mutateAsync({
  orderId: Number(order.id),  // ❌ Problem di sini
  status: newStatus,
  storeSlug: slug.value
})
```

#### Step 3: Cek Data Type
```typescript
console.log(order.id) // Output: "019ad413-465e-733b-8141-1b4d9f26687e"
console.log(Number(order.id)) // Output: NaN
```
**Ditemukan:** Order ID adalah UUID (string), bukan integer!

### Debugging Process

1. **Trace data flow:**
   - Backend menggunakan UUID untuk order ID
   - Frontend convert UUID ke Number → `NaN`
   - Route `/orders/NaN/status` → 404

2. **Cek API endpoint:**
   ```php
   // routes/api.php
   Route::put('/sejajans/{sejajanSlug}/orders/{orderId}/status', ...)
   // ✅ Endpoint support string parameter
   ```

3. **Cek type definition:**
   ```typescript
   // composables/queries/useSejajanQueries.ts
   mutationFn: async ({ orderId, status, storeSlug }: { 
     orderId: number,  // ❌ Wrong type
     status: string, 
     storeSlug: string 
   })
   ```

### Solution

#### Fix 1: Ubah Type Definition
```typescript
// composables/queries/useSejajanQueries.ts
mutationFn: async ({ orderId, status, storeSlug }: { 
  orderId: string,  // ✅ UUID adalah string
  status: string, 
  storeSlug: string 
})
```

#### Fix 2: Hapus Number() Conversion
```typescript
// pages/apps/sejajan/profile/mart/[slug]/orders/index.vue
await updateStatusMutation.mutateAsync({
  orderId: order.id,  // ✅ Langsung kirim string
  status: newStatus,
  storeSlug: slug.value
})
```

### Verification
✅ Update status berhasil
✅ Order ID terkirim sebagai UUID string
✅ API endpoint menerima UUID dengan benar

---

## 🐛 Scenario 3: LocalStorage Error di SSR

### Problem
Saat development server berjalan, muncul error "localStorage is not defined" di console.

### Symptoms
```
[nuxt] Error: localStorage is not defined
  at useAuth (composables/auth/useAuth.ts:15)
  at setup (pages/apps/sejajan/orders.vue:10)
```

### Root Cause Analysis

#### Step 1: Identifikasi Context
- Error terjadi saat **server-side rendering** (SSR)
- `localStorage` hanya tersedia di browser, tidak di Node.js server

#### Step 2: Cek Code
```typescript
// composables/auth/useAuth.ts
export const useAuth = () => {
  const token = ref(localStorage.getItem('auth_token'))  // ❌ Error di SSR
}
```

#### Step 3: Test di Browser vs Server
```typescript
console.log(typeof window)
// Browser: "object"
// Server: "undefined"
```

### Debugging Process

1. **Reproduce error:**
   - Refresh page → Error muncul saat SSR
   - Navigate client-side → Tidak error

2. **Check execution context:**
   ```typescript
   console.log('Running on:', import.meta.client ? 'Client' : 'Server')
   // Output saat error: "Running on: Server"
   ```

3. **Identify affected files:**
   - `useAuth.ts`
   - `useSejajanQueries.ts`
   - `echo.client.ts`

### Solution

#### Fix 1: Check Client-Side
```typescript
// composables/auth/useAuth.ts
export const useAuth = () => {
  const token = ref(
    import.meta.client ? localStorage.getItem('auth_token') : null
  )
}
```

#### Fix 2: Use Plugin Hook
```typescript
// plugins/auth-init.client.ts
export default defineNuxtPlugin(() => {
  // .client.ts suffix = only run on client
  const token = localStorage.getItem('auth_token')
  // Initialize auth state
})
```

#### Fix 3: Check Window Existence
```typescript
export const useAuth = () => {
  const getToken = () => {
    if (typeof window === 'undefined') return null
    return localStorage.getItem('auth_token')
  }
}
```

### Verification
✅ SSR berjalan tanpa error
✅ Client-side hydration berhasil
✅ Auth state tersinkronisasi

---

## 🐛 Scenario 4: Form Request Validation Tidak Terpakai

### Problem
Custom validation di `StoreOrderRequest` tidak berjalan, masih menggunakan validation manual di controller.

### Symptoms
- `StoreOrderRequest` sudah dibuat
- Controller masih menggunakan `Validator::make()`
- Custom error messages tidak muncul

### Root Cause Analysis

#### Step 1: Cek Controller Method
```php
// SejajanOrderController.php
public function store(Request $request) {  // ❌ Masih pakai Request
    $validator = Validator::make($request->all(), [...])
}
```

#### Step 2: Cek Form Request
```php
// StoreOrderRequest.php
class StoreOrderRequest extends FormRequest {
    public function authorize(): bool {
        return false;  // ❌ Return false!
    }
}
```

### Solution

#### Fix 1: Update Method Signature
```php
// Before
public function store(Request $request) { ... }

// After ✅
public function store(StoreOrderRequest $request) { ... }
```

#### Fix 2: Enable Authorization
```php
// StoreOrderRequest.php
public function authorize(): bool {
    return true;  // ✅ Allow request
}
```

#### Fix 3: Remove Manual Validation
```php
// Before ❌
$validator = Validator::make($request->all(), [...]);
if ($validator->fails()) { ... }
$data = $validator->validated();

// After ✅
$data = $request->validated();  // Otomatis dari Form Request
```

### Verification
✅ Custom validation berjalan
✅ Custom error messages muncul
✅ Code lebih clean dan maintainable

---

## 🛠 Tools & Techniques untuk Debugging

### 1. Laravel Debugging
```php
// Logging
Log::info('Debug data', ['key' => $value]);
Log::error('Error occurred', ['exception' => $e->getMessage()]);

// Dump & Die
dd($variable);

// Dump tanpa die
dump($variable);

// Query Debugging
DB::enableQueryLog();
// ... queries ...
dd(DB::getQueryLog());
```

### 2. Frontend Debugging
```typescript
// Console logging
console.log('Data:', data)
console.table(users)
console.error('Error:', error)

// Vue DevTools
// Install browser extension

// Network debugging
// Browser DevTools > Network tab

// Query debugging (Tanstack Query)
queryClient.getQueryData(queryKey)
queryClient.invalidateQueries(queryKey)
```

### 3. Real-time Debugging
```bash
# Reverb logs
php artisan reverb:start --debug

# Laravel logs
tail -f storage/logs/laravel.log

# Queue worker with verbose
php artisan queue:work --verbose
```

### 4. Database Debugging
```bash
# Check migrations
php artisan migrate:status

# Fresh migration with seed
php artisan migrate:fresh --seed

# Tinker untuk test data
php artisan tinker
>>> User::count()
>>> Order::with('items')->first()
```

---

## 📝 Checklist Debugging Process

### Saat Menemukan Bug:
- [ ] **Reproduce** - Pastikan bug bisa direproduce
- [ ] **Isolate** - Identifikasi scope masalah (frontend/backend/both)
- [ ] **Log** - Tambahkan logging di area yang dicurigai
- [ ] **Test** - Test setiap komponen secara terpisah
- [ ] **Fix** - Implementasi solusi
- [ ] **Verify** - Pastikan bug fixed dan tidak ada regression
- [ ] **Document** - Catat di dokumentasi untuk referensi

### Best Practices:
✅ Gunakan error handling yang proper
✅ Tambahkan logging di critical points
✅ Gunakan type safety (TypeScript, PHP type hints)
✅ Test sebelum commit
✅ Write clear error messages
✅ Document non-obvious solutions

---

**Update Terakhir:** 1 Desember 2025
