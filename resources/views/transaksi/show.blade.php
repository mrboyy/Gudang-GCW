@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Detail Transaksi</li>
@endsection

@section('content')
@php
    $jenis            = $transaksi->jenis_transaksi;
    $isMasuk          = $jenis === 'masuk';
    $isReturCustomer  = $jenis === 'retur_customer';
    $isReturProduksi  = $jenis === 'retur_produksi';
    $isRetur          = $isReturCustomer || $isReturProduksi;

    if ($isMasuk)         $backRoute = route('transaksi.masuk');
    elseif ($isReturCustomer) $backRoute = route('retur.index');
    elseif ($isReturProduksi) $backRoute = route('retur.produksi.index');
    else                  $backRoute = route('transaksi.keluar');

    // Accent for quantity header
    if ($isMasuk)         { $accentBg='var(--success-soft)'; $accentClr='var(--success)'; $numClr='var(--success)'; }
    elseif ($isReturCustomer) { $accentBg='var(--info-soft)'; $accentClr='var(--info)'; $numClr='var(--info)'; }
    elseif ($isReturProduksi) { $accentBg='var(--warning-soft)'; $accentClr='var(--warning)'; $numClr='var(--warning)'; }
    else                  { $accentBg='var(--brand-soft)'; $accentClr='var(--brand)'; $numClr='var(--danger)'; }

    // Badge
    if ($isMasuk)         { $badgeBg='var(--success-soft)'; $badgeClr='var(--success)'; $badgeBdr='#A7F3D0'; $badgeIcon='box-arrow-in-down'; $badgeTxt='Barang Masuk'; }
    elseif ($isReturCustomer) { $badgeBg='var(--info-soft)'; $badgeClr='var(--info)'; $badgeBdr='#BFDBFE'; $badgeIcon='arrow-return-left'; $badgeTxt='Retur Customer'; }
    elseif ($isReturProduksi) { $badgeBg='var(--warning-soft)'; $badgeClr='var(--warning)'; $badgeBdr='#FCD34D'; $badgeIcon='arrow-counterclockwise'; $badgeTxt='Retur Produksi'; }
    else                  { $badgeBg='var(--brand-soft)'; $badgeClr='var(--brand)'; $badgeBdr='#FDDDB5'; $badgeIcon='box-arrow-up'; $badgeTxt='Barang Keluar'; }
@endphp

<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

@if($transaksi->is_void)
<div class="alert alert-danger d-flex align-items-start gap-2 mb-3" style="border-left:4px solid #b91c1c;">
    <i class="bi bi-slash-circle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <div class="fw-bold mb-1">Transaksi Dibatalkan</div>
        <div style="font-size:.85rem;">
            @if($transaksi->voidUser)
            Oleh <strong>{{ $transaksi->voidUser->username }}</strong> pada {{ $transaksi->void_at->format('d/m/Y H:i') }}
            @endif
            @if($transaksi->void_reason)
            <div class="mt-1 text-muted">Alasan: {{ $transaksi->void_reason }}</div>
            @endif
        </div>
    </div>
</div>
@endif

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ $backRoute }}" class="btn btn-sm btn-outline-secondary" title="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-fill">
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;color:var(--text);">Detail Transaksi</h5>
        <div class="text-muted" style="font-size:.78rem;">{{ $transaksi->no_transaksi }}</div>
    </div>
    @if($transaksi->is_void)
    <span class="badge px-3 py-2" style="font-size:.82rem;background:#fff5f5;color:#991b1b;border:1px solid #fecaca;">
        <i class="bi bi-slash-circle me-1"></i>Dibatalkan
    </span>
    @else
    <span class="badge px-3 py-2" style="font-size:.82rem;background:{{ $badgeBg }};color:{{ $badgeClr }};border:1px solid {{ $badgeBdr }};">
        <i class="bi bi-{{ $badgeIcon }} me-1"></i>{{ $badgeTxt }}
    </span>
    @endif
    @if(!$transaksi->is_void && (auth()->user()->isOperator() || auth()->user()->isKepalaGudang() || auth()->user()->isAdmin()))
    <a href="{{ route('transaksi.edit', $transaksi) }}" class="btn btn-sm btn-outline-secondary" title="Edit keterangan/tanggal">
        <i class="bi bi-pencil"></i>
    </a>
    @endif
    <a href="{{ route('transaksi.print', $transaksi) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak / Simpan PDF">
        <i class="bi bi-printer"></i>
    </a>
</div>

<div class="card">
    {{-- Quantity highlight --}}
    <div class="p-4" style="border-bottom:1px solid var(--border-soft);background:{{ $accentBg }};border-radius:8px 8px 0 0;">
        <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:{{ $accentClr }};margin-bottom:4px;opacity:.7;">Jumlah</div>
        <div style="font-size:2.6rem;font-weight:800;letter-spacing:-.04em;line-height:1;color:{{ $numClr }};">
            {{ number_format($transaksi->quantity) }}
            <span style="font-size:1rem;font-weight:600;color:var(--text-muted);">{{ $transaksi->barang->satuan }}</span>
        </div>
    </div>

    {{-- Info rows --}}
    <div class="card-body p-0">
        @php
        $rows = [
            ['icon'=>'bi-archive',       'label'=>'Nama Barang',  'val'=>$transaksi->barang->nama_barang],
            ['icon'=>'bi-calendar3',     'label'=>'Tanggal',      'val'=>$transaksi->tanggal->isoFormat('D MMMM Y')],
            ['icon'=>'bi-upc',           'label'=>'Lot',    'val'=>$transaksi->nomor_lot ?: '—'],
        ];

        if ($jenis === 'keluar' && $transaksi->no_surat_jalan) {
            $rows[] = ['icon'=>'bi-file-text', 'label'=>'No. Surat Jalan', 'val'=>$transaksi->no_surat_jalan];
        }
        if ($transaksi->no_ref && in_array($jenis, ['retur_customer', 'retur_produksi'])) {
            $rows[] = ['icon'=>'bi-hash', 'label'=>'Ref. No. SJ', 'val'=>$transaksi->no_ref];
        }

        if ($transaksi->jenis_transaksi === 'keluar') {
            $rows[] = ['icon'=>'bi-geo-alt', 'label'=>'Tujuan', 'val'=>$transaksi->tujuan_keluar ?: '—'];
        }

        if ($transaksi->jenis_transaksi === 'masuk' && $transaksi->nama_supplier) {
            $rows[] = ['icon'=>'bi-truck', 'label'=>'Supplier', 'val'=>$transaksi->nama_supplier];
        }

        $rows = array_merge($rows, [
            ['icon'=>'bi-person',        'label'=>'Dicatat oleh', 'val'=>$transaksi->user->username],
            ['icon'=>'bi-chat-text',     'label'=>'Keterangan',   'val'=>$transaksi->keterangan ?: '—'],
            ['icon'=>'bi-clock-history', 'label'=>'Waktu Input',  'val'=>$transaksi->created_at->format('d/m/Y H:i')],
        ]);
        @endphp

        @foreach($rows as $i => $row)
        <div class="d-flex align-items-start gap-3 px-4 py-3 {{ $i < count($rows)-1 ? 'border-bottom' : '' }}" style="border-color:var(--border-soft)!important;">
            <div style="width:32px;flex-shrink:0;margin-top:2px;">
                <i class="bi {{ $row['icon'] }}" style="font-size:.95rem;color:var(--text-muted);"></i>
            </div>
            <div>
                <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin-bottom:2px;">{{ $row['label'] }}</div>
                <div style="font-size:.92rem;font-weight:600;color:var(--text);">{{ $row['val'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@if(!$transaksi->is_void && $transaksi->jenis_transaksi === 'keluar' && (auth()->user()->isOperator() || auth()->user()->isAdmin() || auth()->user()->isKepalaGudang()))
@php
    $isCustomer = str_starts_with($transaksi->tujuan_keluar ?? '', 'Customer');
    $returRoute = $isCustomer ? route('retur.create', ['no_ref' => $transaksi->no_transaksi]) : route('retur.produksi.create', ['no_ref' => $transaksi->no_transaksi]);
    $returLabel = $isCustomer ? 'Retur Customer' : 'Retur Produksi';
    $returIcon  = $isCustomer ? 'arrow-return-left' : 'arrow-counterclockwise';
@endphp
<div class="mt-3 d-flex justify-content-between align-items-center">
    <a href="{{ $returRoute }}" class="btn btn-sm btn-outline-info">
        <i class="bi bi-{{ $returIcon }} me-1"></i>{{ $returLabel }}
    </a>
    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#voidModal">
        <i class="bi bi-slash-circle me-1"></i>Batalkan Transaksi
    </button>
    @endif
</div>
@elseif(!$transaksi->is_void && (auth()->user()->isAdmin() || auth()->user()->isKepalaGudang()))
<div class="mt-3 text-end">
    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#voidModal">
        <i class="bi bi-slash-circle me-1"></i>Batalkan Transaksi
    </button>
</div>
@endif

</div>
</div>

{{-- Modal Konfirmasi Void --}}
@if(!$transaksi->is_void && (auth()->user()->isAdmin() || auth()->user()->isKepalaGudang()))
<div class="modal fade" id="voidModal" tabindex="-1" aria-labelledby="voidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold" id="voidModalLabel">
                    <i class="bi bi-slash-circle me-2 text-danger"></i>Batalkan Transaksi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('transaksi.void', $transaksi) }}">
                @csrf
                <div class="modal-body pt-2">
                    <p class="text-muted" style="font-size:.88rem;">
                        Transaksi <strong>{{ $transaksi->no_transaksi }}</strong> akan dibatalkan.
                        Stok akan dikoreksi otomatis. Tindakan ini tidak dapat diurungkan.
                    </p>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:.88rem;">
                            Alasan pembatalan <span class="text-danger">*</span>
                        </label>
                        <textarea name="void_reason" class="form-control form-control-sm @error('void_reason') is-invalid @enderror"
                            rows="3" required maxlength="500"
                            placeholder="Contoh: Salah input quantity, duplikasi transaksi...">{{ old('void_reason') }}</textarea>
                        @error('void_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-slash-circle me-1"></i>Konfirmasi Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
@media print {
    .topbar, .sidebar, .mobile-bottom-nav,
    .btn-outline-secondary, button { display: none !important; }
    .main-wrap { margin-left: 0 !important; padding-top: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .row.justify-content-center > div { max-width: 100% !important; width: 100% !important; }
    body { background: #fff !important; }
    @page { margin: 1.5cm; }
}
</style>
@endpush

@endsection
