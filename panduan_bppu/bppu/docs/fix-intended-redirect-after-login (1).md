# Fix: Login Tidak Kembali ke URL Awal (Intended Redirect)

## Gejala

User akses URL terproteksi (contoh `/pengelola/mahasiswa`), karena belum login diarahkan ke halaman login (`/pengelola/login`). Setelah login berhasil, harusnya kembali ke `/pengelola/mahasiswa`, TAPI malah selalu ke halaman default (contoh `/pengelola/dasbor`).

Pola ini berlaku untuk semua guard/area dengan prefix berbeda (admin, dosen, mahasiswa, dll).

## Root Cause

Laravel punya mekanisme "intended URL": URL tujuan disimpan ke session ketika redirect ke login, lalu diambil setelah login berhasil.

Masalahnya ada DUA sisi yang harus sinkron:

1. **Sisi middleware (saat redirect ke login)**: HARUS pakai `redirect()->guest('/login-url')` - method `guest()` ini yang menyimpan URL saat ini ke session sebagai intended URL.
2. **Sisi login controller (setelah auth berhasil)**: pakai `redirect()->intended('/default-url')` - method `intended()` mengambil URL dari session, fallback ke URL default jika tidak ada.

Jika middleware pakai `redirect('/login-url')` BIASA (tanpa `->guest()`), maka session tidak terisi, dan `intended()` selalu pakai fallback.

## Cara Verifikasi Cepat

Cari di project:

```bash
# Cari middleware yang redirect ke login
grep -rn "redirect('/.*login')" app/Http/Middleware/

# Cari login controller yang pakai intended
grep -rn "redirect()->intended\|return redirect()->intended" app/Http/Controllers/
```

Jika ada middleware yang return `redirect('/xxx/login')` (TANPA `->guest()`) dan controller login-nya pakai `redirect()->intended(...)`, maka bug ini ADA.

## Fix

### 1. Middleware - ganti `redirect()` jadi `redirect()->guest()`

**SEBELUM (BUG):**
```php
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        return redirect('/pengelola/login');
    }
    // ...
}
```

**SESUDAH (FIX):**
```php
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        return redirect()->guest('/pengelola/login');
    }
    // ...
}
```

Catatan: Untuk redirect karena alasan LAIN (misal akses ditolak karena role kurang), TETAP pakai `redirect()` biasa - bukan `guest()`. `guest()` hanya untuk kasus "belum login" supaya URL tujuan disimpan untuk dipakai setelah login.

### 2. Login Controller - pastikan pakai `intended()`

**Benar (sudah seperti ini biasanya):**
```php
public function login(Request $request)
{
    // ... validasi & Auth::attempt ...

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        // ...
        return redirect()->intended('/pengelola/dasbor');
    }
    // ...
}
```

Jika controller pakai `redirect('/pengelola/dasbor')` BIASA, ganti jadi `redirect()->intended('/pengelola/dasbor')`.

## Edge Cases yang Harus Tetap Bekerja

Setelah fix, pastikan flow berikut TETAP benar:

1. User akses URL terproteksi tanpa login -> diarahkan ke login -> setelah login balik ke URL awal. (Inti perbaikan)
2. User login langsung dari halaman login (tanpa URL awal) -> diarahkan ke halaman default (fallback `intended()`).
3. User dengan role khusus (misal `data_clerk` di sistem ini) yang punya redirect khusus -> override `intended()` jika perlu, JANGAN sampai mereka diarahkan ke URL yang tidak boleh diakses. Contoh: kalau ada role yang dipaksa ke `/pengelola/arsip`, periksa role DULU sebelum `intended()`, atau gunakan `intended('/pengelola/arsip')` untuk role itu.
4. User sudah login lalu akses halaman login -> `RedirectIfAuthenticated` tetap arahkan ke halaman default sesuai role.

## Checklist untuk Agen

Saat memperbaiki sistem serupa:

- [ ] Identifikasi semua middleware auth (Admin, Dosen, Mahasiswa, dst) di `app/Http/Middleware/`
- [ ] Untuk setiap middleware: ganti `redirect('/xxx/login')` jadi `redirect()->guest('/xxx/login')` HANYA pada cabang "belum login" (`!Auth::check()`)
- [ ] JANGAN ganti redirect untuk cabang "role tidak sesuai" - itu memang harus redirect biasa
- [ ] Pastikan controller login terkait pakai `redirect()->intended(...)` bukan `redirect(...)` biasa
- [ ] Verifikasi role-based redirect override (kalau ada) tetap menang atas `intended()` untuk role yang dibatasi
- [ ] Test manual: akses URL terproteksi tanpa login -> login -> harus balik ke URL awal

## Referensi Laravel

- `Redirector::guest($path)` - simpan `url()->full()` ke session key `url.intended`, lalu redirect ke `$path`
- `Redirector::intended($default)` - pull `url.intended` dari session, redirect ke sana atau ke `$default` jika kosong

Lihat: `Illuminate\Routing\Redirector` di vendor Laravel.
