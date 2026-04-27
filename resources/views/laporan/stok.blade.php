@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Laporan Stok Barang</h4>
        <p>Ringkasan stok seluruh barang di gudang saat ini.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.stok-per-lot') }}" class="btn btn-outline-secondary">
            <i class="bi bi-layers me-2"></i>Lihat Per Lot
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-2"></i>Cetak
        </button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-9">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama barang atau merk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search me-1"></i>Cari</button>
                @if(request('search'))
                <a href="{{ route('laporan.stok') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
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
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Satuan</th>
                        <th class="text-end">Stok</th>
                        <th class="text-end">Min.</th>
                        <th class="text-center">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $b)
                    @php $stok = $b->getStok(); $habis = $b->isStokMinimum(); @endphp
                    <tr>
                        <td><code class="code-tag">{{ $b->kode_barang }}</code></td>
                        <td class="fw-semibold">{{ $b->nama_barang }}</td>
                        <td class="text-muted">{{ $b->merk ?: '—' }}</td>
                        <td>{{ $b->satuan }}</td>
                        <td class="text-end fw-bold" style="color:{{ $habis ? 'var(--danger)' : 'var(--success)' }};">{{ $stok }}</td>
                        <td class="text-end text-muted">{{ $b->stok_minimum }}</td>
                        <td class="text-center">
                            @if($habis)
                            <span class="badge badge-status-warn"><i class="bi bi-exclamation-triangle-fill me-1"></i>Hampir Habis</span>
                            @else
                            <span class="badge badge-status-ok"><i class="bi bi-check-circle-fill me-1"></i>Normal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada data barang</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($barangs->hasPages())
    <div class="card-footer">{{ $barangs->links() }}</div>
    @endif
</div>

@push('styles')
@media print {
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .btn { display: none !important; }
}
@endpush
@endsection
