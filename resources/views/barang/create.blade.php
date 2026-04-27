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
    <div class="card-header">
        <i class="bi bi-archive me-2" style="color:var(--text-muted);"></i>
        <span>Informasi Barang</span>
    </div>
    <form method="POST" action="{{ route('barang.store') }}">
    @csrf
    <div class="card-body p-4">

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
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-check2 me-1"></i>Simpan
        </button>
    </div>
    </form>
</div>

</div>
</div>
@endsection
