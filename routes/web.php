<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Redirect root ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ==================== ROUTE UNTUK SEMUA USER YANG LOGIN (AUTH) ====================
Route::middleware(['auth'])->group(function () {

    // Dashboard - Redirect berdasarkan role
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    // ==================== USER DASHBOARD ====================
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/bookings', [UserDashboardController::class, 'bookings'])->name('user.bookings');
    Route::get('/user/mybookings', [UserDashboardController::class, 'mybookings'])->name('user.mybookings');

    // ==================== LAPANGAN (user bisa lihat) ====================
    Route::get('/lapangans', [LapanganController::class, 'index'])->name('lapangans.index');
    Route::get('/lapangans/{lapangan}', [LapanganController::class, 'show'])->name('lapangans.show');
    Route::get('/cek-ketersediaan', [LapanganController::class, 'cekKetersediaan'])->name('lapangans.cek-ketersediaan');

    // ==================== BOOKING (user bisa CRUD booking sendiri) ====================
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // PENTING: route dengan path literal harus SEBELUM route dengan parameter {booking}
    Route::get('/bookings/available-times', [BookingController::class, 'getAvailableTimes'])->name('bookings.available-times');
    Route::get('/bookings-export/csv', [BookingController::class, 'exportCSV'])->name('bookings.export-csv');

    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');   // <-- DITAMBAHKAN
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{booking}/print', [BookingController::class, 'printInvoice'])->name('bookings.print');

    // ==================== PROFILE ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==================== ROUTE UNTUK ADMIN SAJA ====================
Route::middleware(['auth', 'admin'])->group(function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // ==================== MANAJEMEN LAPANGAN (ADMIN) ====================
    Route::resource('admin/lapangans', LapanganController::class)->except(['index', 'show']);
    Route::post('/admin/lapangans/{lapangan}/update-status', [LapanganController::class, 'updateStatus'])->name('admin.lapangans.update-status');
    Route::get('/admin/cek-ketersediaan', [LapanganController::class, 'cekKetersediaan'])->name('admin.lapangans.cek-ketersediaan');
    Route::post('/lapangans/{lapangan}/update-status', [LapanganController::class, 'updateStatus'])->name('lapangans.update-status');

    // ==================== MANAJEMEN PELANGGAN (ADMIN) ====================
    Route::resource('pelanggans', PelangganController::class);
    Route::get('/pelanggans/cari/select', [PelangganController::class, 'cariForSelect'])->name('pelanggans.cari-select');

    // ==================== MANAJEMEN BOOKING (ADMIN) ====================
    Route::get('/admin/bookings', [BookingController::class, 'adminIndex'])->name('admin.bookings.index');
    Route::get('/admin/bookings/{booking}', [BookingController::class, 'adminShow'])->name('admin.bookings.show');
    Route::put('/admin/bookings/{booking}', [BookingController::class, 'adminUpdate'])->name('admin.bookings.update');

    // ==================== MANAJEMEN PEMBAYARAN (ADMIN) ====================
    Route::resource('pembayarans', PembayaranController::class);
    Route::post('/pembayarans/{pembayaran}/verify', [PembayaranController::class, 'verify'])->name('pembayarans.verify');
    Route::post('/pembayarans/{pembayaran}/reject', [PembayaranController::class, 'reject'])->name('pembayarans.reject');
    Route::get('/payment-statistics', [PembayaranController::class, 'getStatistics'])->name('pembayarans.statistics');
    Route::get('/pembayaran-export/csv', [PembayaranController::class, 'exportCSV'])->name('pembayarans.export-csv');
});

// Auth routes (bawaan Laravel)
require __DIR__.'/auth.php';
