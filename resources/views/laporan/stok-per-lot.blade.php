@extends('layouts.app')
@section('title', 'Stok Per Lot')
@section('page-title', 'Stok Per Lot')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Stok Per Nomor Lot</h4>
        <p>Detail stok barang berdasarkan nomor lot / batch.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.stok') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clipboard-data me-2"></i>Semua Stok
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-2"></i>Cetak
        </button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="nomor_lot" class="form-control"
                       placeholder="Filter nomor lot..." value="{{ request('nomor_lot') }}">
            </div>
            <div class="col-md-4">
                <select name="merk" class="form-select">
                    <option value="">Semua Merk</option>
                    @foreach($merks as $m)
                    <option value="{{ $m }}" {{ request('merk') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Filter</button>
                @if(request()->hasAny(['nomor_lot','merk']))
                <a href="{{ route('laporan.stok-per-lot') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Nomor Lot</th>
                        <th>Satuan</th>
                        <th class="text-end">Stok Akhir</th>
                        <th>Update Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stoks as $s)
                    <tr>
                        <td class="fw-semibold">{{ $s->barang->nama_barang }}</td>
                        <td class="text-muted">{{ $s->barang->merk ?: '—' }}</td>
                        <td>
                            @if($s->nomor_lot)
                            <code class="code-tag">{{ $s->nomor_lot }}</code>
                            @else
                            <span class="text-muted" style="font-style:italic;">Tanpa Lot</span>
                            @endif
                        </td>
                        <td>{{ $s->barang->satuan }}</td>
                        <td class="text-end fw-bold" style="color:{{ $s->stok_akhir <= 0 ? 'var(--danger)' : 'var(--success)' }};">
                            {{ $s->stok_akhir }}
                        </td>
                        <td class="text-muted">{{ $s->tanggal_update->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada data stok</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($stoks->hasPages())
    <div class="card-footer">{{ $stoks->links() }}</div>
    @endif
</div>
@endsection
