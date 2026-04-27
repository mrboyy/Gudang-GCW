@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
@php
    $jenis     = $transaksi->jenis_transaksi;
    $isMasuk   = $jenis === 'masuk';
    $isRetur   = $jenis === 'retur_customer';
    $backRoute = $isMasuk ? route('transaksi.masuk') : ($isRetur ? route('retur.index') : route('transaksi.keluar'));
    $accentBg  = $isMasuk ? 'var(--success-soft)' : ($isRetur ? 'var(--info-soft)' : 'var(--brand-soft)');
    $accentClr = $isMasuk ? 'var(--success)' : ($isRetur ? 'var(--info)' : 'var(--brand)');
    $numClr    = $isMasuk ? 'var(--success)' : ($isRetur ? 'var(--info)' : 'var(--danger)');
    $badgeBg   = $isMasuk ? 'var(--success-soft)' : ($isRetur ? 'var(--info-soft)' : 'var(--brand-soft)');
    $badgeClr  = $isMasuk ? 'var(--success)' : ($isRetur ? 'var(--info)' : 'var(--brand)');
    $badgeBdr  = $isMasuk ? '#A7F3D0' : ($isRetur ? '#BFDBFE' : '#FDDDB5');
    $badgeIcon = $isMasuk ? 'box-arrow-in-down' : ($isRetur ? 'arrow-return-left' : 'box-arrow-up');
    $badgeTxt  = $isMasuk ? 'Barang Masuk' : ($isRetur ? 'Retur Customer' : 'Barang Keluar');
@endphp

<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ $backRoute }}" class="btn btn-sm btn-outline-secondary" title="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-fill">
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;color:var(--text);">Detail Transaksi</h5>
        <div class="text-muted" style="font-size:.78rem;">{{ $transaksi->no_transaksi }}</div>
    </div>
    <span class="badge px-3 py-2" style="font-size:.82rem;background:{{ $badgeBg }};color:{{ $badgeClr }};border:1px solid {{ $badgeBdr }};">
        <i class="bi bi-{{ $badgeIcon }} me-1"></i>{{ $badgeTxt }}
    </span>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary" title="Cetak">
        <i class="bi bi-printer"></i>
    </button>
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
            ['icon'=>'bi-archive',       'label'=>'Nama Barang',  'val'=>$transaksi->barang->nama_barang.($transaksi->barang->merk ? ' — '.$transaksi->barang->merk : '')],
            ['icon'=>'bi-calendar3',     'label'=>'Tanggal',      'val'=>$transaksi->tanggal->isoFormat('D MMMM Y')],
            ['icon'=>'bi-upc',           'label'=>'Nomor Lot',    'val'=>$transaksi->nomor_lot ?: '—'],
            ['icon'=>'bi-person',        'label'=>'Dicatat oleh', 'val'=>$transaksi->user->username],
            ['icon'=>'bi-chat-text',     'label'=>'Keterangan',   'val'=>$transaksi->keterangan ?: '—'],
            ['icon'=>'bi-clock-history', 'label'=>'Waktu Input',  'val'=>$transaksi->created_at->format('d/m/Y H:i')],
        ];
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

</div>
</div>

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
