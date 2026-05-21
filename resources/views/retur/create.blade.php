@extends('layouts.app')
@section('title', 'Catat Retur Customer')
@section('page-title', 'Retur Customer')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaksi.keluar') }}">Barang Keluar</a></li>
    <li class="breadcrumb-item active" aria-current="page">Input Retur</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('transaksi.keluar') }}" class="btn btn-sm btn-outline-secondary" title="Kembali"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;color:var(--text);">Catat Retur Customer</h5>
        <div class="text-muted" style="font-size:.78rem;">Barang yang dikembalikan customer masuk kembali ke stok gudang</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card">
    <div class="card-header">
        Data Barang yang Dikembalikan
    </div>
    <form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    <input type="hidden" name="jenis_transaksi" value="retur_customer">
    <div class="card-body p-4">

        {{-- No. Ref Transaksi Keluar Customer --}}
        <div class="mb-4">
            <label class="form-label">No. Surat Jalan <span class="text-danger">*</span></label>
            <select name="no_ref" class="form-select" id="refSelect" required>
                <option value="" disabled selected>— Pilih No. Surat Jalan —</option>
                @foreach($transaksisKeluar as $tk)
                @php $sudahRetur = $returSums[$tk->no_surat_jalan] ?? 0; $sisa = $tk->quantity - $sudahRetur; @endphp
                <option value="{{ $tk->no_surat_jalan }}"
                        data-barang-id="{{ $tk->id_barang }}"
                        data-lot="{{ $tk->nomor_lot }}"
                        data-qty="{{ $tk->quantity }}"
                        data-sudah-retur="{{ $sudahRetur }}"
                        data-sisa="{{ $sisa }}"
                        data-satuan="{{ $tk->barang->satuan }}"
                        {{ request('no_ref') == $tk->no_surat_jalan ? 'selected' : '' }}>
                    {{ $tk->no_surat_jalan }} — {{ $tk->barang->nama_barang }} (sisa: {{ $sisa }} {{ $tk->barang->satuan }})
                </option>
                @endforeach
            </select>
            <div class="text-muted mt-1" style="font-size:.75rem;">Hanya menampilkan barang keluar ke Customer. Pilih No. SJ untuk mengisi barang &amp; lot otomatis.</div>
        </div>

        {{-- Nama Barang --}}
        <div class="mb-4">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <select name="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required id="barangSelect"
                    style="background:var(--bg);pointer-events:none;">
                <option value="">— Pilih No. Ref terlebih dahulu —</option>
                @foreach($barangs as $b)
                <option value="{{ $b->id }}" data-satuan="{{ $b->satuan }}" {{ old('id_barang') == $b->id ? 'selected' : '' }}>
                    {{ $b->nama_barang }} ({{ $b->kode_barang }})
                </option>
                @endforeach
            </select>
            @error('id_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Lot <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <input type="text" name="nomor_lot" id="lotInput" class="form-control @error('nomor_lot') is-invalid @enderror"
                   value="{{ old('nomor_lot') }}" readonly style="background:var(--bg);">
            @error('nomor_lot')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" name="quantity" id="qtyInput"
                       class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity') }}" min="1" required>
                <span class="input-group-text" id="satuanLabel">pcs</span>
            </div>
            <div id="refInfo" class="mt-1 text-info" style="font-size:.75rem;display:none;"></div>
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
        <a href="{{ route('transaksi.keluar') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary px-5" id="submitBtnRetur">Simpan Retur</button>
    </div>
    </form>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
const refSelect    = document.getElementById('refSelect');
const barangSelect = document.getElementById('barangSelect');
const lotInput     = document.getElementById('lotInput');
const qtyInput     = document.getElementById('qtyInput');
const satuanLabel  = document.getElementById('satuanLabel');
const refInfo      = document.getElementById('refInfo');

function updateSatuan() {
    const opt = barangSelect.options[barangSelect.selectedIndex];
    satuanLabel.textContent = opt.dataset.satuan || 'pcs';
}

refSelect.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (!opt.value) {
        barangSelect.style.background    = 'var(--bg)';
        barangSelect.style.pointerEvents = 'none';
        lotInput.readOnly                = true;
        lotInput.style.background        = 'var(--bg)';
        refInfo.style.display            = 'none';
        qtyInput.removeAttribute('max');
        return;
    }
    barangSelect.value               = opt.dataset.barangId;
    barangSelect.style.background    = 'var(--bg)';
    barangSelect.style.pointerEvents = 'none';
    updateSatuan();

    lotInput.value            = (opt.dataset.lot && opt.dataset.lot !== 'null') ? opt.dataset.lot : '';
    lotInput.readOnly         = true;
    lotInput.style.background = 'var(--bg)';

    const sisa        = parseInt(opt.dataset.sisa);
    const sudahRetur  = parseInt(opt.dataset.sudahRetur || 0);
    qtyInput.value = sisa;
    qtyInput.max   = sisa;

    const infoExtra = sudahRetur > 0 ? ` | Sudah diretur: ${sudahRetur}` : '';
    refInfo.textContent   = `Sisa quota retur: ${sisa} ${opt.dataset.satuan}${infoExtra}`;
    refInfo.style.display = 'block';
});

barangSelect.addEventListener('change', updateSatuan);
updateSatuan();

if (refSelect.value) {
    refSelect.dispatchEvent(new Event('change'));
}

document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtnRetur');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
});
</script>
@endpush
