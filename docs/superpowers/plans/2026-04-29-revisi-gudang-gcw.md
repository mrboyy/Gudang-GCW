# Revisi Gudang-GCW Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Perbaiki dan tambah fitur gudang-gcw: quick fixes, tile view barang + foto, retur produksi, laporan stok accordion, dan export Excel yang rapi.

**Architecture:** Semua perubahan di dalam Laravel MVC existing. Tambah 1 migration (foto + retur_produksi enum), modifikasi controller & blade views yang sudah ada, tambah views baru untuk retur produksi.

**Tech Stack:** Laravel 11, Blade, MySQL (XAMPP, port 3307, DB: gudang_gcw), Bootstrap 5, vanilla JS

---

## Task 1: Perbaikan Kecil — Hapus +/-, Icon Tombol, Fix Dropdown Lot

**Files:**
- Modify: `resources/views/transaksi/masuk.blade.php`
- Modify: `resources/views/transaksi/keluar.blade.php`
- Modify: `resources/views/laporan/transaksi.blade.php`
- Modify: `resources/views/barang/index.blade.php`
- Modify: `resources/views/barang/create.blade.php`
- Modify: `resources/views/barang/edit.blade.php`
- Modify: `resources/views/laporan/stok.blade.php`

- [ ] **Step 1: Hapus ikon dari tombol Cari/Simpan di `transaksi/masuk.blade.php`**

Buka `C:\xampp\htdocs\gudang-gcw\resources\views\transaksi\masuk.blade.php`.

Cari baris yang ada `<button type="submit" class="btn btn-primary flex-fill">Cari</button>` — pastikan tidak ada `<i class="bi bi-...">` di dalamnya. Jika masih ada, hapus tag `<i>` dari dalam tombol Cari.

Juga pastikan di kolom qty tidak ada tanda `+`. Cari baris seperti:
```
{{ $t->quantity }}
```
Pastikan sudah tidak ada `+` di depannya.

- [ ] **Step 2: Hapus ikon dari tombol di `transaksi/keluar.blade.php`**

Buka `C:\xampp\htdocs\gudang-gcw\resources\views\transaksi\keluar.blade.php`.

Pastikan di kolom qty tidak ada tanda `-`. Cari dan hapus jika ada:
```
-{{ $t->quantity }}
```
Ganti menjadi:
```
{{ $t->quantity }}
```

- [ ] **Step 3: Hapus tanda +/- di `laporan/transaksi.blade.php`**

Buka `C:\xampp\htdocs\gudang-gcw\resources\views\laporan\transaksi.blade.php`.

Cari semua `+{{ $transaksis->where(` dan `{{ $transaksis->where(` — pastikan tidak ada tanda +/- di depan angka di summary cards dan di kolom qty tabel.

Temukan baris yang ada `{{ $t->quantity }}` di kolom tabel — pastikan tidak ada tanda +/- di depannya.

- [ ] **Step 4: Hapus ikon dari tombol Cari di `barang/index.blade.php`**

Buka `C:\xampp\htdocs\gudang-gcw\resources\views\barang\index.blade.php`.

Temukan:
```blade
<button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Cari</button>
```
Ganti menjadi:
```blade
<button type="submit" class="btn btn-primary flex-fill">Cari</button>
```

- [ ] **Step 5: Hapus ikon dari tombol Simpan di `barang/create.blade.php`**

Temukan:
```blade
<button type="submit" class="btn btn-primary px-5">
    <i class="bi bi-check2 me-1"></i>Simpan
</button>
```
Ganti menjadi:
```blade
<button type="submit" class="btn btn-primary px-5">Simpan</button>
```

- [ ] **Step 6: Hapus ikon dari tombol di `barang/edit.blade.php`**

Temukan:
```blade
<button type="submit" class="btn btn-primary px-5">
    <i class="bi bi-check2 me-1"></i>Simpan Perubahan
</button>
```
Ganti menjadi:
```blade
<button type="submit" class="btn btn-primary px-5">Simpan Perubahan</button>
```

Juga di `card-header` di edit.blade.php, hapus baris `<i class="bi bi-pencil me-2" ...></i>`:
Temukan:
```blade
<div class="card-header">
    <i class="bi bi-pencil me-2" style="color:var(--text-muted);"></i>
    <span>Informasi Barang</span>
</div>
```
Ganti menjadi:
```blade
<div class="card-header">Informasi Barang</div>
```

- [ ] **Step 7: Hapus ikon dari tombol di `laporan/stok.blade.php`**

Temukan:
```blade
<button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Cari</button>
```
Ganti menjadi:
```blade
<button type="submit" class="btn btn-primary flex-fill">Cari</button>
```

- [ ] **Step 8: Verifikasi dropdown lot sudah bekerja**

Buka browser, login sebagai operator/admin, buka `/barang-keluar/create`, pilih barang yang punya stok. Pastikan dropdown nomor lot muncul dan terisi otomatis.

Jika dropdown lot kosong padahal ada stok, buka `C:\xampp\htdocs\gudang-gcw\resources\views\transaksi\create-keluar.blade.php` dan pastikan ada `<select name="nomor_lot" id="nomorLot">` dan script `muatLotDanStok()`.

- [ ] **Step 9: Commit**

```bash
cd C:\xampp\htdocs\gudang-gcw
git add resources/views/transaksi/masuk.blade.php resources/views/transaksi/keluar.blade.php resources/views/laporan/transaksi.blade.php resources/views/barang/index.blade.php resources/views/barang/create.blade.php resources/views/barang/edit.blade.php resources/views/laporan/stok.blade.php
git commit -m "fix: hapus tanda +/- dan ikon dari tombol"
```

---

## Task 2: Migration — Foto Barang + Retur Produksi Enum

**Files:**
- Create: `database/migrations/2026_04_29_000001_add_foto_to_barangs_and_retur_produksi_enum.php`

- [ ] **Step 1: Buat file migration**

Buat file baru `C:\xampp\htdocs\gudang-gcw\database\migrations\2026_04_29_000001_add_foto_to_barangs_and_retur_produksi_enum.php` dengan isi:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('deskripsi');
        });

        DB::statement("ALTER TABLE transaksis MODIFY COLUMN jenis_transaksi ENUM('masuk','keluar','retur_customer','retur_produksi') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('foto');
        });

        DB::statement("ALTER TABLE transaksis MODIFY COLUMN jenis_transaksi ENUM('masuk','keluar','retur_customer') NOT NULL");
    }
};
```

- [ ] **Step 2: Jalankan migration**

```bash
cd C:\xampp\htdocs\gudang-gcw
php artisan migrate
```

Expected output:
```
Running migrations.
2026_04_29_000001_add_foto_to_barangs_and_retur_produksi_enum ............. DONE
```

- [ ] **Step 3: Buat storage link**

```bash
php artisan storage:link
```

Expected output: `The [public/storage] link has been connected to [storage/app/public].`

Jika sudah ada link: `The [public/storage] link already exists.` — itu normal, tidak masalah.

- [ ] **Step 4: Commit**

```bash
git add database/migrations/2026_04_29_000001_add_foto_to_barangs_and_retur_produksi_enum.php
git commit -m "feat: migration foto barang dan enum retur_produksi"
```

---

## Task 3: Barang Model + Controller — Upload Foto

**Files:**
- Modify: `app/Models/Barang.php`
- Modify: `app/Http/Controllers/BarangController.php`

- [ ] **Step 1: Update Barang model — tambah foto ke fillable**

Buka `C:\xampp\htdocs\gudang-gcw\app\Models\Barang.php`.

Ganti baris:
```php
protected $fillable = ['kode_barang', 'nama_barang', 'merk', 'satuan', 'stok_minimum', 'deskripsi', 'is_active'];
```
Menjadi:
```php
protected $fillable = ['kode_barang', 'nama_barang', 'merk', 'satuan', 'stok_minimum', 'deskripsi', 'is_active', 'foto'];
```

- [ ] **Step 2: Update BarangController — ganti seluruh isi file**

Ganti seluruh isi `C:\xampp\htdocs\gudang-gcw\app\Http\Controllers\BarangController.php` dengan:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_barang', 'like', "%{$request->search}%")
                  ->orWhere('kode_barang', 'like', "%{$request->search}%")
                  ->orWhere('merk', 'like', "%{$request->search}%");
            });
        }
        $barangs = $query->orderBy('nama_barang')->paginate(24)->withQueryString();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'  => 'required|unique:barangs,kode_barang',
            'nama_barang'  => 'required',
            'merk'         => 'required',
            'satuan'       => 'required',
            'stok_minimum' => 'required|integer|min:0',
            'foto'         => 'nullable|image|max:2048',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi',
            'kode_barang.unique'   => 'Kode barang sudah digunakan',
            'nama_barang.required' => 'Nama barang wajib diisi',
            'merk.required'        => 'Merk wajib diisi',
            'foto.image'           => 'File harus berupa gambar',
            'foto.max'             => 'Ukuran foto maksimal 2MB',
        ]);

        $data = $request->only(['kode_barang', 'nama_barang', 'merk', 'satuan', 'stok_minimum', 'deskripsi']);
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        Barang::create($data);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang'  => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang'  => 'required',
            'merk'         => 'required',
            'satuan'       => 'required',
            'stok_minimum' => 'required|integer|min:0',
            'foto'         => 'nullable|image|max:2048',
        ], [
            'foto.image' => 'File harus berupa gambar',
            'foto.max'   => 'Ukuran foto maksimal 2MB',
        ]);

        $data = [
            'kode_barang'  => $request->kode_barang,
            'nama_barang'  => $request->nama_barang,
            'merk'         => $request->merk,
            'satuan'       => $request->satuan,
            'stok_minimum' => $request->stok_minimum ?? 0,
            'deskripsi'    => $request->deskripsi,
            'is_active'    => $request->boolean('is_active'),
        ];

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $barang->update($data);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        $barang->update(['is_active' => false]);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dinonaktifkan');
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Models/Barang.php app/Http/Controllers/BarangController.php
git commit -m "feat: barang controller support upload foto"
```

---

## Task 4: Barang Form — Tambah Field Foto

**Files:**
- Modify: `resources/views/barang/create.blade.php`
- Modify: `resources/views/barang/edit.blade.php`

- [ ] **Step 1: Update `create.blade.php` — tambah enctype dan field foto**

Ganti seluruh isi `C:\xampp\htdocs\gudang-gcw\resources\views\barang\create.blade.php` dengan:

```blade
@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('page-title', 'Tambah Barang')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('barang.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;">Tambah Barang Baru</h5>
        <div style="font-size:.78rem;color:var(--text-muted);">Daftarkan barang baru ke sistem gudang</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card">
    <div class="card-header">Informasi Barang</div>
    <form method="POST" action="{{ route('barang.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-body p-4">

        {{-- Foto --}}
        <div class="mb-3">
            <label class="form-label">Foto Barang <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                   accept="image/*" capture="environment">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div style="font-size:.78rem;color:var(--text-muted);margin-top:4px;">Maks. 2MB. Di HP bisa langsung foto atau pilih dari galeri.</div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-5">
                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                <input type="text" name="kode_barang"
                       class="form-control @error('kode_barang') is-invalid @enderror"
                       value="{{ old('kode_barang') }}" required>
                @error('kode_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-7">
                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                <select name="satuan" class="form-select @error('satuan') is-invalid @enderror" required>
                    @foreach(['pcs','Liter','Kg','Box','Botol','Unit','Karton','Lusin','Set','Lembar'] as $s)
                    <option value="{{ $s }}" {{ old('satuan') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang"
                   class="form-control @error('nama_barang') is-invalid @enderror"
                   value="{{ old('nama_barang') }}" required>
            @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-7">
                <label class="form-label">Merk</label>
                <input type="text" name="merk" class="form-control" value="{{ old('merk') }}">
            </div>
            <div class="col-5">
                <label class="form-label">Stok Minimum</label>
                <input type="number" name="stok_minimum" class="form-control"
                       value="{{ old('stok_minimum', 0) }}" min="0">
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
        </div>

    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary px-5">Simpan</button>
    </div>
    </form>
</div>

</div>
</div>
@endsection
```

- [ ] **Step 2: Update `edit.blade.php` — tambah enctype dan field foto**

Ganti seluruh isi `C:\xampp\htdocs\gudang-gcw\resources\views\barang\edit.blade.php` dengan:

```blade
@extends('layouts.app')
@section('title', 'Edit Barang')
@section('page-title', 'Edit Barang')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('barang.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;">Edit Barang</h5>
        <div style="font-size:.78rem;color:var(--text-muted);">{{ $barang->kode_barang }} · {{ $barang->nama_barang }}</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- Stok info strip --}}
<div class="d-flex align-items-center gap-3 p-3 mb-3 rounded" style="background:var(--surface);border:1px solid var(--border);">
    <div>
        <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Stok Saat Ini</div>
        <div class="fw-bold" style="font-size:1.4rem;letter-spacing:-.02em;color:{{ $barang->isStokMinimum() ? 'var(--danger)' : 'var(--success)' }};">
            {{ $barang->getStok() }} <span style="font-size:.9rem;font-weight:500;">{{ $barang->satuan }}</span>
        </div>
    </div>
    @if($barang->isStokMinimum())
    <span class="ms-auto badge" style="background:var(--danger-soft);color:var(--danger);border:1px solid #FCA5A5;font-size:.78rem;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>Hampir Habis
    </span>
    @else
    <span class="ms-auto badge" style="background:var(--success-soft);color:var(--success);border:1px solid #A7F3D0;font-size:.78rem;">
        <i class="bi bi-check-circle-fill me-1"></i>Normal
    </span>
    @endif
</div>

<div class="card">
    <div class="card-header">Informasi Barang</div>
    <form method="POST" action="{{ route('barang.update', $barang) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card-body p-4">

        {{-- Foto --}}
        <div class="mb-3">
            <label class="form-label">Foto Barang</label>
            @if($barang->foto)
            <div class="mb-2">
                <img src="{{ Storage::url($barang->foto) }}" alt="Foto" style="max-width:120px;max-height:120px;border-radius:8px;border:1px solid var(--border);object-fit:cover;">
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Foto saat ini. Upload baru untuk mengganti.</div>
            </div>
            @endif
            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror"
                   accept="image/*" capture="environment">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-5">
                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                <input type="text" name="kode_barang"
                       class="form-control @error('kode_barang') is-invalid @enderror"
                       value="{{ old('kode_barang', $barang->kode_barang) }}" required>
                @error('kode_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-7">
                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                <select name="satuan" class="form-select" required>
                    @foreach(['pcs','Liter','Kg','Box','Botol','Unit','Karton','Lusin','Set','Lembar'] as $s)
                    <option value="{{ $s }}" {{ old('satuan', $barang->satuan) == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang"
                   class="form-control @error('nama_barang') is-invalid @enderror"
                   value="{{ old('nama_barang', $barang->nama_barang) }}" required>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-7">
                <label class="form-label">Merk</label>
                <input type="text" name="merk" class="form-control" value="{{ old('merk', $barang->merk) }}">
            </div>
            <div class="col-5">
                <label class="form-label">Stok Minimum</label>
                <input type="number" name="stok_minimum" class="form-control"
                       value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
        </div>

        <div class="pt-2" style="border-top:1px solid var(--border-soft);">
            <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                       id="isActive" {{ $barang->is_active ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive" style="font-size:.88rem;">Barang Aktif</label>
            </div>
        </div>

    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary px-5">Simpan Perubahan</button>
    </div>
    </form>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
// Preview foto sebelum upload
document.querySelector('input[name="foto"]').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        let img = document.getElementById('fotoPreview');
        if (!img) {
            img = document.createElement('img');
            img.id = 'fotoPreview';
            img.style.cssText = 'max-width:120px;max-height:120px;border-radius:8px;border:1px solid var(--border);object-fit:cover;display:block;margin-top:8px;';
            this.parentElement.appendChild(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/barang/create.blade.php resources/views/barang/edit.blade.php
git commit -m "feat: form barang support upload foto"
```

---

## Task 5: Daftar Barang — Tile View + Toggle

**Files:**
- Modify: `resources/views/barang/index.blade.php`

- [ ] **Step 1: Ganti seluruh isi `barang/index.blade.php`**

```blade
@extends('layouts.app')
@section('title', 'Daftar Barang')
@section('page-title', 'Daftar Barang')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Daftar Barang</h4>
        <p>Seluruh barang yang tersimpan di gudang.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        {{-- Toggle tile/tabel --}}
        <div class="btn-group" role="group">
            <button type="button" id="btnTile" class="btn btn-sm btn-outline-secondary" onclick="setView('tile')" title="Tampilan Tile">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button type="button" id="btnTable" class="btn btn-sm btn-outline-secondary" onclick="setView('table')" title="Tampilan Tabel">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
        <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah Barang</a>
        @endif
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama barang, kode, atau merk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                @if(request('search'))
                <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TILE VIEW --}}
<div id="viewTile">
    <div class="row g-3">
        @forelse($barangs as $b)
        @php $stokSekarang = $b->getStok(); $habis = $b->isStokMinimum(); @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <div class="barang-card h-100">
                {{-- Foto --}}
                <div class="barang-foto">
                    @if($b->foto)
                    <img src="{{ Storage::url($b->foto) }}" alt="{{ $b->nama_barang }}">
                    @else
                    <div class="barang-foto-placeholder">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    @endif
                    @if($habis)
                    <span class="barang-badge-warn">Hampir Habis</span>
                    @endif
                </div>
                <div class="barang-info">
                    <div class="barang-nama">{{ $b->nama_barang }}</div>
                    <div class="barang-merk">{{ $b->merk ?: '—' }}</div>
                    <div class="barang-stok {{ $habis ? 'text-danger' : 'text-success' }}">
                        {{ $stokSekarang }} <span style="font-size:.75rem;font-weight:500;opacity:.7;">{{ $b->satuan }}</span>
                    </div>
                    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
                    <div class="barang-aksi">
                        <a href="{{ route('barang.edit', $b) }}" class="btn btn-sm btn-outline-secondary w-100">Edit</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Tidak ada data barang</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- TABLE VIEW --}}
<div id="viewTable" style="display:none;">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Merk</th>
                            <th>Satuan</th>
                            <th class="text-end">Stok</th>
                            @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
                            <th class="text-end">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $b)
                        @php $stokSekarang = $b->getStok(); $habis = $b->isStokMinimum(); @endphp
                        <tr>
                            <td style="width:50px;">
                                @if($b->foto)
                                <img src="{{ Storage::url($b->foto) }}" style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid var(--border);" alt="">
                                @else
                                <div style="width:36px;height:36px;border-radius:6px;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-subtle);">
                                    <i class="bi bi-box-seam" style="font-size:.85rem;"></i>
                                </div>
                                @endif
                            </td>
                            <td><code class="code-tag">{{ $b->kode_barang }}</code></td>
                            <td class="fw-semibold">{{ $b->nama_barang }}</td>
                            <td class="text-muted">{{ $b->merk ?: '—' }}</td>
                            <td>{{ $b->satuan }}</td>
                            <td class="text-end">
                                <span class="fw-bold" style="color:{{ $habis ? 'var(--danger)' : 'var(--success)' }};">
                                    {{ $stokSekarang }}
                                </span>
                                @if($habis)
                                <i class="bi bi-exclamation-triangle-fill ms-1" style="color:var(--danger);font-size:.8rem;"></i>
                                @endif
                            </td>
                            @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('barang.edit', $b) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('barang.destroy', $b) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Nonaktifkan barang ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada data barang</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($barangs->hasPages())
        <div class="card-footer">{{ $barangs->links() }}</div>
        @endif
    </div>
</div>

{{-- Pagination untuk tile view --}}
<div id="tilePagination">
    @if($barangs->hasPages())
    <div class="mt-3 d-flex justify-content-center">{{ $barangs->links() }}</div>
    @endif
</div>

@push('styles')
<style>
.barang-card {
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--surface);
    box-shadow: var(--shadow-xs);
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
    display: flex;
    flex-direction: column;
}
.barang-card:hover { box-shadow: var(--shadow-sm); transform: translateY(-2px); }

.barang-foto {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    background: var(--bg);
    overflow: hidden;
}
.barang-foto img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.barang-foto-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-subtle);
    font-size: 2.5rem;
}
.barang-badge-warn {
    position: absolute;
    top: 8px; right: 8px;
    background: var(--danger);
    color: #fff;
    font-size: .62rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    letter-spacing: .03em;
}
.barang-info {
    padding: 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.barang-nama {
    font-size: .875rem;
    font-weight: 700;
    color: var(--text);
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.barang-merk {
    font-size: .75rem;
    color: var(--text-muted);
    margin-bottom: 4px;
}
.barang-stok {
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: -.02em;
    margin-top: auto;
    padding-top: 8px;
}
.barang-aksi { margin-top: 8px; }
</style>
@endpush

@push('scripts')
<script>
function setView(v) {
    localStorage.setItem('barangView', v);
    document.getElementById('viewTile').style.display  = v === 'tile'  ? '' : 'none';
    document.getElementById('viewTable').style.display = v === 'table' ? '' : 'none';
    document.getElementById('tilePagination').style.display = v === 'tile' ? '' : 'none';
    document.getElementById('btnTile').classList.toggle('btn-primary', v === 'tile');
    document.getElementById('btnTile').classList.toggle('btn-outline-secondary', v !== 'tile');
    document.getElementById('btnTable').classList.toggle('btn-primary', v === 'table');
    document.getElementById('btnTable').classList.toggle('btn-outline-secondary', v !== 'table');
}
setView(localStorage.getItem('barangView') || 'tile');
</script>
@endpush

@endsection
```

- [ ] **Step 2: Verifikasi `use Illuminate\Support\Facades\Storage` di view edit**

Di `edit.blade.php` sudah pakai `Storage::url()` — ini Blade, tidak perlu `use`. Tapi harus pastikan storage link ada (`php artisan storage:link` sudah dijalankan di Task 2).

- [ ] **Step 3: Commit**

```bash
git add resources/views/barang/index.blade.php
git commit -m "feat: daftar barang tile view + toggle + foto"
```

---

## Task 6: Retur Produksi — Controller, Routes, Views, Sidebar

**Files:**
- Modify: `app/Http/Controllers/TransaksiController.php`
- Modify: `routes/web.php`
- Create: `resources/views/retur/produksi.blade.php`
- Create: `resources/views/retur/create-produksi.blade.php`
- Modify: `resources/views/layouts/app.blade.php`

- [ ] **Step 1: Update TransaksiController — tambah retur_produksi**

Buka `C:\xampp\htdocs\gudang-gcw\app\Http\Controllers\TransaksiController.php`.

**1a.** Di method `store()`, cari:
```php
'jenis_transaksi' => 'required|in:masuk,keluar,retur_customer',
```
Ganti menjadi:
```php
'jenis_transaksi' => 'required|in:masuk,keluar,retur_customer,retur_produksi',
```

**1b.** Di method `store()`, cari:
```php
$prefixMap   = ['masuk' => 'BM', 'keluar' => 'BK', 'retur_customer' => 'RC'];
```
Ganti menjadi:
```php
$prefixMap   = ['masuk' => 'BM', 'keluar' => 'BK', 'retur_customer' => 'RC', 'retur_produksi' => 'RP'];
```

**1c.** Di method `store()`, cari:
```php
$stokJenis = ($jenis === 'retur_customer') ? 'masuk' : $jenis;
```
Ganti menjadi:
```php
$stokJenis = in_array($jenis, ['retur_customer', 'retur_produksi']) ? 'masuk' : $jenis;
```

**1d.** Di method `store()`, cari:
```php
$routeMap = ['masuk' => 'transaksi.masuk', 'keluar' => 'transaksi.keluar', 'retur_customer' => 'retur.index'];
```
Ganti menjadi:
```php
$routeMap = ['masuk' => 'transaksi.masuk', 'keluar' => 'transaksi.keluar', 'retur_customer' => 'retur.index', 'retur_produksi' => 'retur.produksi.index'];
```

**1e.** Tambah dua method baru di akhir class (sebelum `private function applyFilters`):

```php
public function createReturProduksi()
{
    $barangs = Barang::where('is_active', true)->orderBy('nama_barang')->get();
    return view('retur.create-produksi', compact('barangs'));
}

public function returProduksi(Request $request)
{
    $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'retur_produksi');
    $this->applyFilters($query, $request);
    $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
    return view('retur.produksi', compact('transaksis'));
}
```

- [ ] **Step 2: Update `routes/web.php` — tambah retur produksi**

Buka `C:\xampp\htdocs\gudang-gcw\routes\web.php`.

Cari blok:
```php
// ── RETUR CUSTOMER ──
Route::get('/retur-customer', [TransaksiController::class, 'retur'])->name('retur.index');
Route::get('/retur-customer/create', [TransaksiController::class, 'createRetur'])->name('retur.create');
```

Tambahkan DI BAWAH baris itu:
```php

// ── RETUR PRODUKSI ──
Route::get('/retur-produksi', [TransaksiController::class, 'returProduksi'])->name('retur.produksi.index');
Route::get('/retur-produksi/create', [TransaksiController::class, 'createReturProduksi'])->name('retur.produksi.create');
```

- [ ] **Step 3: Buat `resources/views/retur/produksi.blade.php`**

```blade
@extends('layouts.app')
@section('title', 'Retur Produksi')
@section('page-title', 'Retur Produksi')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Retur Produksi</h4>
        <p>Catatan barang sisa produksi yang dikembalikan ke gudang.</p>
    </div>
    <a href="{{ route('retur.produksi.create') }}" class="btn btn-primary">Catat Retur Produksi</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama barang, merk, no. transaksi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-5">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach(['hari'=>'Hari Ini','bulan'=>'Bulan Ini','tahun'=>'Tahun Ini'] as $val=>$label)
                        <a href="{{ route('retur.produksi.index', array_merge(request()->except('periode','tanggal_dari','tanggal_sampai','page'), ['periode'=>$val])) }}"
                           class="btn btn-sm {{ request('periode')===$val ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                        @endforeach
                        @if(!in_array(request('periode'),['hari','bulan','tahun']))
                        <input type="date" name="tanggal_dari" class="form-control form-control-sm" style="width:auto;" value="{{ request('tanggal_dari') }}">
                        <input type="date" name="tanggal_sampai" class="form-control form-control-sm" style="width:auto;" value="{{ request('tanggal_sampai') }}">
                        @endif
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                    @if(request()->hasAny(['search','tanggal_dari','tanggal_sampai','periode']))
                    <a href="{{ route('retur.produksi.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>No. Lot</th>
                        <th class="text-end">Jumlah</th>
                        <th>Keterangan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr>
                        <td>
                            <a href="{{ route('transaksi.show', $t) }}" class="text-decoration-none">
                                <span class="badge" style="background:var(--warning-soft);color:var(--warning);border:1px solid #FCD34D;">{{ $t->no_transaksi }}</span>
                            </a>
                        </td>
                        <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                        <td>{{ $t->nomor_lot ?: '—' }}</td>
                        <td class="text-end fw-semibold" style="color:var(--warning);">
                            {{ $t->quantity }} <small class="text-muted fw-normal">{{ $t->barang->satuan }}</small>
                        </td>
                        <td style="color:var(--text-muted);font-size:.85rem;">{{ $t->keterangan ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('transaksi.show', $t) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            <p>Belum ada catatan retur produksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transaksis->hasPages())
    <div class="card-footer">{{ $transaksis->links() }}</div>
    @endif
</div>
@endsection
```

- [ ] **Step 4: Buat `resources/views/retur/create-produksi.blade.php`**

```blade
@extends('layouts.app')
@section('title', 'Catat Retur Produksi')
@section('page-title', 'Retur Produksi')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('retur.produksi.index') }}" class="btn btn-sm btn-outline-secondary" title="Kembali"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;color:var(--text);">Catat Retur Produksi</h5>
        <div class="text-muted" style="font-size:.78rem;">Barang sisa dari produksi dikembalikan ke stok gudang</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card">
    <div class="card-header">Data Barang yang Dikembalikan dari Produksi</div>
    <form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    <input type="hidden" name="jenis_transaksi" value="retur_produksi">
    <div class="card-body p-4">

        <div class="mb-4">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <select name="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required id="barangSelect">
                <option value="">— Pilih barang —</option>
                @foreach($barangs as $b)
                <option value="{{ $b->id }}" data-satuan="{{ $b->satuan }}" {{ old('id_barang') == $b->id ? 'selected' : '' }}>
                    {{ $b->nama_barang }}{{ $b->merk ? ' — '.$b->merk : '' }}
                </option>
                @endforeach
            </select>
            @error('id_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Nomor Lot <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <input type="text" name="nomor_lot" class="form-control" value="{{ old('nomor_lot') }}">
        </div>

        <div class="mb-4">
            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" name="quantity" id="qtyInput"
                       class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity') }}" min="1" required>
                <span class="input-group-text" id="satuanLabel">pcs</span>
            </div>
            @error('quantity')<div class="text-danger" style="font-size:.82rem;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Tanggal Retur <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                   value="{{ old('tanggal', date('Y-m-d')) }}" required>
            @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-1">
            <label class="form-label">Keterangan <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
        </div>

    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('retur.produksi.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary px-5">Simpan Retur</button>
    </div>
    </form>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('barangSelect').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('satuanLabel').textContent = opt.dataset.satuan || 'pcs';
});
document.getElementById('barangSelect').dispatchEvent(new Event('change'));
</script>
@endpush
```

- [ ] **Step 5: Update sidebar di `layouts/app.blade.php` — retur produksi**

Cari di `app.blade.php`:
```blade
<a href="{{ route('retur.index') }}" class="slink {{ request()->routeIs('retur.*') ? 'active' : '' }}">
    <i class="bi bi-arrow-return-left"></i> Retur Customer
</a>
```

Ganti dengan:
```blade
<a href="{{ route('retur.index') }}" class="slink {{ request()->routeIs('retur.index','retur.create') ? 'active' : '' }}">
    <i class="bi bi-arrow-return-left"></i> Retur Customer
</a>
<a href="{{ route('retur.produksi.index') }}" class="slink {{ request()->routeIs('retur.produksi.*') ? 'active' : '' }}">
    <i class="bi bi-arrow-counterclockwise"></i> Retur Produksi
</a>
```

Juga di sidebar, hapus link `Stok Per Lot` (akan dihapus di Task 7):
Cari:
```blade
<a href="{{ route('laporan.stok-per-lot') }}" class="slink {{ request()->routeIs('laporan.stok-per-lot') ? 'active' : '' }}">
    <i class="bi bi-layers"></i> Stok Per Lot
</a>
```
Hapus baris-baris tersebut.

- [ ] **Step 6: Update `show.blade.php` — tambah support retur_produksi**

Buka `C:\xampp\htdocs\gudang-gcw\resources\views\transaksi\show.blade.php`.

Cari blok `@php` di atas dan update variabel untuk support `retur_produksi`:
```php
@php
    $jenis     = $transaksi->jenis_transaksi;
    $isMasuk   = $jenis === 'masuk';
    $isRetur   = $jenis === 'retur_customer';
    $isReturP  = $jenis === 'retur_produksi';
    $backRoute = $isMasuk ? route('transaksi.masuk') : ($isRetur ? route('retur.index') : ($isReturP ? route('retur.produksi.index') : route('transaksi.keluar')));
    $accentBg  = $isMasuk ? 'var(--success-soft)' : (($isRetur || $isReturP) ? 'var(--info-soft)' : 'var(--brand-soft)');
    $accentClr = $isMasuk ? 'var(--success)' : (($isRetur || $isReturP) ? 'var(--info)' : 'var(--brand)');
    $numClr    = $isMasuk ? 'var(--success)' : (($isRetur || $isReturP) ? 'var(--info)' : 'var(--danger)');
    $badgeBg   = $isMasuk ? 'var(--success-soft)' : (($isRetur || $isReturP) ? 'var(--info-soft)' : 'var(--brand-soft)');
    $badgeClr  = $isMasuk ? 'var(--success)' : (($isRetur || $isReturP) ? 'var(--info)' : 'var(--brand)');
    $badgeBdr  = $isMasuk ? '#A7F3D0' : (($isRetur || $isReturP) ? '#BFDBFE' : '#FDDDB5');
    $badgeIcon = $isMasuk ? 'box-arrow-in-down' : ($isRetur ? 'arrow-return-left' : ($isReturP ? 'arrow-counterclockwise' : 'box-arrow-up'));
    $badgeTxt  = $isMasuk ? 'Barang Masuk' : ($isRetur ? 'Retur Customer' : ($isReturP ? 'Retur Produksi' : 'Barang Keluar'));
@endphp
```

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/TransaksiController.php routes/web.php resources/views/retur/produksi.blade.php resources/views/retur/create-produksi.blade.php resources/views/layouts/app.blade.php resources/views/transaksi/show.blade.php
git commit -m "feat: fitur retur produksi (RP-) lengkap"
```

---

## Task 7: Laporan Stok — Accordion (Merge + Hapus Stok Per Lot)

**Files:**
- Modify: `app/Http/Controllers/LaporanController.php`
- Modify: `resources/views/laporan/stok.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Update `LaporanController@stok` — load lots**

Buka `C:\xampp\htdocs\gudang-gcw\app\Http\Controllers\LaporanController.php`.

Ganti method `stok()`:
```php
public function stok(Request $request)
{
    $query = Barang::with(['stoks' => function ($q) {
        $q->where('stok_akhir', '>', 0)->orderBy('nomor_lot');
    }])->where('is_active', true);

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('nama_barang', 'like', "%{$request->search}%")
              ->orWhere('merk', 'like', "%{$request->search}%");
        });
    }

    $barangs = $query->withSum('stoks as stok_total', 'stok_akhir')
                     ->orderBy('nama_barang')
                     ->paginate(25)
                     ->withQueryString();

    return view('laporan.stok', compact('barangs'));
}
```

Juga hapus method `stokPerLot()` sepenuhnya dari controller (cari dan hapus method tersebut).

- [ ] **Step 2: Ganti seluruh `laporan/stok.blade.php`**

```blade
@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Laporan Stok Barang</h4>
        <p>Stok seluruh barang beserta rincian per nomor lot.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.export-stok') }}" class="btn btn-outline-secondary">Export Excel</a>
        <button onclick="window.print()" class="btn btn-outline-secondary">Cetak</button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama barang atau merk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                @if(request('search'))
                <a href="{{ route('laporan.stok') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0" id="stokTable">
            <thead>
                <tr>
                    <th style="width:32px;"></th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Satuan</th>
                    <th class="text-end">Total Stok</th>
                    <th class="text-center">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $b)
                @php
                    $stok  = $b->stok_total ?? 0;
                    $habis = $stok <= $b->stok_minimum;
                    $lots  = $b->stoks;
                @endphp
                {{-- Baris utama barang --}}
                <tr class="barang-row {{ $lots->count() > 0 ? 'expandable' : '' }}"
                    onclick="{{ $lots->count() > 0 ? 'toggleLot('.$b->id.')' : '' }}"
                    style="{{ $lots->count() > 0 ? 'cursor:pointer;' : '' }}">
                    <td class="text-center" style="color:var(--text-subtle);">
                        @if($lots->count() > 0)
                        <i class="bi bi-chevron-right toggle-icon" id="icon-{{ $b->id }}" style="font-size:.75rem;transition:transform .2s;"></i>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $b->nama_barang }}</td>
                    <td class="text-muted">{{ $b->merk ?: '—' }}</td>
                    <td>{{ $b->satuan }}</td>
                    <td class="text-end fw-bold" style="color:{{ $habis ? 'var(--danger)' : 'var(--success)' }};">{{ $stok }}</td>
                    <td class="text-center">
                        @if($habis)
                        <span class="badge badge-status-warn">Hampir Habis</span>
                        @else
                        <span class="badge badge-status-ok">Normal</span>
                        @endif
                    </td>
                </tr>
                {{-- Baris lot (tersembunyi) --}}
                @foreach($lots as $lot)
                <tr class="lot-row" id="lot-{{ $b->id }}" style="display:none;">
                    <td></td>
                    <td colspan="2" style="padding-left:36px;color:var(--text-muted);font-size:.84rem;">
                        <i class="bi bi-tag me-1" style="color:var(--brand);font-size:.75rem;"></i>
                        Lot: <strong>{{ $lot->nomor_lot ?: '(tanpa lot)' }}</strong>
                    </td>
                    <td style="color:var(--text-muted);font-size:.84rem;">{{ $b->satuan }}</td>
                    <td class="text-end fw-semibold" style="color:var(--text);">{{ $lot->stok_akhir }}</td>
                    <td></td>
                </tr>
                @endforeach
                @empty
                <tr><td colspan="6" class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada data barang</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($barangs->hasPages())
    <div class="card-footer">{{ $barangs->links() }}</div>
    @endif
</div>

@push('styles')
<style>
.barang-row.expandable:hover { background: #FAFBFC; }
.lot-row td { background: #F8FAFB; border-bottom: 1px solid var(--border-soft); }
@media print {
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .btn { display: none !important; }
    .lot-row { display: table-row !important; }
}
</style>
@endpush

@push('scripts')
<script>
const openLots = new Set();

function toggleLot(id) {
    const rows = document.querySelectorAll('#lot-' + id);
    const icon = document.getElementById('icon-' + id);
    const isOpen = openLots.has(id);

    rows.forEach(r => r.style.display = isOpen ? 'none' : '');
    if (icon) icon.style.transform = isOpen ? '' : 'rotate(90deg)';

    isOpen ? openLots.delete(id) : openLots.add(id);
}
</script>
@endpush

@endsection
```

- [ ] **Step 3: Hapus route stok-per-lot dari `routes/web.php`**

Buka `routes/web.php`, cari dan hapus baris:
```php
Route::get('/stok-per-lot', [LaporanController::class, 'stokPerLot'])->name('stok-per-lot');
```

- [ ] **Step 4: Hapus file `laporan/stok-per-lot.blade.php`**

```bash
del "C:\xampp\htdocs\gudang-gcw\resources\views\laporan\stok-per-lot.blade.php"
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/LaporanController.php resources/views/laporan/stok.blade.php routes/web.php
git rm resources/views/laporan/stok-per-lot.blade.php
git commit -m "feat: laporan stok accordion per lot, hapus halaman stok-per-lot"
```

---

## Task 8: Export Excel Stok — Group By Barang + Lot

**Files:**
- Modify: `app/Http/Controllers/LaporanController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Tambah method `exportStok()` ke LaporanController**

Tambahkan method berikut di akhir class `LaporanController` (sebelum tanda `}`):

```php
public function exportStok()
{
    $barangs = Barang::with(['stoks' => function ($q) {
        $q->where('stok_akhir', '>', 0)->orderBy('nomor_lot');
    }])
    ->where('is_active', true)
    ->withSum('stoks as stok_total', 'stok_akhir')
    ->orderBy('nama_barang')
    ->get();

    Laporan::create([
        'id_user'          => Auth::id(),
        'tanggal_generate' => today(),
        'jenis_laporan'    => 'Stok Barang ' . now()->format('d/m/Y'),
        'file_path'        => null,
    ]);

    $filename = 'laporan-stok-' . now()->format('Y-m-d') . '.csv';
    $headers  = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($barangs) {
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF");

        fputcsv($file, ['Nama Barang / Nomor Lot', 'Total Stok', 'Satuan']);

        $grandTotal = 0;
        foreach ($barangs as $b) {
            $stok = $b->stok_total ?? 0;
            $grandTotal += $stok;

            fputcsv($file, [$b->nama_barang . ($b->merk ? ' (' . $b->merk . ')' : ''), $stok, $b->satuan]);

            foreach ($b->stoks as $lot) {
                fputcsv($file, ['    Lot: ' . ($lot->nomor_lot ?: '(tanpa lot)'), $lot->stok_akhir, $b->satuan]);
            }
        }

        fputcsv($file, ['']);
        fputcsv($file, ['TOTAL KESELURUHAN', $grandTotal, '']);
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
```

- [ ] **Step 2: Tambah route `export-stok` di `routes/web.php`**

Di dalam blok `Route::middleware(['role:kepala_gudang,admin'])->prefix('laporan')`, tambahkan:
```php
Route::get('/export-stok', [LaporanController::class, 'exportStok'])->name('laporan.export-stok');
```

- [ ] **Step 3: Verifikasi tombol Export Excel di `laporan/stok.blade.php`**

Pastikan sudah ada baris ini di view stok (sudah ditambahkan di Task 7):
```blade
<a href="{{ route('laporan.export-stok') }}" class="btn btn-outline-secondary">Export Excel</a>
```

- [ ] **Step 4: Test export**

Buka `/laporan/stok`, klik tombol "Export Excel". File CSV harus ter-download dengan nama `laporan-stok-YYYY-MM-DD.csv`.

Buka di Excel/WPS — harus tampil group by Nama Barang → Lot di bawahnya, dengan total di baris paling bawah.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/LaporanController.php routes/web.php
git commit -m "feat: export stok CSV group by barang dan lot"
```

---

## Task 9: Verifikasi & Final Commit

- [ ] **Step 1: Test semua fitur utama di browser**

Login sebagai admin (`http://localhost/gudang-gcw/public`):

| Fitur | Cek |
|---|---|
| Daftar Barang tile view | Tampil grid kartu dengan placeholder jika tidak ada foto |
| Toggle tile/tabel | Berganti dan mengingat pilihan |
| Tambah barang + upload foto | Foto tersimpan, tampil di tile |
| Edit barang + ganti foto | Foto lama terganti |
| Barang keluar → dropdown lot | Muncul list lot saat pilih barang |
| Retur Customer | Kode RC-, stok naik |
| Retur Produksi | Kode RP-, stok naik, halaman terpisah |
| Laporan Stok accordion | Klik baris → expand lot ke bawah |
| Export Excel stok | Download CSV, buka di Excel, group by tampil |
| Tidak ada tanda +/- di mana pun | Cek semua halaman transaksi |

- [ ] **Step 2: Pastikan sidebar bersih**

Sidebar tidak boleh ada link "Stok Per Lot" lagi. Hanya ada:
- Laporan Stok
- Laporan Transaksi

Retur:
- Retur Customer
- Retur Produksi

- [ ] **Step 3: Final commit**

```bash
cd C:\xampp\htdocs\gudang-gcw
git status
git add -A
git commit -m "feat: revisi lengkap - tile view, foto barang, retur produksi, accordion stok, export excel"
```
