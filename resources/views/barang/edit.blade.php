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
    <form method="POST" action="{{ route('barang.update', $barang) }}">
    @csrf @method('PUT')
    <div class="card-body p-4">

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
