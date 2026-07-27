# BPPU Online
## Badan Pengelola dan Pengembangan Usaha IKIP Siliwangi

Platform manajemen terpadu untuk BPPU IKIP Siliwangi yang mencakup sistem Point of Sale (PoS), manajemen keuangan, self-order kantin, dan dashboard pimpinan.

Badan Pengelola dan Pengembangan Usaha (BPPU) IKIP Siliwangi merupakan lembaga yang bertanggung jawab dalam mengelola dan mengembangkan usaha di lingkungan IKIP Siliwangi dengan komitmen untuk mengembangkan berbagai unit usaha yang berkelanjutan, transparan, dan akuntabel.

## Fitur Utama

### 1. Point of Sale (PoS) System
- Transaksi penjualan real-time
- Manajemen stok barang dengan barcode scanner
- Multiple payment methods
- Thermal printer support (58mm & 80mm)
- Kategori dan sub-kategori produk
- Featured products management
- Business partner (mitra) commission system

### 2. Self-Order System
- Pemesanan mandiri untuk member
- Registrasi dan verifikasi email
- Member points & rewards
- Order tracking
- Invoice generation
- Survey satisfaction system

### 3. Financial Management
- Buku kas digital dengan kategori
- Transaction categorization
- Multi unit kerja support
- Recycle bin untuk recovery data
- Financial reporting
- Export to PDF

### 4. Business Partner (Mitra) System
- Mitra dashboard
- Commission tracking
- Sales analytics
- Product management
- Withdrawal system
- Performance reports

### 5. Inventory Management
- Master barang management
- Stock opname automation
- Import/export barang (CSV/Excel)
- Supplier management
- Expired date tracking
- Multiple pricing schemas (konsinyasi, bagi hasil, jual putus)

### 6. Reporting & Analytics
- Executive dashboard untuk pimpinan
- Sales trend analysis
- Top items reporting
- Work unit performance
- PDF export untuk berbagai laporan
- Real-time statistics

### 7. Archive Management
- Document archiving system
- Kategorisasi dan tagging
- Usage tracking
- Automated scanning and import

### 8. Content Management
- Pages management
- Posts/News system
- Media picker
- Rich text editor (TipTap)
- SEO optimization with keywords
- Sitemap generation

## Tech Stack

- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3 + Inertia.js
- **Styling**: TailwindCSS
- **Database**: MySQL
- **PDF Generation**: Barryvdh/Laravel-DomPDF
- **Authentication**: Laravel Sanctum
- **Role Management**: Spatie Permission

## Installation

1. Clone repository
```bash
git clone git@github.com:ikipslw/bppu.git
cd bppu
```

2. Install dependencies
```bash
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database di `.env`
```
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations & seeders
```bash
php artisan migrate
php artisan db:seed
```

6. Build assets
```bash
npm run build
```

7. Create storage link
```bash
php artisan storage:link
```

## Development

Untuk development mode:
```bash
npm run dev
php artisan serve
```

## User Roles

- **Sysadmin**: Full system access
- **Admin**: Administrative functions
- **Pimpinan**: Executive dashboard & reports
- **Canteen**: Kantin operations & PoS
- **Shop**: Toko operations
- **Data Clerk**: Data entry & management
- **Mitra**: Business partner dashboard
- **Member**: Self-order & member area

## Configuration

### Settings Management
Platform menyediakan settings management untuk:
- Points & rewards configuration
- Report customization
- Work units setup
- Kategori barang & transaksi
- Sub-kategori menu & toko

### Business Schema
Support untuk berbagai skema bisnis:
- Konsinyasi
- Bagi Hasil
- Jual Putus
- Beli Putus

## Security

Jika menemukan vulnerability, silakan laporkan ke tim development.

## License

Proprietary - BPPU IKIP Siliwangi

## Contact

BPPU IKIP Siliwangi
Website: https://bppu.ikipsiliwangi.ac.id
