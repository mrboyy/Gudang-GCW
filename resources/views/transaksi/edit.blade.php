@extends('layouts.app')
@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')

@section('content')
@php
    $backRoute = route('transaksi.show', $transaksi);
    $jenisLabel = ['masuk'=>'Barang Masuk','keluar'=>'Barang Keluar','retur_customer'=>'Retur Customer','retur_produksi'=>'Retur Produksi'][$transaksi->jenis_transaksi] ?? $transaksi->jenis_transaksi;
@endphp

<div class="row justify-content-center">
<div class="col-12 col-lg-7 col-xl-6">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ $backRoute }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="color:var(--text);">Edit Transaksi</h5>
        <div class="text-muted" style="font-size:.78rem;">{{ $transaksi->no_transaksi }} — hanya keterangan & tanggal yang bisa diubah</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card mb-3">
    <div class="card-body p-4">
        <div class="text-muted mb-3" style="font-size:.82rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Info (tidak bisa diubah)</div>
        <table class="table table-sm mb-0" style="font-size:.875rem;">
            <tr><td style="color:var(--text-muted);width:130px;">Jenis</td><td>{{ $jenisLabel }}</td></tr>
            <tr><td style="color:var(--text-muted);">Barang</td><td>{{ $transaksi->barang->nama_barang }}</td></tr>
            <tr><td style="color:var(--text-muted);">Jumlah</td><td>{{ number_format($transaksi->quantity) }} {{ $transaksi->barang->satuan }}</td></tr>
            <tr><td style="color:var(--text-muted);">Lot</td><td>{{ $transaksi->nomor_lot ?: '—' }}</td></tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Data yang Bisa Diubah</div>
    <form method="POST" action="{{ route('transaksi.update', $transaksi) }}">
        @csrf
        @method('PATCH')
        <div class="card-body p-4">

            <div class="mb-4">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal"
                       class="form-control @error('tanggal') is-invalid @enderror"
                       value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}"
                       max="{{ date('Y-m-d') }}" required>
                @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-1">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3"
                          maxlength="500">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                <div class="text-muted mt-1" style="font-size:.75rem;">Maks 500 karakter</div>
            </div>

        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ $backRoute }}" class="btn btn-outline-secondary">
                <i class="bi bi-x me-1"></i>Batal
            </a>
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>

</div>
</div>
@endsection
