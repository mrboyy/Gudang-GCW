<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Dashboard — semua role
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── TRANSAKSI (Operator, Kepala Gudang, Admin) ──
    Route::get('/barang-masuk', [TransaksiController::class, 'masuk'])->name('transaksi.masuk');
    Route::get('/barang-masuk/create', [TransaksiController::class, 'createMasuk'])->name('transaksi.create-masuk');
    Route::get('/barang-keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');
    Route::get('/barang-keluar/create', [TransaksiController::class, 'createKeluar'])->name('transaksi.create-keluar');

    // Cek stok AJAX — harus SEBELUM {transaksi}
    Route::get('/transaksi/cek-stok', [TransaksiController::class, 'cekStok'])->name('transaksi.cek-stok');

    // ── RETUR CUSTOMER ──
    Route::get('/retur-customer', [TransaksiController::class, 'retur'])->name('retur.index');
    Route::get('/retur-customer/create', [TransaksiController::class, 'createRetur'])->name('retur.create');

    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');

    // ── DATA BARANG ──
    // Lihat daftar barang — semua role
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');

    // Kelola barang — Kepala Gudang & Admin saja
    Route::middleware(['role:kepala_gudang,admin'])->group(function () {
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
    });

    // ── LAPORAN — Kepala Gudang & Admin saja ──
    Route::middleware(['role:kepala_gudang,admin'])->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
        Route::get('/stok-per-lot', [LaporanController::class, 'stokPerLot'])->name('stok-per-lot');
        Route::get('/transaksi', [LaporanController::class, 'transaksi'])->name('transaksi');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
    });

    // ── KELOLA USER — Admin saja ──
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('user', UserController::class)->except(['show']);
    });
});
