<?php

use App\Http\Controllers\AuditLogController;
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
    Route::get('/api/last-update', [DashboardController::class, 'lastUpdate'])->name('api.last-update');

    // ── TRANSAKSI LIST & SHOW — semua role (monitoring) ──
    Route::get('/barang-masuk', [TransaksiController::class, 'masuk'])->name('transaksi.masuk');
    Route::get('/barang-keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');
    Route::get('/retur-customer', [TransaksiController::class, 'retur'])->name('retur.index');
    Route::get('/retur-produksi', [TransaksiController::class, 'returProduksi'])->name('retur.produksi.index');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/print', [TransaksiController::class, 'printView'])->name('transaksi.print');

    // ── TRANSAKSI CREATE/STORE — Operator & Admin saja ──
    Route::middleware(['role:operator,admin'])->group(function () {
        Route::get('/barang-masuk/create', [TransaksiController::class, 'createMasuk'])->name('transaksi.create-masuk');
        Route::get('/barang-keluar/create', [TransaksiController::class, 'createKeluar'])->name('transaksi.create-keluar');
        Route::get('/retur-customer/create', [TransaksiController::class, 'createRetur'])->name('retur.create');
        Route::get('/retur-produksi/create', [TransaksiController::class, 'createReturProduksi'])->name('retur.produksi.create');
        Route::get('/transaksi/cek-stok', [TransaksiController::class, 'cekStok'])->name('transaksi.cek-stok');
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
    });

    // ── LAPORAN — Kepala Gudang & Admin saja ──
    Route::middleware(['role:kepala_gudang,admin'])->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/stok', [LaporanController::class, 'stok'])->name('stok');
        Route::get('/export-stok', [LaporanController::class, 'exportStok'])->name('export-stok');
        Route::get('/print-stok', [LaporanController::class, 'printStok'])->name('print-stok');
        Route::get('/transaksi', [LaporanController::class, 'transaksi'])->name('transaksi');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/print-transaksi', [LaporanController::class, 'printTransaksi'])->name('print-transaksi');
    });

    // ── AUDIT LOG & KELOLA USER — Admin saja ──
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit.index');
        Route::resource('user', UserController::class)->except(['show']);
    });
});
