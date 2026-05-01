@extends('layouts.app')
@section('title', 'Input Barang Keluar')
@section('page-title', 'Input Barang Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaksi.keluar') }}">Barang Keluar</a></li>
    <li class="breadcrumb-item active" aria-current="page">Input Keluar</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('transaksi.keluar') }}" class="btn btn-sm btn-outline-secondary" title="Kembali"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;color:var(--text);">Input Barang Keluar</h5>
        <div class="text-muted" style="font-size:.78rem;">Catat pengeluaran barang dari gudang</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card">
    <div class="card-header">Data Pengeluaran Barang</div>
    <form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    <input type="hidden" name="jenis_transaksi" value="keluar">
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
            <div id="stokInfo" class="mt-2"></div>
        </div>

        <div class="mb-4">
            <label class="form-label">Nomor Lot <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <select name="nomor_lot" id="nomorLot" class="form-select">
                <option value="">— Ambil dari semua stok —</option>
            </select>
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
            <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
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
        <a href="{{ route('transaksi.keluar') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary px-5">Simpan</button>
    </div>
    </form>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
const barangSelect = document.getElementById('barangSelect');
const nomorLot     = document.getElementById('nomorLot');
const stokInfo     = document.getElementById('stokInfo');
const satuanLabel  = document.getElementById('satuanLabel');

function muatLotDanStok() {
    const idBarang = barangSelect.value;
    if (!idBarang) {
        stokInfo.innerHTML = '';
        nomorLot.innerHTML = '<option value="">— Ambil dari semua stok —</option>';
        return;
    }
    fetch(`{{ route('transaksi.cek-stok') }}?id_barang=${idBarang}`)
        .then(r => r.json())
        .then(data => {
            satuanLabel.textContent = data.satuan || 'pcs';
            let opts = '<option value="">— Ambil dari semua stok —</option>';
            if (data.lots && data.lots.length > 0) {
                data.lots.forEach(lot => {
                    opts += `<option value="${lot.nomor_lot}">${lot.nomor_lot} (stok: ${lot.stok_akhir} ${data.satuan})</option>`;
                });
            }
            nomorLot.innerHTML = opts;
            tampilStok(data.stok, data.satuan, '');
        });
}

nomorLot.addEventListener('change', function() {
    const idBarang = barangSelect.value;
    if (!idBarang) return;
    const lot = this.value;
    fetch(`{{ route('transaksi.cek-stok') }}?id_barang=${idBarang}&nomor_lot=${encodeURIComponent(lot)}`)
        .then(r => r.json())
        .then(data => tampilStok(data.stok, data.satuan, lot));
});

function tampilStok(stok, satuan, lot) {
    document.getElementById('qtyInput').max = stok;
    const ok    = stok > 10;
    const warn  = stok > 0 && stok <= 10;
    const bg    = ok ? '#ECFDF5' : warn ? '#FFFBEB' : '#FEF2F2';
    const color = ok ? '#059669' : warn ? '#D97706' : '#DC2626';
    const icon  = ok ? 'bi-check-circle-fill' : warn ? 'bi-exclamation-triangle-fill' : 'bi-x-circle-fill';
    const label = lot ? `Stok lot <strong>${lot}</strong>` : 'Total stok tersedia';
    stokInfo.innerHTML = `
        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background:${bg};color:${color};font-size:.86rem;font-weight:600;border:1px solid ${color}22;">
            <i class="bi ${icon}"></i>
            <span>${label}: <strong>${stok} ${satuan}</strong></span>
        </div>`;
}

barangSelect.addEventListener('change', muatLotDanStok);
if (barangSelect.value) muatLotDanStok();
</script>
@endpush
