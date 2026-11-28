# Implementasi 3 Role (Admin / Staff / Customer) + Fitur Baru

## Cara pasang
```bash
composer install            # jika vendor belum ada
php artisan migrate         # 4 migration baru (2026_11_28_*)
php artisan test            # RentalFlowTest (diperbarui) + RoleAndFeatureTest (baru)
php artisan schedule:work   # (dev) atau cron `* * * * * php artisan schedule:run` (produksi)
```
Database lama: akun `staff@rentcar.com` masih ber-role `admin`. Ubah lewat menu
Admin > Pengguna, atau `php artisan migrate:fresh --seed` (seeder sudah memakai role `staff`).

## Tahap 1 - Database & Model
| File | Isi |
|---|---|
| `2026_11_28_000001_add_staff_role_to_users_table` | enum role: admin, staff, customer |
| `2026_11_28_000002_update_bookings_...` | start/end_date -> datetime; borrower_name, borrower_phone, with_driver, driver_fee, payment_deadline |
| `2026_11_28_000003_add_fine_columns_to_returns_table` | damage_fee, total_fine, fine_status (none/unpaid/waiting/paid), fine_payment_method, fine_proof_image, fine_paid_at |
| `2026_11_28_000004_create_settings_table` | key/value untuk Pengaturan Sistem |
Model diperbarui: User (isStaff, isStaffOrAdmin, dashboardRoute), Booking (casts, calculatePricing,
hasDateConflict berbasis jam, expireOverdue), CarReturn, Review, Car (averageRating), Setting (baru).

## Tahap 2 - Middleware & Routing
- `RoleMiddleware` (alias `role`): `role:admin`, `role:admin,staff`, `role:customer`. IsAdmin/IsCustomer dihapus.
- Login & `/dashboard` mengarah ke `admin.dashboard` / `staff.dashboard` / `customer.dashboard`.
- Admin saja: `admin.users.*`, `admin.reports.*`, `admin.settings.*`, `admin.dashboard`.
- Admin + Staff: mobil, kategori, booking/handover/return, verifikasi pembayaran, verifikasi denda
  (nama route tetap `admin.*` agar view lama tidak berubah).

## Tahap 3 - Admin & Petugas
Manajemen akun, laporan (filter tanggal/status, ekspor CSV, cetak), pengaturan sistem
(tenggat bayar, tarif sopir/hari, denda per jam/hari, info rekening, gambar QRIS),
pemeriksaan pengembalian (denda telat otomatis + denda kerusakan), verifikasi pelunasan denda.

## Tahap 4 - Customer
Form sewa (nama/kontak peminjam, tanggal+jam, Lepas Kunci/Dengan Sopir, rincian biaya JS),
total dihitung ulang di server, `payment_deadline` otomatis + pembatalan otomatis, halaman Denda
(unggah bukti), Ulasan (hanya booking completed milik sendiri, 1x per booking), halaman detail mobil
publik `/cars/{id}` berisi rating & ulasan, rating tampil di kartu katalog.

## Aturan bisnis yang perlu Anda ketahui
- Durasi = selisih jam dibulatkan ke atas per 24 jam (min. 1 hari).
- Pembatalan otomatis hanya untuk booking pending yang lewat tenggat DAN belum punya bukti bayar
  (atau buktinya ditolak). Bukti yang menunggu verifikasi tidak dibatalkan. Saat petugas menolak
  bukti, tenggat diperpanjang sebesar durasi pengaturan.
- Denda telat: mode per jam/hari dari pengaturan; kolom "tarif per hari" di form pengembalian
  (opsional) menimpa pengaturan.
- Laporan memfilter berdasarkan tanggal order. Pendapatan = pembayaran sewa verified + denda lunas.
