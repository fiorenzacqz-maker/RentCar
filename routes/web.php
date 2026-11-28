<?php

use App\Http\Controllers\Admin\CarController as AdminCarController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FineController as AdminFineController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarCatalogController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\FineController as CustomerFineController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\RentalController as CustomerRentalController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use Illuminate\Support\Facades\Route;

// Halaman Utama & Detail Mobil (publik, menampilkan ulasan)
Route::get('/', [HomepageController::class, 'index'])->name('homepage');
Route::get('/cars/{car}', [CarCatalogController::class, 'show'])->name('cars.show');

// Rute Autentikasi untuk Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Autentikasi Pengguna Masuk (Auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Edit Profil & Ganti Password (semua role)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Dashboard redirect otomatis berdasarkan role
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return redirect()->route($user->dashboardRoute());
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Khusus Admin (role:admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen akun (admin, staff, customer)
    Route::resource('users', AdminUserController::class)->except(['show']);

    // Laporan transaksi & pendapatan
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/print', [AdminReportController::class, 'print'])->name('reports.print');

    // Pengaturan sistem
    Route::get('/settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Petugas (staff) & Admin (role:admin,staff)
| Nama route tetap "admin.*" agar view yang sudah ada tidak berubah.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->name('admin.')->group(function () {
    // CRUD Mobil & Kategori
    Route::resource('cars', AdminCarController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // Kelola Booking, Serah Terima, dan Pengembalian
    Route::get('/rentals', [AdminRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/create', [AdminRentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals', [AdminRentalController::class, 'store'])->name('rentals.store');
    Route::get('/rentals/{booking}', [AdminRentalController::class, 'show'])->name('rentals.show');
    Route::post('/rentals/{booking}/handover', [AdminRentalController::class, 'handover'])->name('rentals.handover');
    Route::post('/rentals/{booking}/return', [AdminRentalController::class, 'returnCar'])->name('rentals.return');
    Route::post('/rentals/{booking}/cancel', [AdminRentalController::class, 'cancel'])->name('rentals.cancel');
    Route::patch('/bookings/{booking}/status', [AdminRentalController::class, 'updateStatus'])->name('bookings.status');

    // Verifikasi Pembayaran
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
    Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

    // Verifikasi Pelunasan Denda
    Route::get('/fines', [AdminFineController::class, 'index'])->name('fines.index');
    Route::post('/fines/{carReturn}/verify', [AdminFineController::class, 'verify'])->name('fines.verify');
    Route::post('/fines/{carReturn}/reject', [AdminFineController::class, 'reject'])->name('fines.reject');
});

// Dashboard Petugas
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Khusus Customer (role:customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Booking / Rental Customer
    Route::get('/rentals', [CustomerRentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/create', [CustomerRentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals', [CustomerRentalController::class, 'store'])->name('rentals.store');
    Route::get('/rentals/{booking}', [CustomerRentalController::class, 'show'])->name('rentals.show');
    Route::post('/rentals/{booking}/cancel', [CustomerRentalController::class, 'cancel'])->name('rentals.cancel');
    Route::post('/bookings/{booking}/cancel', [CustomerRentalController::class, 'cancel'])->name('bookings.cancel');

    // Pembayaran Customer
    Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{booking}/create', [CustomerRentalController::class, 'show'])->name('payments.create');
    Route::post('/payments/{booking}', [CustomerPaymentController::class, 'store'])->name('payments.store');

    // Pengembalian Mobil Customer
    Route::get('/returns', [CustomerRentalController::class, 'returns'])->name('returns.index');

    // Pelunasan Denda
    Route::get('/fines', [CustomerFineController::class, 'index'])->name('fines.index');
    Route::post('/fines/{booking}', [CustomerFineController::class, 'store'])->name('fines.store');

    // Ulasan (hanya untuk booking completed milik sendiri)
    Route::get('/rentals/{booking}/review', [CustomerReviewController::class, 'create'])->name('reviews.create');
    Route::post('/rentals/{booking}/review', [CustomerReviewController::class, 'store'])->name('reviews.store');
});
