@extends('layouts.app')
@section('title', 'Catat Retur')
@section('page-title', 'Catat Retur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaksi.keluar') }}">Barang Keluar</a></li>
    <li class="breadcrumb-item active" aria-current="page">Catat Retur</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('transaksi.keluar') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;">Catat Retur Barang</h5>
        <div class="text-muted" style="font-size:.78rem;">Barang kembali masuk ke stok gudang</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

{{-- Pilihan Jenis Retur --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="form-label fw-bold mb-2" style="font-size:.82rem;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted);">Jenis Retur</div>
        <div class="d-flex gap-3">
            <label class="retur-type-card active" id="labelCustomer" for="typeCustomer">
                <input type="radio" id="typeCustomer" name="_jenis" value="customer" checked style="display:none;">
                <div class="rtc-icon"><i class="bi bi-person-check-fill"></i></div>
                <div class="rtc-label">Retur Customer</div>
                <div class="rtc-sub">Barang dikembalikan oleh customer</div>
            </label>
            <label class="retur-type-card" id="labelProduksi" for="typeProduksi">
                <input type="radio" id="typeProduksi" name="_jenis" value="produksi" style="display:none;">
                <div class="rtc-icon"><i class="bi bi-gear-fill"></i></div>
                <div class="rtc-label">Retur Produksi</div>
                <div class="rtc-sub">Barang kembali dari lini produksi</div>
            </label>
        </div>
    </div>
</div>

{{-- ══════════ FORM CUSTOMER ══════════ --}}
<div class="card" id="formCustomer">
    <div class="card-header"><i class="bi bi-person-check me-2" style="color:var(--info);"></i>Retur Customer</div>
    <form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    <input type="hidden" name="jenis_transaksi" value="retur_customer">
    <div class="card-body p-4">

        <div class="mb-4">
            <label class="form-label">No. Surat Jalan <span class="text-danger">*</span></label>
            <select name="no_ref" class="form-select" id="refSelectC" required>
                <option value="" disabled selected>— Pilih No. Surat Jalan —</option>
                @foreach($transaksisCustomer as $tk)
                @php $sr = $returSums[$tk->no_surat_jalan] ?? 0; $ss = $tk->quantity - $sr; @endphp
                <option value="{{ $tk->no_surat_jalan }}"
                        data-barang-id="{{ $tk->id_barang }}"
                        data-lot="{{ $tk->nomor_lot }}"
                        data-qty="{{ $tk->quantity }}"
                        data-sudah-retur="{{ $sr }}"
                        data-sisa="{{ $ss }}"
                        data-satuan="{{ $tk->barang->satuan }}"
                        {{ request('no_ref') == $tk->no_surat_jalan ? 'selected' : '' }}>
                    {{ $tk->no_surat_jalan }} — {{ $tk->barang->nama_barang }} (sisa: {{ $ss }} {{ $tk->barang->satuan }})
                </option>
                @endforeach
            </select>
            <div class="text-muted mt-1" style="font-size:.75rem;">Hanya menampilkan barang keluar ke Customer. Pilih No. SJ untuk mengisi barang &amp; lot otomatis.</div>
        </div>

        <div class="mb-4">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <select name="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required id="barangSelectC"
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
            <input type="text" name="nomor_lot" id="lotInputC" class="form-control"
                   value="{{ old('nomor_lot') }}" readonly style="background:var(--bg);">
        </div>

        <div class="mb-4">
            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" name="quantity" id="qtyInputC" class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity') }}" min="1" required>
                <span class="input-group-text" id="satuanLabelC">pcs</span>
            </div>
            <div id="refInfoC" class="mt-1" style="font-size:.75rem;color:var(--info);display:none;"></div>
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
        <button type="submit" class="btn btn-primary px-5"><i class="bi bi-check2 me-1"></i>Simpan Retur</button>
    </div>
    </form>
</div>

{{-- ══════════ FORM PRODUKSI ══════════ --}}
<div class="card" id="formProduksi" style="display:none;">
    <div class="card-header"><i class="bi bi-gear me-2" style="color:var(--warning);"></i>Retur Produksi</div>
    <form method="POST" action="{{ route('transaksi.store') }}">
    @csrf
    <input type="hidden" name="jenis_transaksi" value="retur_produksi">
    <div class="card-body p-4">

        <div class="mb-4">
            <label class="form-label">No. Surat Jalan <span class="text-danger">*</span></label>
            <select name="no_ref" class="form-select" id="refSelectP" required>
                <option value="" disabled selected>— Pilih No. Surat Jalan —</option>
                @foreach($transaksisInternal as $tk)
                @php $sr = $returSums[$tk->no_surat_jalan] ?? 0; $ss = $tk->quantity - $sr; @endphp
                <option value="{{ $tk->no_surat_jalan }}"
                        data-barang-id="{{ $tk->id_barang }}"
                        data-lot="{{ $tk->nomor_lot }}"
                        data-qty="{{ $tk->quantity }}"
                        data-sudah-retur="{{ $sr }}"
                        data-sisa="{{ $ss }}"
                        data-satuan="{{ $tk->barang->satuan }}"
                        {{ request('no_ref') == $tk->no_surat_jalan ? 'selected' : '' }}>
                    {{ $tk->no_surat_jalan }} — {{ $tk->barang->nama_barang }} (sisa: {{ $ss }} {{ $tk->barang->satuan }}) · {{ $tk->tujuan_keluar }}
                </option>
                @endforeach
            </select>
            <div class="text-muted mt-1" style="font-size:.75rem;">Hanya menampilkan barang keluar Internal/Departemen. Pilih No. SJ untuk mengisi barang &amp; lot otomatis.</div>
        </div>

        <div class="mb-4">
            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
            <select name="id_barang" class="form-select @error('id_barang') is-invalid @enderror" required id="barangSelectP"
                    style="background:var(--bg);pointer-events:none;">
                <option value="">— Pilih No. Ref terlebih dahulu —</option>
                @foreach($barangs as $b)
                <option value="{{ $b->id }}" data-satuan="{{ $b->satuan }}" {{ old('id_barang') == $b->id ? 'selected' : '' }}>
                    {{ $b->nama_barang }} ({{ $b->kode_barang }})
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Lot <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <input type="text" name="nomor_lot" id="lotInputP" class="form-control"
                   value="{{ old('nomor_lot') }}" readonly style="background:var(--bg);">
        </div>

        <div class="mb-4">
            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" name="quantity" id="qtyInputP" class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity') }}" min="1" required>
                <span class="input-group-text" id="satuanLabelP">pcs</span>
            </div>
            <div id="refInfoP" class="mt-1" style="font-size:.75rem;color:var(--warning);display:none;"></div>
        </div>

        <div class="mb-4">
            <label class="form-label">Tanggal Retur <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                   value="{{ old('tanggal', date('Y-m-d')) }}" required>
        </div>

        <div class="mb-1">
            <label class="form-label">Keterangan <span class="badge bg-secondary" style="font-size:.68rem;font-weight:500;">Opsional</span></label>
            <textarea name="keterangan" class="form-control" rows="2" maxlength="500">{{ old('keterangan') }}</textarea>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('transaksi.keluar') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary px-5"><i class="bi bi-check2 me-1"></i>Simpan Retur</button>
    </div>
    </form>
</div>

</div>
</div>
@endsection

@push('styles')
<style>
.retur-type-card {
    flex: 1;
    border: 2px solid var(--border);
    border-radius: 10px;
    padding: 14px 16px;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: var(--surface);
}
.retur-type-card.active {
    border-color: var(--brand);
    background: var(--brand-soft);
}
.retur-type-card .rtc-icon {
    font-size: 1.4rem;
    color: var(--text-subtle);
    margin-bottom: 6px;
}
.retur-type-card.active .rtc-icon { color: var(--brand); }
.retur-type-card .rtc-label {
    font-size: .88rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 2px;
}
.retur-type-card .rtc-sub {
    font-size: .72rem;
    color: var(--text-muted);
}
</style>
@endpush

@push('scripts')
<script>
// ── Type switcher ──────────────────────────────────────────────
const typeCustomer = document.getElementById('typeCustomer');
const typeProduksi = document.getElementById('typeProduksi');
const labelC = document.getElementById('labelCustomer');
const labelP = document.getElementById('labelProduksi');
const formC  = document.getElementById('formCustomer');
const formP  = document.getElementById('formProduksi');

function switchType(type) {
    if (type === 'customer') {
        formC.style.display = '';
        formP.style.display = 'none';
        labelC.classList.add('active');
        labelP.classList.remove('active');
    } else {
        formC.style.display = 'none';
        formP.style.display = '';
        labelP.classList.add('active');
        labelC.classList.remove('active');
    }
}
typeCustomer.addEventListener('change', () => switchType('customer'));
typeProduksi.addEventListener('change', () => switchType('produksi'));
labelP.addEventListener('click', () => switchType('produksi'));
labelC.addEventListener('click', () => switchType('customer'));

// ── Helper: buat auto-fill + lock untuk satu form ──────────────
function bindRefAutofill(refSelect, barangSelect, lotInput, qtyInput, satuanLabel, refInfo) {
    function updateSatuan() {
        const opt = barangSelect.options[barangSelect.selectedIndex];
        satuanLabel.textContent = opt && opt.dataset.satuan ? opt.dataset.satuan : 'pcs';
    }
    barangSelect.addEventListener('change', updateSatuan);
    updateSatuan();

    refSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) {
            barangSelect.style.background   = 'var(--bg)';
            barangSelect.style.pointerEvents = 'none';
            lotInput.readOnly               = true;
            lotInput.style.background       = 'var(--bg)';
            refInfo.style.display           = 'none';
            qtyInput.removeAttribute('max');
            return;
        }
        barangSelect.value              = opt.dataset.barangId;
        barangSelect.style.background   = 'var(--bg)';
        barangSelect.style.pointerEvents = 'none';
        updateSatuan();

        lotInput.value            = (opt.dataset.lot && opt.dataset.lot !== 'null') ? opt.dataset.lot : '';
        lotInput.readOnly         = true;
        lotInput.style.background = 'var(--bg)';

        const sisa       = parseInt(opt.dataset.sisa);
        const sudahRetur = parseInt(opt.dataset.sudahRetur || 0);
        qtyInput.value = sisa;
        qtyInput.max   = sisa;

        const infoExtra = sudahRetur > 0 ? ` | Sudah diretur: ${sudahRetur}` : '';
        refInfo.textContent   = `Sisa quota retur: ${sisa} ${opt.dataset.satuan}${infoExtra}`;
        refInfo.style.display = 'block';
    });

    if (refSelect.value) refSelect.dispatchEvent(new Event('change'));
}

// Bind ke Customer form
bindRefAutofill(
    document.getElementById('refSelectC'),
    document.getElementById('barangSelectC'),
    document.getElementById('lotInputC'),
    document.getElementById('qtyInputC'),
    document.getElementById('satuanLabelC'),
    document.getElementById('refInfoC')
);

// Bind ke Produksi form
bindRefAutofill(
    document.getElementById('refSelectP'),
    document.getElementById('barangSelectP'),
    document.getElementById('lotInputP'),
    document.getElementById('qtyInputP'),
    document.getElementById('satuanLabelP'),
    document.getElementById('refInfoP')
);

// Pre-select no_ref dari URL (misal dari tombol RETUR di halaman keluar)
const preRef = '{{ request("no_ref") }}';
if (preRef) {
    const refC = document.getElementById('refSelectC');
    const refP = document.getElementById('refSelectP');
    // Cek ada di Customer atau Produksi
    let found = false;
    for (let opt of refC.options) {
        if (opt.value === preRef) { refC.value = preRef; refC.dispatchEvent(new Event('change')); switchType('customer'); found = true; break; }
    }
    if (!found) {
        for (let opt of refP.options) {
            if (opt.value === preRef) { refP.value = preRef; refP.dispatchEvent(new Event('change')); switchType('produksi'); break; }
        }
    }
}

// Double-submit protection — disable whichever submit button is clicked
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'; }
    });
});
</script>
@endpush
