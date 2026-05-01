@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('page-title', 'Tambah Barang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Data Barang</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
@endsection

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
            <div id="fotoPreviewWrap" style="display:none;margin-bottom:8px;">
                <img id="fotoPreview" style="max-width:120px;max-height:120px;border-radius:8px;border:1px solid var(--border);object-fit:cover;">
            </div>
            <input type="file" name="foto" id="fotoInput"
                   class="form-control @error('foto') is-invalid @enderror"
                   accept="image/jpeg,image/png,image/webp" capture="environment">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Maks. 2MB · JPG, PNG, WebP · Di HP bisa foto langsung atau pilih dari galeri</div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-5">
                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                <input type="text" name="kode_barang"
                       class="form-control @error('kode_barang') is-invalid @enderror"
                       value="{{ old('kode_barang') }}" maxlength="100" required>
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
                   value="{{ old('nama_barang') }}" maxlength="255" required>
            @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3 mb-3">
            <div class="col-7">
                <label class="form-label">Merk</label>
                <input type="text" name="merk" class="form-control" value="{{ old('merk') }}" maxlength="255">
            </div>
            <div class="col-5">
                <label class="form-label">Stok Minimum</label>
                <input type="number" name="stok_minimum" class="form-control"
                       value="{{ old('stok_minimum', 0) }}" min="0" max="999999">
            </div>
        </div>

        <div class="mb-1">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2" maxlength="1000">{{ old('deskripsi') }}</textarea>
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

@push('scripts')
<script>
document.getElementById('fotoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('fotoPreview').src = e.target.result;
        document.getElementById('fotoPreviewWrap').style.display = '';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
