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
    <div class="card-header d-flex align-items-center gap-2">
        <span style="width:28px;height:28px;background:#FFF4E8;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#E8751A;font-size:.95rem;flex-shrink:0;">
            <i class="bi bi-box-arrow-up"></i>
        </span>
        Data Pengeluaran Barang
    </div>
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
                    {{ $b->nama_barang }} ({{ $b->kode_barang }})
                </option>
                @endforeach
            </select>
            @error('id_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div id="stokInfo" class="mt-2"></div>
        </div>

        <div class="mb-4">
            <label class="form-label">Lot <span class="text-danger">*</span></label>
            <select name="nomor_lot" id="nomorLot" class="form-select @error('nomor_lot') is-invalid @enderror" required>
                <option value="" disabled selected>— Pilih barang terlebih dahulu —</option>
            </select>
            @error('nomor_lot')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div id="lotWarning" class="mt-1" style="display:none;font-size:.78rem;color:var(--danger);">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>Stok barang ini tidak memiliki Lot. Lakukan barang masuk dengan Lot terlebih dahulu.
            </div>
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
            <label class="form-label">Tipe Pengeluaran <span class="text-danger">*</span></label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipe_tujuan" id="tipeCustomer" value="Customer" checked>
                    <label class="form-check-label" for="tipeCustomer">Customer</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="tipe_tujuan" id="tipeInternal" value="Internal">
                    <label class="form-check-label" for="tipeInternal">Internal</label>
                </div>
            </div>
        </div>

        <div class="mb-4" id="divInternalDetail" style="display:none;">
            <label class="form-label">User/Departemen <span class="text-danger">*</span></label>
            <input type="text" name="detail_internal" id="detailInternal" class="form-control">
        </div>

        <input type="hidden" name="tujuan_keluar" id="tujuanKeluar" value="Customer">

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
        <a href="{{ route('transaksi.keluar') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x me-1"></i>Batal
        </a>
        <button type="submit" class="btn btn-primary px-5" id="submitBtnKeluar">
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
const barangSelect  = document.getElementById('barangSelect');
const nomorLot      = document.getElementById('nomorLot');
const stokInfo      = document.getElementById('stokInfo');
const satuanLabel   = document.getElementById('satuanLabel');
const lotWarning    = document.getElementById('lotWarning');
const submitBtn     = document.querySelector('button[type="submit"]');

function muatLotDanStok() {
    var idBarang = barangSelect.value;
    if (!idBarang) {
        stokInfo.innerHTML = '';
        nomorLot.innerHTML = '<option value="" disabled selected>— Pilih barang terlebih dahulu —</option>';
        lotWarning.style.display = 'none';
        submitBtn.disabled = false;
        return;
    }

    var url = "{{ url('transaksi/cek-stok') }}?id_barang=" + idBarang;

    fetch(url)
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP error ' + r.status);
            return r.json();
        })
        .then(function(data) {
            satuanLabel.textContent = data.satuan || 'pcs';
            lotWarning.style.display = 'none';
            submitBtn.disabled = false;

            if (data.lots && data.lots.length > 0) {
                var opts = '<option value="" disabled selected>— Pilih Lot —</option>';
                data.lots.forEach(function(lot) {
                    opts += '<option value="' + lot.nomor_lot + '">' + lot.nomor_lot + ' (stok: ' + lot.stok_akhir + ' ' + data.satuan + ')</option>';
                });
                nomorLot.innerHTML = opts;
                nomorLot.disabled = false;
            } else if (data.stok > 0) {
                nomorLot.innerHTML = '<option value="" disabled selected>— Stok tidak memiliki Lot —</option>';
                lotWarning.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                nomorLot.innerHTML = '<option value="" disabled selected>— Stok kosong —</option>';
            }
            tampilStok(data.stok, data.satuan, '');
        })
        .catch(function(err) {
            stokInfo.innerHTML = '<div class="text-danger small">Gagal memuat data stok.</div>';
        });
}

nomorLot.addEventListener('change', function() {
    var idBarang = barangSelect.value;
    var lot = this.value;
    if (!idBarang || !lot) return;
    var url = "{{ url('transaksi/cek-stok') }}?id_barang=" + idBarang + "&nomor_lot=" + encodeURIComponent(lot);
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) { tampilStok(data.stok, data.satuan, lot); })
        .catch(function() {});
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

// Logika Tipe Pengeluaran
const tipeCustomer   = document.getElementById('tipeCustomer');
const tipeInternal   = document.getElementById('tipeInternal');
const divInternal    = document.getElementById('divInternalDetail');
const detailInternal = document.getElementById('detailInternal');
const tujuanKeluar   = document.getElementById('tujuanKeluar');

function updateTujuan() {
    if (tipeInternal.checked) {
        divInternal.style.display = 'block';
        detailInternal.setAttribute('required', 'required');
        tujuanKeluar.value = 'Internal: ' + detailInternal.value;
    } else {
        divInternal.style.display = 'none';
        detailInternal.removeAttribute('required');
        tujuanKeluar.value = 'Customer';
    }
}

tipeCustomer.addEventListener('change', updateTujuan);
tipeInternal.addEventListener('change', updateTujuan);
detailInternal.addEventListener('input', updateTujuan);

document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtnKeluar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>
@endpush
