# Dashboard Eksekutif untuk Role Pimpinan

Dashboard eksekutif yang komprehensif telah dibuat untuk role `pimpinan` dengan fitur-fitur lengkap untuk monitoring dan analitik strategis.

## Fitur Dashboard Eksekutif

### 1. Overview Keuangan
- Pendapatan bulan ini dengan perbandingan growth vs bulan lalu
- Pengeluaran bulan ini dengan tracking growth
- Kas bersih bulan ini dengan rasio pengeluaran
- Total penjualan dengan jumlah transaksi

### 2. Ringkasan Statistik
**Keuangan:**
- Total kas masuk (all time)
- Total kas keluar (all time)
- Saldo bersih keseluruhan

**Operasional:**
- Unit kerja aktif vs total
- Pengguna aktif vs total
- Mitra usaha aktif vs total

**Inventori:**
- Total item aktif
- Item dengan stock rendah (alert)
- Nilai total inventori berdasarkan HPP

### 3. Tren Keuangan (6 Bulan)
Chart interaktif menampilkan:
- Tren pendapatan
- Tren pengeluaran
- Tren penjualan

### 4. Performa Unit Kerja
Tabel detail per unit kerja menampilkan:
- Pendapatan
- Jumlah transaksi
- Kas masuk
- Kas keluar
- Kas bersih
- Total keseluruhan di footer

### 5. Top 10 Item Terlaris
Ranking item terlaris bulan ini dengan:
- Total quantity terjual
- Total revenue
- Jumlah transaksi

### 6. Performa per Kategori
Analitik penjualan berdasarkan kategori barang dengan:
- Total revenue per kategori
- Total quantity
- Jumlah transaksi

## Struktur File

### Backend
```
app/
├── Services/
│   └── Dashboard/
│       └── ExecutiveDashboardService.php (Service untuk data statistik)
└── Http/
    └── Controllers/
        └── AdminController.php (Updated dengan method executiveDashboard)
```

### Frontend Components (Modular)
```
resources/js/Components/Dashboard/Executive/
├── OverviewCard.vue (Card untuk overview stats)
├── StatCard.vue (Card untuk multiple stats)
├── TrendsChart.vue (Chart untuk trends dengan Chart.js)
├── WorkUnitTable.vue (Tabel performa unit kerja)
└── TopItemsCard.vue (Card untuk top items)
```

### Pages
```
resources/js/Pages/Admin/Dashboard/
└── PimpinanDashboard.vue (Main dashboard page)
```

### Database
```
database/seeders/
└── PimpinanRoleSeeder.php (Seeder untuk create role pimpinan)
```

## Cara Setup

### 1. Install Dependencies
```bash
npm install chart.js
npm run build
```

### 2. Buat Role Pimpinan (Manual via Tinker - PRODUCTION)
Karena ini production environment, gunakan tinker untuk create role:

```bash
php artisan tinker
```

Kemudian jalankan:
```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create role
$role = Role::firstOrCreate(['name' => 'pimpinan']);

// Create permissions
$permissions = [
    'view dashboard',
    'view statistics',
    'view reports',
    'view work units',
    'view financial data',
    'view sales data',
    'view inventory data',
];

foreach ($permissions as $permissionName) {
    $permission = Permission::firstOrCreate(['name' => $permissionName]);
    $role->givePermissionTo($permission);
}

exit
```

### 3. Assign Role ke User
Via tinker:
```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'email@pimpinan.com')->first();
$user->assignRole('pimpinan');
exit
```

Atau via UserManagement di admin panel (untuk sysadmin).

## Akses Dashboard

1. Login ke https://bppu.ikipsiliwangi.ac.id/pengelola/login dengan akun yang memiliki role `pimpinan`
2. Setelah login, akan otomatis diarahkan ke dashboard eksekutif
3. URL dashboard: https://bppu.ikipsiliwangi.ac.id/pengelola/dasbor

## Keamanan & Access Control

- Dashboard hanya bisa diakses oleh user dengan role `pimpinan`
- Read-only access - tidak ada fitur edit/create/delete
- Data real-time dari database
- Middleware `admin` memastikan user sudah login
- Controller routing otomatis berdasarkan role user

## Data Sources

Semua data diambil dari database secara real-time:
- `penjualans` - Data penjualan
- `penjualan_items` - Detail item penjualan
- `transaksi_kas` - Data kas masuk/keluar
- `buku_kas` - Buku kas per unit kerja
- `work_units` - Data unit kerja
- `barangs` - Data barang/inventory
- `suppliers` - Data mitra usaha
- `users` - Data pengguna

## Customization

### Mengubah Periode Chart
Edit file `ExecutiveDashboardService.php` method `getTrendsData()`:
```php
// Ubah dari 6 bulan ke periode lain
for ($i = 11; $i >= 0; $i--) { // untuk 12 bulan
```

### Menambah/Mengurangi Top Items
Edit file `ExecutiveDashboardService.php` method `getSalesStats()`:
```php
->limit(20) // ubah dari 10 ke 20
```

### Mengubah Warna
Edit file `WARNA_UTAMA_IKIP.md` untuk panduan warna atau langsung edit di component Vue.

## Troubleshooting

### Dashboard tidak muncul
1. Pastikan user sudah di-assign role `pimpinan`
2. Clear cache: `php artisan cache:clear`
3. Clear view cache: `php artisan view:clear`

### Data tidak muncul
1. Pastikan ada data di database
2. Check log: `tail -f storage/logs/laravel.log`

### Chart tidak muncul
1. Pastikan chart.js sudah terinstall: `npm list chart.js`
2. Rebuild: `npm run build`
