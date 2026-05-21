@extends('layouts.print')

@section('title', 'Laporan Transaksi')
@section('doc-title', 'LAPORAN TRANSAKSI')
@section('doc-no', 'Periode: ' . \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') . ' s/d ' . \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y'))

@section('content')

<div class="doc-section-title">Ringkasan</div>
<table class="info-table" style="margin-bottom:16px;">
    <tr><td>Periode</td><td>:</td><td>{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</td></tr>
    <tr><td>Total Transaksi</td><td>:</td><td><strong>{{ $transaksis->count() }}</strong></td></tr>
    <tr>
        <td>Total Masuk</td><td>:</td>
        <td>{{ $transaksis->whereIn('jenis_transaksi',['masuk','retur_customer','retur_produksi'])->where('is_void',false)->sum('quantity') }} unit</td>
    </tr>
    <tr>
        <td>Total Keluar</td><td>:</td>
        <td>{{ $transaksis->where('jenis_transaksi','keluar')->where('is_void',false)->sum('quantity') }} unit</td>
    </tr>
</table>

<div class="doc-section-title">Daftar Transaksi</div>
<table class="data-table">
    <thead>
        <tr>
            <th>No. Transaksi</th>
            <th>Jenis</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Lot</th>
            <th>Supplier/Tujuan</th>
            <th style="text-align:right">Qty</th>
            <th>Operator</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transaksis as $t)
        <tr @if($t->is_void) style="opacity:.5;text-decoration:line-through;" @endif>
            <td style="font-family:monospace;font-size:10px;">{{ $t->no_transaksi }}</td>
            <td>
                @php
                    $bc = match($t->jenis_transaksi) { 'masuk'=>'badge-masuk', 'keluar'=>'badge-keluar', 'retur_produksi'=>'badge-retur-p', default=>'badge-retur' };
                    $bl = ['masuk'=>'Masuk','keluar'=>'Keluar','retur_customer'=>'Retur Cust.','retur_produksi'=>'Retur Prod.'][$t->jenis_transaksi] ?? $t->jenis_transaksi;
                @endphp
                <span class="badge-jenis {{ $bc }}">{{ $bl }}</span>
            </td>
            <td>{{ $t->tanggal->format('d/m/Y') }}</td>
            <td>{{ $t->barang->nama_barang }}</td>
            <td>{{ $t->nomor_lot ?? '—' }}</td>
            <td style="font-size:10px;">{{ $t->nama_supplier ?: $t->tujuan_keluar ?: '—' }}</td>
            <td style="text-align:right;font-weight:700;">{{ number_format($t->quantity) }}</td>
            <td>{{ $t->user->username }}</td>
            <td style="color:#555;font-size:10px;">{{ $t->keterangan ?: '—' }}{{ $t->is_void ? ' [BATAL]' : '' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:20px;color:#888;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align:right;">Total qty tidak void:</td>
            <td style="text-align:right;">{{ number_format($transaksis->where('is_void',false)->sum('quantity')) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

@endsection

@section('signature')
<div class="sign-box">
    <div class="sign-line"></div>
    <div class="sign-label">Mengetahui,<br>Kepala Gudang</div>
</div>
@endsection
