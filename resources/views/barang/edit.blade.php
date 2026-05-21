@extends('layouts.app')
@section('title', 'Edit Barang')
@section('page-title', 'Edit Barang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Data Barang</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Barang</li>
@endsection

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
            <div id="fotoUploadArea" class="foto-upload-area {{ $barang->foto ? 'has-foto' : '' }} @error('foto') border-danger @enderror">
                <div id="fotoPlaceholder" style="{{ $barang->foto ? 'display:none;' : '' }}">
                    <i class="bi bi-camera fs-2 mb-2" style="color:var(--text-subtle);"></i>
                    <div style="font-size:.88rem;font-weight:600;color:var(--text-muted);">Tap untuk ganti foto</div>
                    <div style="font-size:.75rem;color:var(--text-subtle);margin-top:2px;">Galeri atau kamera · JPG, PNG, WebP</div>
                </div>
                <img id="fotoPreview" src="{{ $barang->foto ? asset('storage/' . $barang->foto) : '' }}"
                     style="{{ $barang->foto ? '' : 'display:none;' }}width:100%;max-height:200px;object-fit:contain;border-radius:8px;">
                <div id="fotoInfo" style="display:none;font-size:.75rem;color:var(--brand);margin-top:6px;text-align:center;"></div>
                @if($barang->foto)
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:6px;text-align:center;">Foto saat ini · Tap untuk ganti</div>
                @endif
            </div>
            <input type="file" name="fotoFile" id="fotoInput" accept="image/*" style="display:none;">
            <input type="hidden" name="foto_base64" id="fotoBase64">
            @error('foto')<div class="text-danger" style="font-size:.85rem;margin-top:4px;">{{ $message }}</div>@enderror
            <div id="fotoClearWrap" style="display:none;margin-top:6px;">
                <button type="button" id="fotoClear" class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Batal ganti</button>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-5">
                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                <input type="text" name="kode_barang"
                       class="form-control @error('kode_barang') is-invalid @enderror"
                       value="{{ old('kode_barang', $barang->kode_barang) }}" maxlength="100" required>
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
                   value="{{ old('nama_barang', $barang->nama_barang) }}" maxlength="255" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Merk / Brand</label>
            <input type="text" name="merk" class="form-control" value="{{ old('merk', $barang->merk) }}" maxlength="100">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-7">
                <label class="form-label">Stok Minimum</label>
                <input type="number" name="stok_minimum" class="form-control"
                       value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0" max="999999">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2" maxlength="1000">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
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

@push('styles')
<style>
.foto-upload-area {
    border: 2px dashed var(--border);
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: var(--bg);
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.foto-upload-area:hover, .foto-upload-area.dragover { border-color: var(--brand); background: var(--brand-soft, #FFF4E8); }
.foto-upload-area.has-foto { border-style: solid; border-color: var(--brand); padding: 12px; }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const area    = document.getElementById('fotoUploadArea');
    const input   = document.getElementById('fotoInput');
    const preview = document.getElementById('fotoPreview');
    const ph      = document.getElementById('fotoPlaceholder');
    const info    = document.getElementById('fotoInfo');
    const b64     = document.getElementById('fotoBase64');
    const clearWrap = document.getElementById('fotoClearWrap');

    area.addEventListener('click', () => input.click());
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragover'));
    area.addEventListener('drop', e => { e.preventDefault(); area.classList.remove('dragover'); if (e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0]); });
    input.addEventListener('change', function() { if (this.files[0]) handleFile(this.files[0]); });

    document.getElementById('fotoClear').addEventListener('click', function(e) {
        e.stopPropagation();
        b64.value = '';
        input.value = '';
        clearWrap.style.display = 'none';
        info.style.display = 'none';
        const hasOld = '{{ $barang->foto ? "1" : "0" }}' === '1';
        if (hasOld) {
            preview.src = '{{ $barang->foto ? asset('storage/' . $barang->foto) : "" }}';
            preview.style.display = '';
            ph.style.display = 'none';
        } else {
            preview.style.display = 'none';
            ph.style.display = '';
            area.classList.remove('has-foto');
        }
    });

    function handleFile(file) {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => compressImage(e.target.result, file.name);
        reader.readAsDataURL(file);
    }

    function compressImage(dataUrl, name) {
        const img = new Image();
        img.onload = function() {
            const MAX = 800;
            let w = img.width, h = img.height;
            if (w > MAX || h > MAX) {
                if (w > h) { h = Math.round(h * MAX / w); w = MAX; }
                else { w = Math.round(w * MAX / h); h = MAX; }
            }
            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            const compressed = canvas.toDataURL('image/jpeg', 0.82);
            const kb = Math.round((compressed.length * 3/4) / 1024);
            b64.value = compressed;
            preview.src = compressed;
            preview.style.display = '';
            ph.style.display = 'none';
            info.textContent = 'Foto baru: ' + name + ' · ' + kb + ' KB';
            info.style.display = '';
            clearWrap.style.display = '';
            area.classList.add('has-foto');
        };
        img.src = dataUrl;
    }
})();
</script>
@endpush
