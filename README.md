<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## Fitur Rental dan Pengembalian Mobil

### Alur customer
1. Login sebagai customer, lalu buka Dashboard Customer.
2. Klik **Sewa Mobil Baru**, pilih mobil, tanggal mulai, tanggal selesai, dan isi catatan jika diperlukan.
3. Sistem menghitung total hari dan biaya rental, lalu menyimpan pengajuan dengan status `pending`.
4. Buka menu **Pengembalian Mobil** untuk melihat rental yang sudah berstatus `ongoing`.
5. Isi kondisi mobil dan konfirmasi pengembalian. Sistem menyimpan tanggal pengembalian, menghitung keterlambatan, dan mengubah status booking menjadi `completed`.

### Alur admin
1. Login sebagai admin dan buka Dashboard Admin.
2. Pada daftar **Booking Terbaru**, klik **Konfirmasi** untuk mengubah status `pending` menjadi `confirmed`.
3. Klik **Mulai Rental** untuk mengubah status `confirmed` menjadi `ongoing`. Setelah itu customer dapat memproses pengembalian.

Denda keterlambatan saat ini ditetapkan Rp100.000 per hari. Sistem menolak pengajuan jika mobil sudah memiliki booking aktif pada rentang tanggal yang bertabrakan. Fitur menggunakan tabel `bookings` dan `returns` yang sudah tersedia pada migration project.

## Fitur Pembayaran dan Verifikasi

### Alur customer
1. Setelah mengajukan sewa mobil atau melalui **Dashboard Customer**, buka daftar **Booking Aktif Saat Ini** pada pesanan yang berstatus **Menunggu Pembayaran**.
2. Klik **Batalkan** jika ingin membatalkan pesanan sebelum membayar (status booking otomatis berubah menjadi `cancelled`), atau klik **Bayar Sekarang** untuk melanjutkan pembayaran.
3. Pilih metode pembayaran (**Transfer Bank** atau **QRIS / E-Wallet**). Jika memilih **QRIS / E-Wallet**, kode QRIS akan muncul otomatis di bawah pilihan metode.
4. Unggah bukti pembayaran (`JPG`, `PNG`, `WEBP`, atau `PDF`) lalu klik **Kirim Bukti Pembayaran**.
5. Sistem menyimpan data pembayaran dengan status `pending` (tampil sebagai **Menunggu Verifikasi** pada **Dashboard Customer** dan menu **Pembayaran**) hingga diperiksa oleh admin. Jika pembayaran ditolak, customer dapat mengunggah ulang bukti pembayaran melalui tombol **Bayar Ulang**.

### Alur admin
1. Login sebagai admin dan buka **Dashboard Admin**. Pada daftar **Booking Terbaru**, pesanan yang belum dibayar ditandai sebagai **BELUM BAYAR**, sedangkan pesanan yang sudah mengunggah bukti pembayaran berstatus `pending`.
2. Buka menu **Verifikasi Pembayaran** untuk melihat daftar transaksi masuk beserta informasi nama mobil, penyewa, metode, nominal, dan tombol **Buka Bukti**.
3. Periksa bukti transfer yang diunggah penyewa, lalu klik **Terima** untuk menyetujui pembayaran (status pembayaran berubah menjadi `verified` dan status booking otomatis menjadi `confirmed`) atau klik **Tolak** jika bukti tidak valid (`rejected` dan booking menjadi `cancelled`).
4. Setelah pembayaran diterima dan status booking menjadi `confirmed`, admin dapat langsung menekan tombol **Mulai Rental** untuk mengubah status booking menjadi `ongoing`.

Bukti pembayaran disimpan secara lokal pada direktori `storage/app/public/payments` (maksimal 1 GB) dan gambar kode QRIS ditempatkan pada `public/image/qris.png`. Fitur ini menggunakan tabel `payments` yang terhubung langsung dengan tabel `bookings`.