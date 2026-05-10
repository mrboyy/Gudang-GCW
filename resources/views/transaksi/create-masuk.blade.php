@extends('layouts.app')
@section('title', 'Input Barang Masuk')
@section('page-title', 'Input Barang Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaksi.masuk') }}">Barang Masuk</a></li>
    <li class="breadcrumb-item active" aria-current="page">Input Masuk</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('transaksi.masuk') }}" class="btn btn-sm btn-outline-secondary" title="Kembali"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;color:var(--text);">Input Barang Masuk</h5>
        <div class="text-muted" style="font-size:.78rem;">Catat penerimaan barang ke gudang</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <span style="width:28px;height:28px;background:#ECFDF5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#059669;font-size:.95rem;flex-shrink:0;">
            <i class="bi bi-box-arrow-in-down"></i>
        </span>
        Data Penerimaan Barang
    </div>
    <form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    <input type="hidden" name="jenis_transaksi" value="masuk">
    <div class="card-body p-4">

        <div class="mb-4">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <select name="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required id="barangSelect">
                <option value="">— Pilih barang —</option>
                @foreach($barangs as $b)
                <option value="{{ $b->id }}" data-satuan="{{ $b->satuan }}" {{ old('id_barang') == $b->id ? 'selected' : '' }}>
                    {{ $b->nama_barang }} ({{ $b->kode_barang }})
                </option>
                @endforeach
            </select>
            @error('id_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                   value="{{ old('tanggal', date('Y-m-d')) }}" required>
            @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Nomor Lot <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <input type="text" name="nomor_lot" class="form-control" value="{{ old('nomor_lot') }}">
        </div>

        <div class="mb-1">
            <label class="form-label">Keterangan <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
        </div>

        <div class="mb-1 mt-3">
            <label class="form-label">Nama Supplier <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <div class="input-group">
                <select id="supplierSelect" class="form-select" style="border-radius:6px 0 0 6px;">
                    <option value="">— Pilih atau ketik baru —</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s }}" {{ old('nama_supplier') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                    <option value="__new__">+ Tambah supplier baru...</option>
                </select>
                <input type="text" name="nama_supplier" id="supplierInput"
                       class="form-control"
                       value="{{ old('nama_supplier') }}" maxlength="200"
                       placeholder="Nama pemasok / supplier"
                       style="display:none;border-radius:0 6px 6px 0;">
                <button type="button" id="supplierBack" class="btn btn-outline-secondary" style="display:none;border-radius:0 6px 6px 0;" title="Kembali ke pilihan">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('transaksi.masuk') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x me-1"></i>Batal
        </a>
        <button type="submit" class="btn btn-success px-5">
            <i class="bi bi-check-lg me-2"></i>Simpan Transaksi
        </button>
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

// Supplier dropdown logic
const supplierSelect = document.getElementById('supplierSelect');
const supplierInput  = document.getElementById('supplierInput');
const supplierBack   = document.getElementById('supplierBack');

function showSupplierInput(val) {
    supplierSelect.style.display = 'none';
    supplierInput.style.display  = '';
    supplierBack.style.display   = '';
    supplierInput.value = (val && val !== '__new__') ? val : '';
    supplierInput.focus();
}
function showSupplierSelect() {
    supplierSelect.style.display = '';
    supplierInput.style.display  = 'none';
    supplierBack.style.display   = 'none';
    supplierInput.value = '';
}

// On load: if old value exists and not in list, show input
const oldSupplier = "{{ old('nama_supplier') }}";
if (oldSupplier) {
    const opts = Array.from(supplierSelect.options).map(o => o.value);
    if (!opts.includes(oldSupplier)) {
        showSupplierInput(oldSupplier);
    } else {
        supplierSelect.value = oldSupplier;
    }
}

supplierSelect.addEventListener('change', function() {
    if (this.value === '__new__') {
        showSupplierInput('');
    } else if (this.value) {
        supplierInput.value = this.value;
    } else {
        supplierInput.value = '';
    }
});
supplierBack.addEventListener('click', showSupplierSelect);
</script>
@endpush
