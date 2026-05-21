<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:3,1')->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Dashboard — semua role
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/last-update', [DashboardController::class, 'lastUpdate'])->name('api.last-update');

    // ── TRANSAKSI LIST & SHOW — semua role (monitoring) ──
    Route::get('/barang-masuk', [TransaksiController::class, 'masuk'])->name('transaksi.masuk');
    Route::get('/riwayat-barang-keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');
    Route::get('/retur-customer', [TransaksiController::class, 'retur'])->name('retur.index');
    Route::get('/retur-produksi', [TransaksiController::class, 'returProduksi'])->name('retur.produksi.index');
    
    // Cek Stok harus di atas detail transaksi agar tidak bentrok (404)
    Route::get('/transaksi/cek-stok', [TransaksiController::class, 'cekStok'])->middleware('throttle:60,1')->name('transaksi.cek-stok');
    
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/print', [TransaksiController::class, 'printView'])->name('transaksi.print');

    Route::middleware(['role:operator,kepala_gudang,admin'])->group(function () {
        Route::get('/transaksi/{transaksi}/edit', [TransaksiController::class, 'edit'])->name('transaksi.edit');
        Route::patch('/transaksi/{transaksi}', [TransaksiController::class, 'update'])->name('transaksi.update');
    });

    // ── TRANSAKSI CREATE/STORE — Operator, Kepala Gudang, & Admin ──
    Route::middleware(['role:operator,kepala_gudang,admin'])->group(function () {
        Route::get('/barang-masuk/create', [TransaksiController::class, 'createMasuk'])->name('transaksi.create-masuk');
        Route::get('/barang-keluar/create', [TransaksiController::class, 'createKeluar'])->name('transaksi.create-keluar');
        Route::get('/retur/create', [TransaksiController::class, 'createReturCombined'])->name('retur.create-combined');
        Route::get('/retur-customer/create', [TransaksiController::class, 'createRetur'])->name('retur.create');
        Route::get('/retur-produksi/create', [TransaksiController::class, 'createReturProduksi'])->name('retur.produksi.create');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    });

    // ── VOID — Kepala Gudang & Admin (pengawasan) ──
    Route::middleware(['role:kepala_gudang,admin'])->group(function () {
        Route::post('/transaksi/{transaksi}/void', [TransaksiController::class, 'void'])->name('transaksi.void');
    });

    // ── DATA BARANG — semua role (view) ──
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');

    // ── KELOLA BARANG — Admin saja ──
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::patch('/barang/{barang}/toggle-active', [BarangController::class, 'toggleActive'])->name('barang.toggle-active');
    });

    // ── LAPORAN TRANSAKSI — semua role termasuk Operator (hanya view) ──
    Route::middleware(['role:operator,kepala_gudang,admin'])->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/transaksi', [LaporanController::class, 'transaksi'])->name('transaksi');
        Route::get('/print-transaksi', [LaporanController::class, 'printTransaksi'])->name('print-transaksi');
    });

    // ── LAPORAN STOK & EXPORT — Kepala Gudang & Admin saja ──
    Route::middleware(['role:kepala_gudang,admin'])->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
        Route::get('/export-stok', [LaporanController::class, 'exportStok'])->middleware('throttle:10,1')->name('export-stok');
        Route::get('/print-stok', [LaporanController::class, 'printStok'])->name('print-stok');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->middleware('throttle:10,1')->name('export-excel');
    });

    // ── AUDIT LOG, KELOLA USER & SUPPLIER — Admin saja ──
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
        Route::resource('user', UserController::class)->except(['show']);
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
        Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
        Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    });
});
