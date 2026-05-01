# Sistem Manajemen Gudang — PT Galih Cipta Wisesa

Aplikasi pencatatan gudang berbasis web untuk PT Galih Cipta Wisesa (GCW).

## Tech Stack

- Laravel 11 / PHP 8.2
- MySQL 8
- Bootstrap 5.3

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+

## Setup Development

1. Clone repo dan install dependencies:
   ```bash
   git clone <repo-url>
   cd gudang-gcw
   composer install
   ```

2. Copy `.env.example` ke `.env` dan sesuaikan konfigurasi:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Setup database:
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```

4. Jalankan server:
   ```bash
   php artisan serve
   ```

5. Akses: `http://localhost:8000`

## Akses Default

| Username | Password | Role |
|----------|----------|------|
| admin | Admin@1234 | Administrator |

## Roles

| Role | Akses |
|------|-------|
| admin | Semua fitur + kelola user + audit log |
| kepala_gudang | Laporan + void transaksi + kelola stok |
| operator | Input transaksi saja |

## Jenis Transaksi

| Kode | Jenis | Prefix |
|------|-------|--------|
| masuk | Barang masuk dari supplier | BM- |
| keluar | Barang keluar ke customer/produksi | BK- |
| retur_customer | Retur dari customer | RC- |
| retur_produksi | Retur dari produksi | RP- |

## Menjalankan Test

```bash
php artisan test
```

## Storage

Foto produk disimpan di `storage/app/public/barang/`. Pastikan symlink sudah dibuat dengan `php artisan storage:link`.
