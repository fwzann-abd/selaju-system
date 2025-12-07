# Testing & Debugging Guide

Panduan lengkap untuk melakukan testing dan debugging pada aplikasi Selaju.

---

## 🧪 Testing Scenarios

### 1. Unit Testing (Backend)

#### Test Authentication
```php
<?php
// tests/Feature/AuthTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'user' => ['id', 'name', 'email'],
                     'token'
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    /** @test */
    public function login_requires_valid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Invalid credentials']);
    }
}
```

#### Test Order Creation
```php
<?php
// tests/Feature/OrderTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Participant;
use App\Models\Sejajan;
use App\Models\SejajanProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_order()
    {
        // Setup
        $user = User::factory()->create();
        $participant = Participant::factory()->create(['user_id' => $user->id]);
        $sejajan = Sejajan::factory()->create();
        $product = SejajanProduct::factory()->create([
            'sejajan_id' => $sejajan->id,
            'stock' => 100,
            'price' => 10000
        ]);

        // Act
        $response = $this->actingAs($user)->postJson("/api/sejajans/{$sejajan->slug}/orders", [
            'sejajan_id' => $sejajan->id,
            'delivery_method' => 'pickup',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => 10000
                ]
            ]
        ]);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'order' => ['id', 'total_price', 'status']
                 ]);

        $this->assertDatabaseHas('sejajan_orders', [
            'participant_id' => $participant->id,
            'sejajan_id' => $sejajan->id,
            'total_price' => 20000,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function order_creation_reduces_stock()
    {
        $user = User::factory()->create();
        $participant = Participant::factory()->create(['user_id' => $user->id]);
        $sejajan = Sejajan::factory()->create();
        $product = SejajanProduct::factory()->create([
            'sejajan_id' => $sejajan->id,
            'stock' => 100
        ]);

        $initialStock = $product->stock;

        $this->actingAs($user)->postJson("/api/sejajans/{$sejajan->slug}/orders", [
            'sejajan_id' => $sejajan->id,
            'delivery_method' => 'pickup',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'price' => 10000
                ]
            ]
        ]);

        $product->refresh();
        $this->assertEquals($initialStock - 5, $product->stock);
    }

    /** @test */
    public function cannot_order_out_of_stock_product()
    {
        $user = User::factory()->create();
        $participant = Participant::factory()->create(['user_id' => $user->id]);
        $sejajan = Sejajan::factory()->create();
        $product = SejajanProduct::factory()->create([
            'sejajan_id' => $sejajan->id,
            'stock' => 5
        ]);

        $response = $this->actingAs($user)->postJson("/api/sejajans/{$sejajan->slug}/orders", [
            'sejajan_id' => $sejajan->id,
            'delivery_method' => 'pickup',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,  // More than stock
                    'price' => 10000
                ]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items.0.quantity']);
    }
}
```

### 2. Integration Testing (Frontend)

#### Setup Test File
```typescript
// tests/sejajan/order.test.ts
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'

describe('Order Creation Flow', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('displays validation error when cart is empty', async () => {
    // Test implementation
  })

  it('calculates total price correctly', () => {
    const cart = [
      { id: 1, name: 'Product 1', price: 10000, quantity: 2 },
      { id: 2, name: 'Product 2', price: 15000, quantity: 1 }
    ]
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
    expect(total).toBe(35000)
  })

  it('shows success message after order creation', async () => {
    // Mock API
    vi.mock('~/utils/api.client', () => ({
      post: vi.fn().mockResolvedValue({ data: { message: 'Order created' } })
    }))

    // Test implementation
  })
})
```

---

## 🔍 Manual Testing Checklist

### Authentication Flow
- [ ] Register dengan data valid
- [ ] Register dengan email duplikat → Tampil error
- [ ] Login dengan kredensial benar
- [ ] Login dengan kredensial salah → Tampil error
- [ ] Logout → Token terhapus, redirect ke login
- [ ] Akses protected route tanpa login → Redirect ke login
- [ ] Email verification → Email terkirim dan link berfungsi

### Order Flow (Sejajan)
- [ ] **Buyer Side:**
  - [ ] Browse produk → Tampil semua produk
  - [ ] Add to cart → Produk masuk cart
  - [ ] Update quantity di cart
  - [ ] Remove item dari cart
  - [ ] Checkout dengan pickup → Tidak perlu isi alamat
  - [ ] Checkout dengan delivery → Wajib isi alamat
  - [ ] Order berhasil → Tampil konfirmasi
  - [ ] Terima notifikasi real-time saat status berubah

- [ ] **Seller Side:**
  - [ ] Terima notifikasi real-time saat ada order baru
  - [ ] Lihat detail order
  - [ ] Update status: pending → processing
  - [ ] Update status: processing → completed
  - [ ] Update status: pending → cancelled
  - [ ] Buyer terima notifikasi setiap status berubah

### Product Management
- [ ] Create product → Berhasil dibuat
- [ ] Edit product → Data ter-update
- [ ] Delete product → Soft delete, tidak tampil lagi
- [ ] Upload gambar → Gambar tersimpan dan tampil
- [ ] Add stock → Stock bertambah
- [ ] Order produk → Stock berkurang otomatis

### Real-time Features
- [ ] Reverb server berjalan di port 8080
- [ ] Queue worker berjalan
- [ ] Browser terkoneksi ke WebSocket
- [ ] Event NewOrderReceived → Seller terima notifikasi
- [ ] Event OrderStatusUpdated → Buyer terima notifikasi
- [ ] Notifikasi muncul tanpa refresh page

---

## 🐛 Common Bugs & Solutions

### Backend Issues

#### 1. Queue Worker Stuck
**Symptom:** Events tidak ter-broadcast
```bash
# Check if worker running
ps aux | grep queue:work

# Restart worker
php artisan queue:restart

# Run worker
php artisan queue:work
```

#### 2. CORS Error
**Symptom:** Frontend tidak bisa hit API
```php
// config/cors.php
'allowed_origins' => ['http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

#### 3. Database Connection
**Symptom:** SQLSTATE[HY000] [2002] Connection refused
```bash
# Check MySQL running
sudo systemctl status mysql

# Check .env configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
```

### Frontend Issues

#### 1. Hydration Mismatch
**Symptom:** Warning in console about hydration
```typescript
// Use ClientOnly component
<ClientOnly>
  <ComponentWithLocalStorage />
</ClientOnly>
```

#### 2. Query Stale Data
**Symptom:** Data tidak update setelah mutation
```typescript
// Invalidate query after mutation
await mutation.mutateAsync(data)
await queryClient.invalidateQueries({ queryKey: ['orders'] })
```

#### 3. WebSocket Not Connecting
**Symptom:** Real-time not working
```typescript
// Check Echo configuration
// plugins/echo.client.ts
console.log('Echo client:', window.Echo)
console.log('Connected:', window.Echo?.connector?.pusher?.connection?.state)

// Should output: "connected"
```

---

## 🧰 Debugging Commands

### Laravel
```bash
# Clear all cache
php artisan optimize:clear

# View routes
php artisan route:list

# Test event broadcast
php artisan tinker
>>> broadcast(new \App\Events\NewOrderReceived($order));

# Check queue jobs
php artisan queue:failed
php artisan queue:retry all

# Database
php artisan migrate:fresh --seed
php artisan db:show
```

### Nuxt
```bash
# Clear Nuxt cache
rm -rf .nuxt node_modules/.cache

# Reinstall dependencies
npm install

# Check build
npm run build

# Analyze bundle
npm run analyze
```

---

## 📊 Performance Testing

### Backend Load Test
```bash
# Install Apache Bench
sudo apt install apache2-utils

# Test API endpoint
ab -n 1000 -c 10 -H "Authorization: Bearer TOKEN" \
   http://localhost:8000/api/sejajans

# Results will show:
# - Requests per second
# - Time per request
# - Failed requests
```

### Frontend Lighthouse Test
```bash
# Run Lighthouse in Chrome DevTools
# Or use CLI
npm install -g lighthouse
lighthouse http://localhost:3000 --view
```

---

## 📝 Test Execution

### Run Backend Tests
```bash
# All tests
php artisan test

# Specific test file
php artisan test tests/Feature/OrderTest.php

# With coverage
php artisan test --coverage

# Filter by test name
php artisan test --filter=can_create_order
```

### Run Frontend Tests
```bash
# All tests
npm run test

# Watch mode
npm run test:watch

# Coverage
npm run test:coverage
```

---

## 🎯 Test Coverage Goals

### Backend
- ✅ Authentication: 100%
- ✅ Order Creation: 95%
- ✅ Order Status Update: 90%
- ✅ Product CRUD: 85%
- ⚠️ Broadcasting: 70% (manual testing diperlukan)

### Frontend
- ✅ Components: 80%
- ✅ Composables: 85%
- ⚠️ Pages: 60% (integration testing)
- ⚠️ Real-time: Manual testing only

---

**Update Terakhir:** 1 Desember 2025
