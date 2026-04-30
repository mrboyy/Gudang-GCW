@extends('layouts.print')

@section('title', 'Laporan Stok')
@section('doc-title', 'LAPORAN STOK BARANG')
@section('doc-no', 'Per tanggal ' . now()->format('d/m/Y'))

@section('content')

<div class="doc-section-title">Ringkasan</div>
<table class="info-table" style="margin-bottom:16px;">
    <tr><td>Tanggal Cetak</td><td>:</td><td>{{ now()->isoFormat('D MMMM Y, HH:mm') }}</td></tr>
    <tr><td>Total Jenis Barang</td><td>:</td><td><strong>{{ $barangs->count() }}</strong></td></tr>
    <tr><td>Total Stok Keseluruhan</td><td>:</td><td><strong>{{ number_format($barangs->sum(fn($b) => $b->stoks->sum('stok_akhir'))) }}</strong> unit</td></tr>
</table>

<div class="doc-section-title">Daftar Stok</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Merk</th>
            <th>Satuan</th>
            <th>Nomor Lot</th>
            <th style="text-align:right">Stok</th>
            <th style="text-align:right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($barangs as $b)
            @php $totalStok = $b->stoks->sum('stok_akhir'); @endphp
            @if($b->stoks->count() > 1)
            {{-- Row header barang --}}
            <tr style="background:#F8F9FA;">
                <td style="font-size:10px;color:#555;">{{ $b->kode_barang }}</td>
                <td style="font-weight:700;">{{ $b->nama_barang }}</td>
                <td style="color:#555;">{{ $b->merk }}</td>
                <td>{{ $b->satuan }}</td>
                <td style="color:#888;font-style:italic;">{{ $b->stoks->count() }} lot</td>
                <td></td>
                <td style="text-align:right;font-weight:700;">{{ number_format($totalStok) }}</td>
            </tr>
            {{-- Row tiap lot --}}
            @foreach($b->stoks as $s)
            <tr>
                <td></td>
                <td style="padding-left:20px;color:#555;font-size:11px;">↳ {{ $s->nomor_lot ?? 'Tanpa Lot' }}</td>
                <td></td>
                <td>{{ $b->satuan }}</td>
                <td style="font-size:10px;font-family:monospace;">{{ $s->nomor_lot ?? '—' }}</td>
                <td style="text-align:right;">{{ number_format($s->stok_akhir) }}</td>
                <td></td>
            </tr>
            @endforeach
            @else
            <tr>
                <td style="font-size:10px;color:#555;">{{ $b->kode_barang }}</td>
                <td style="font-weight:600;">{{ $b->nama_barang }}</td>
                <td style="color:#555;">{{ $b->merk }}</td>
                <td>{{ $b->satuan }}</td>
                <td style="font-size:10px;font-family:monospace;">{{ $b->stoks->first()?->nomor_lot ?? '—' }}</td>
                <td style="text-align:right;font-weight:700;">{{ number_format($totalStok) }}</td>
                <td style="text-align:right;font-weight:700;">{{ number_format($totalStok) }}</td>
            </tr>
            @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align:right;">Grand Total:</td>
            <td style="text-align:right;">{{ number_format($barangs->sum(fn($b) => $b->stoks->sum('stok_akhir'))) }}</td>
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
