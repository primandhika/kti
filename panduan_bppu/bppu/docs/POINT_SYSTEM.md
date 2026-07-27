# Sistem Poin Member Buyer - Kantin BPPU IKIP Siliwangi

## 📊 Skema Perolehan Poin

Poin dihitung berdasarkan **total transaksi** dengan tier yang progresif:

| Rentang Transaksi | Poin Dasar | Persentase | Keterangan |
|-------------------|------------|------------|------------|
| **< Rp 5.000** | 1 poin | ~0.02-0.2% | Transaksi kecil |
| **Rp 5.000 - 9.999** | 5 poin | ~0.05-0.1% | Transaksi sedang |
| **Rp 10.000 - 24.999** | 15 poin | ~0.06-0.15% | Transaksi menengah |
| **Rp 25.000 - 49.999** | 40 poin | ~0.08-0.16% | Transaksi besar |
| **≥ Rp 50.000** | 100 poin | ~0.2% | 🎁 **BONUS VIP!** |

---

## 🏆 Membership Tier & Benefits

### Bronze (Default)
- **Minimum Poin**: 0
- **Diskon**: 0%
- **Multiplier Poin**: 1.0x
- **Status**: Member baru

### Silver
- **Minimum Poin**: 100
- **Diskon**: 5%
- **Multiplier Poin**: 1.2x
- **Status**: Member aktif

### Gold
- **Minimum Poin**: 500
- **Diskon**: 10%
- **Multiplier Poin**: 1.5x
- **Status**: Member premium

### Platinum
- **Minimum Poin**: 1,000
- **Diskon**: 15%
- **Multiplier Poin**: 2.0x
- **Status**: 👑 VIP Member

---

## 💰 Contoh Perhitungan Poin

### Transaksi Rp 25.000
- **Bronze** (1.0x): `40 × 1.0 = 40 poin`
- **Silver** (1.2x): `40 × 1.2 = 48 poin`
- **Gold** (1.5x): `40 × 1.5 = 60 poin`
- **Platinum** (2.0x): `40 × 2.0 = 80 poin`

### Transaksi Rp 50.000
- **Bronze** (1.0x): `100 × 1.0 = 100 poin`
- **Silver** (1.2x): `100 × 1.2 = 120 poin`
- **Gold** (1.5x): `100 × 1.5 = 150 poin`
- **Platinum** (2.0x): `100 × 2.0 = 200 poin`

---

## 📈 Simulasi Upgrade Tier

### Mencapai Silver (100 poin)
Sebagai **Bronze**:
- 3× transaksi @ Rp 25.000 = `3 × 40 = 120 poin` ✅
- ATAU 20× transaksi @ Rp 5.000 = `20 × 5 = 100 poin` ✅
- ATAU 1× transaksi @ Rp 50.000 = `100 poin` ✅

### Mencapai Gold (500 poin)
Sebagai **Silver** (1.2x):
- 10× transaksi @ Rp 25.000 = `10 × 48 = 480 poin` (hampir!)
- ATAU 5× transaksi @ Rp 50.000 = `5 × 120 = 600 poin` ✅

### Mencapai Platinum (1000 poin)
Sebagai **Gold** (1.5x):
- 7× transaksi @ Rp 50.000 = `7 × 150 = 1050 poin` ✅
- ATAU 17× transaksi @ Rp 25.000 = `17 × 60 = 1020 poin` ✅

---

## 🔄 Cara Kerja Sistem

### 1. Mendapatkan Poin (Earn)
```php
// Otomatis saat transaksi selesai
$pointService = new PointService();
$pointService->addPointsFromTransaction($user, $penjualan);
```

**Proses:**
1. Sistem cek total transaksi
2. Tentukan poin dasar berdasarkan rentang
3. Kalikan dengan tier multiplier
4. Tambahkan ke total poin user
5. Auto-check upgrade tier

### 2. Menukar Poin (Redeem)
```php
// Tukar poin untuk diskon
$pointService = new PointService();
$pointService->redeemPointsForDiscount($user, $jumlahPoin, $penjualan);
```

**Proses:**
1. Cek ketersediaan poin
2. Kurangi dari total poin
3. Berikan diskon sesuai
4. Auto-check downgrade tier (jika perlu)

### 3. Preview Poin
```php
// Lihat poin yang akan didapat
$preview = $pointService->getPointsPreview($amount, $user);
```

---

## 🎯 Keuntungan Sistem Ini

✅ **Progresif** - Semakin besar belanja, semakin efisien dapat poin  
✅ **Fair** - Member baru tetap bisa kumpul poin dari transaksi kecil  
✅ **Motivasi** - Mendorong untuk belanja lebih banyak sekaligus  
✅ **Loyalty Reward** - Tier tinggi dapat bonus poin berlipat  
✅ **Achievable** - Target tier realistis dengan transaksi rutin  

---

## 📝 Database Tables

### `point_rules`
Aturan perhitungan poin per rentang transaksi

### `membership_tiers`
Level keanggotaan (Bronze, Silver, Gold, Platinum)

### `member_points`
History perolehan dan penukaran poin

### `users`
Kolom tambahan:
- `membership_tier_id` - Tier saat ini
- `total_points` - Total poin tersedia
- `member_code` - Nomor anggota unik
- `member_since` - Tanggal jadi member

---

## 🚀 Usage dalam Kode

### Menggunakan PointService

```php
use App\Services\PointService;

// Initialize service
$pointService = new PointService();

// Tambah poin dari transaksi
$memberPoint = $pointService->addPointsFromTransaction($user, $penjualan);

// Redeem poin
$memberPoint = $pointService->redeemPointsForDiscount($user, 50, $penjualan);

// Preview poin
$preview = $pointService->getPointsPreview(25000, $user);
// Returns: ['base_points' => 40, 'multiplier' => 1.2, 'final_points' => 48, ...]
```

### Helper Methods di User Model

```php
// Cek apakah user adalah member
$user->isMember();

// Dapatkan poin tersedia
$user->getAvailablePoints();

// Dapatkan diskon percentage dari tier
$user->getDiscountPercentage();

// Dapatkan multiplier poin dari tier
$user->getPointsMultiplier();

// Update tier otomatis berdasarkan poin
$user->updateMembershipTier();
```

---

**Dibuat oleh**: Claude Code  
**Tanggal**: 2026-02-01  
**Versi**: 1.0
