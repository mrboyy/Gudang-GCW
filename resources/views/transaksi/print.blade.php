@extends('layouts.print')

@php
    $jenis = $transaksi->jenis_transaksi;
    $jenisLabel = ['masuk'=>'Barang Masuk','keluar'=>'Barang Keluar','retur_customer'=>'Retur Customer','retur_produksi'=>'Retur Produksi'][$jenis] ?? $jenis;
    $badgeClass = match($jenis) { 'masuk'=>'badge-masuk', 'keluar'=>'badge-keluar', 'retur_produksi'=>'badge-retur-p', default=>'badge-retur' };
    $prefixTitle = ['masuk'=>'BUKTI BARANG MASUK','keluar'=>'BUKTI BARANG KELUAR','retur_customer'=>'BUKTI RETUR CUSTOMER','retur_produksi'=>'BUKTI RETUR PRODUKSI'][$jenis] ?? 'BUKTI TRANSAKSI';
@endphp

@section('title', $prefixTitle)
@section('doc-title', $prefixTitle)
@section('doc-no', $transaksi->no_transaksi)

@section('content')

@if($transaksi->is_void)
<div class="void-banner">
    <span class="vb-icon">⊘</span>
    <div class="vb-text">
        TRANSAKSI DIBATALKAN
        @if($transaksi->void_at) — {{ $transaksi->void_at->format('d/m/Y H:i') }}@endif
        @if($transaksi->void_reason) · {{ $transaksi->void_reason }}@endif
    </div>
</div>
@endif

<div class="highlight-box">
    <div class="qty-label">Jumlah</div>
    <div>
        <span class="qty-val">{{ number_format($transaksi->quantity) }}</span>
        <span class="qty-unit">{{ $transaksi->barang->satuan }}</span>
    </div>
</div>

<div class="doc-section-title">Detail Transaksi</div>
<table class="info-table">
    <tr>
        <td>Nomor Transaksi</td><td>:</td>
        <td><strong>{{ $transaksi->no_transaksi }}</strong></td>
    </tr>
    @if($jenis === 'keluar' && $transaksi->no_surat_jalan)
    <tr>
        <td>No. Surat Jalan</td><td>:</td>
        <td><strong>{{ $transaksi->no_surat_jalan }}</strong></td>
    </tr>
    @endif
    @if(in_array($jenis, ['retur_customer','retur_produksi']) && $transaksi->no_ref)
    <tr>
        <td>Ref. No. SJ</td><td>:</td>
        <td>{{ $transaksi->no_ref }}</td>
    </tr>
    @endif
    <tr>
        <td>Jenis</td><td>:</td>
        <td><span class="badge-jenis {{ $badgeClass }}">{{ $jenisLabel }}</span></td>
    </tr>
    <tr>
        <td>Tanggal</td><td>:</td>
        <td>{{ $transaksi->tanggal->isoFormat('D MMMM Y') }}</td>
    </tr>
    <tr>
        <td>Nama Barang</td><td>:</td>
        <td>{{ $transaksi->barang->nama_barang }}</td>
    </tr>
    <tr>
        <td>Kode Barang</td><td>:</td>
        <td>{{ $transaksi->barang->kode_barang }}</td>
    </tr>
    <tr>
        <td>Lot</td><td>:</td>
        <td>{{ $transaksi->nomor_lot ?? '—' }}</td>
    </tr>
    @if($transaksi->jenis_transaksi === 'keluar')
    <tr>
        <td>Tujuan</td><td>:</td>
        <td>{{ $transaksi->tujuan_keluar ?: '—' }}</td>
    </tr>
    @endif
    <tr>
        <td>Keterangan</td><td>:</td>
        <td>{{ $transaksi->keterangan ?: '—' }}</td>
    </tr>
    <tr>
        <td>Dicatat Oleh</td><td>:</td>
        <td>{{ $transaksi->user->username }}</td>
    </tr>
    <tr>
        <td>Waktu Input</td><td>:</td>
        <td>{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</td>
    </tr>
</table>

@endsection

@section('signature')
<div class="sign-box">
    <div class="sign-line"></div>
    <div class="sign-label">Mengetahui,<br>Kepala Gudang</div>
</div>
@endsection
