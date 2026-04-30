# Design Spec: Revisi Gudang-GCW
**Tanggal:** 2026-04-29
**Project:** Sistem Pencatatan Gudang PT Galih Cipta Wisesa
**Stack:** Laravel, Blade, MySQL (gudang_gcw), XAMPP dev

---

## 1. Perbaikan Kecil (Bagian 1)

### 1.1 Dropdown Nomor Lot (Barang Keluar)
- Form `/barang-keluar/create`: field nomor lot sudah berupa `<select>` yang diisi via AJAX dari stok tersedia
- Verifikasi: saat barang dipilih, AJAX hit `GET /transaksi/cek-stok?id_barang=X` → controller sudah return `lots[]`
- Fix jika dropdown tidak populate dengan benar

### 1.2 Hapus Semua Tanda +/-
- Hapus semua sisa `+{{ }}` dan `-{{ }}` di seluruh blade views
- Cek: `transaksi/masuk.blade.php`, `transaksi/keluar.blade.php`, `laporan/transaksi.blade.php`, `dashboard.blade.php`

### 1.3 Kurangi Simbol/Ikon di Tombol
- Tombol aksi (Simpan, Cari, Reset, Export) → teks saja, hapus `<i class="bi bi-...">` dari dalam tombol
- Ikon tetap dipertahankan: sidebar nav, stat cards, badge transaksi

---

## 2. Filter & Pencarian (Bagian 2)

### Berlaku di semua halaman berikut:
- Barang Masuk (`/barang-masuk`)
- Barang Keluar (`/barang-keluar`)
- Retur Customer (`/retur-customer`)
- Retur Produksi (`/retur-produksi`) — baru
- Laporan Transaksi (`/laporan/transaksi`)

### Komponen filter:
- **Search box**: cari berdasarkan nama barang ATAU merk (satu box, OR query)
- **Tombol periode**: `Hari Ini` | `Bulan Ini` | `Tahun Ini` (active state saat dipilih)
- **Range tanggal**: input `tanggal_dari` + `tanggal_sampai` — hanya muncul jika tidak ada periode aktif
- **Reset**: kembali ke default (tanpa filter)

### Controller: `applyFilters()` private method (sudah ada di TransaksiController) — pastikan support `merk` juga

---

## 3. Daftar Barang — Tile View + Foto (Bagian 3)

### 3.1 Database Migration
- Tambah kolom `foto` (nullable, string) ke tabel `barangs`
- Simpan path relatif: `barang/foto-namafile.jpg`

### 3.2 Storage
- Foto disimpan di `storage/app/public/barang/`
- Symlink: `php artisan storage:link`
- Ukuran maks: 2MB, format: jpg/png/webp
- Jika tidak ada foto: tampilkan placeholder icon box

### 3.3 Form Tambah/Edit Barang
- Tambah field upload foto: `<input type="file" accept="image/*" capture="environment">`
- Attribute `capture="environment"` → browser HP otomatis tawarkan kamera atau galeri
- Validasi: `nullable|image|max:2048`
- Jika edit dan tidak upload foto baru → foto lama tetap

### 3.4 Halaman Index Barang (`/barang`)
- Toggle di kanan atas: tombol `Tile` dan `Tabel` (default: Tile)
- Pilihan toggle disimpan di `localStorage` browser
- **Mode Tile**: grid 2 kolom (mobile) / 3-4 kolom (desktop)
  - Tiap card: foto/placeholder, nama barang, merk, stok total, satuan, badge aktif/nonaktif
  - Tombol Edit & Hapus di pojok card
- **Mode Tabel**: tampilan tabel existing (tidak berubah)

---

## 4. Retur Customer & Retur Produksi (Bagian 4)

### 4.1 Jenis Transaksi (enum di DB)
Sudah ada: `masuk`, `keluar`, `retur_customer`
Tambah: `retur_produksi`

Migration: `ALTER TABLE transaksis MODIFY jenis_transaksi ENUM('masuk','keluar','retur_customer','retur_produksi')`

### 4.2 Logika Stok
- `retur_customer` → stok **naik** (sudah ada)
- `retur_produksi` → stok **naik** (barang sisa produksi kembali ke gudang)

### 4.3 Kode Transaksi
- Retur Customer: `RC-YYYYMMDD-XXXX` (sudah ada)
- Retur Produksi: `RP-YYYYMMDD-XXXX` (baru)

### 4.4 Routes & Views
```
GET  /retur-produksi          → retur.produksi.index
GET  /retur-produksi/create   → retur.produksi.create
POST /transaksi               → TransaksiController@store (sudah handle semua jenis)
```

### 4.5 Menu Sidebar
```
Transaksi
  ├─ Barang Masuk
  ├─ Barang Keluar
  └─ Retur
       ├─ Retur Customer
       └─ Retur Produksi
```

### 4.6 Form Retur Produksi
- Sama seperti form Retur Customer
- Nomor lot: input text manual (boleh kosong)
- Tidak ada dropdown lot (karena ini barang dari produksi, bukan dari stok existing)

---

## 5. Laporan Stok — Accordion (Bagian 5)

### 5.1 Merge & Hapus
- Halaman `/laporan/stok-per-lot` **dihapus** dari menu dan route
- Semua informasi lot masuk ke `/laporan/stok`

### 5.2 Tampilan Accordion
```
▼  Klercide Sterile 70/30    Merk: Klercide    Total: 150 Botol   [aktif]
      Lot: 3094SW1406   →   80 Botol
      Lot: 1244SW3008   →   45 Botol
      Lot: 1284SW1208   →   25 Botol

▶  Actichlor Plus Tablet    Merk: Actichlor    Total: 200 Sachet  [aktif]
```
- Klik baris barang → expand/collapse lot di bawahnya (JavaScript toggle)
- Jika barang tidak punya nomor lot → expand tetap muncul, tampilkan "Tidak ada data lot"
- Kolom yang tampil: Nama Barang | Merk | Total Stok | Satuan | Status
- Di dalam accordion: Nomor Lot | Stok

### 5.3 Filter
- Search nama/merk
- Tombol periode (untuk barang yang ada transaksi di periode tersebut) → opsional, tetap tampil semua barang dengan stok > 0

### 5.4 Controller (`LaporanController@stok`)
- Query: `Barang::with('stoks')` (sudah ada)
- Pastikan relasi `stoks` return semua lot dengan `stok_akhir > 0`
- Hapus method `stokPerLot()` setelah merge

---

## 6. Export Excel Group By (Bagian 6)

### 6.1 Format Output
File: `.csv` dengan encoding UTF-8 BOM (agar Excel baca benar)

Struktur:
```
Nama Barang                              | Total
[Klercide Sterile 70/30]                 | 150
  Lot: 3094SW1406                        | 80
  Lot: 1244SW3008                        | 45
  Lot: 1284SW1208                        | 25
[Actichlor Plus Tablet]                  | 200
  Lot: 5295MD0110                        | 200
---
TOTAL KESELURUHAN                        | 350
```

### 6.2 Sumber Data
- Export dari halaman **Laporan Stok** (bukan laporan transaksi)
- Data: semua barang aktif, stok > 0, group by nama barang + nomor lot

### 6.3 Route
```
GET /laporan/export-stok → LaporanController@exportStok
```
Route lama `export-excel` (transaksi) tetap ada.

---

## Urutan Implementasi

1. Fix kecil (1.1–1.3) — langsung, tidak ada dependensi
2. Migration: tambah `foto` ke barangs + tambah `retur_produksi` ke enum
3. Foto barang: BarangController + form + tile view
4. Retur Produksi: route, view, sidebar
5. Filter & pencarian semua halaman
6. Laporan Stok accordion (merge + hapus stok-per-lot)
7. Export Excel stok group by

---

## File yang Akan Diubah

| File | Perubahan |
|---|---|
| `database/migrations/` | +foto barangs, +retur_produksi enum |
| `app/Http/Controllers/BarangController.php` | Handle upload foto |
| `app/Http/Controllers/TransaksiController.php` | +retur_produksi support |
| `app/Http/Controllers/LaporanController.php` | Accordion stok, export stok |
| `app/Models/Barang.php` | Tambah `foto` ke fillable |
| `routes/web.php` | +retur produksi routes, +export-stok |
| `resources/views/barang/index.blade.php` | Tile view + toggle |
| `resources/views/barang/create.blade.php` | Form foto |
| `resources/views/barang/edit.blade.php` | Form foto |
| `resources/views/laporan/stok.blade.php` | Accordion |
| `resources/views/laporan/stok-per-lot.blade.php` | **Dihapus** |
| `resources/views/layouts/app.blade.php` | Menu retur produksi |
| `resources/views/retur/` | +create-produksi.blade.php, +produksi.blade.php |
| `resources/views/transaksi/*.blade.php` | Fix +/-, filter update |
