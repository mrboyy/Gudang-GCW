@extends('layouts.app')
@section('title', 'Barang Masuk')
@section('page-title', 'Barang Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Barang Masuk</li>
@endsection

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Barang Masuk</h4>
        <p>Catatan seluruh barang yang masuk ke gudang.</p>
    </div>
    <a href="{{ route('transaksi.create-masuk') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Catat Barang Masuk
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama barang atau no. transaksi..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                    @if(request()->hasAny(['search','tanggal_dari','tanggal_sampai','periode']))
                    <a href="{{ route('transaksi.masuk') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
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
                        <th>No. Transaksi</th>
                        <th class="d-none d-md-table-cell">Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="d-none d-md-table-cell">Lot</th>
                        <th class="d-none d-lg-table-cell">Supplier</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prevTglMasuk = null; @endphp
                    @forelse($transaksis as $t)
                    @php $tglStr = $t->tanggal->locale('id')->isoFormat('dddd, D MMMM Y'); @endphp
                    @if($tglStr !== $prevTglMasuk)
                    <tr style="background:var(--bg);"><td colspan="7" style="padding:7px 18px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);border-bottom:1px solid var(--border);">
                        <i class="bi bi-calendar3 me-2" style="color:var(--brand);"></i>{{ $tglStr }}</td></tr>
                    @php $prevTglMasuk = $tglStr; @endphp
                    @endif
                    <tr @if($t->is_void) style="opacity:.5;" @endif>
                        <td>
                            <span class="badge badge-masuk">{{ $t->no_transaksi }}</span>
                            @if($t->is_void)
                            <span class="badge ms-1" style="background:#fff5f5;color:#991b1b;font-size:.7rem;border:1px solid #fecaca;">Batal</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                        <td class="d-none d-md-table-cell">{{ $t->nomor_lot ?: '—' }}</td>
                        <td class="text-muted d-none d-lg-table-cell" style="font-size:.85rem;">{{ $t->nama_supplier ?: '—' }}</td>
                        <td class="text-end fw-semibold" style="color:{{ $t->is_void ? 'var(--text-muted)' : 'var(--success)' }};">
                            {{ $t->quantity }} <small class="text-muted fw-normal">{{ $t->barang->satuan }}</small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('transaksi.show', $t) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada transaksi barang masuk</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transaksis->hasPages())
    <div class="card-footer">{{ $transaksis->links() }}</div>
    @endif
</div>
@endsection
