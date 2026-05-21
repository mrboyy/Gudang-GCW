@extends('layouts.app')
@section('title', 'Retur Customer')
@section('page-title', 'Retur Customer')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Retur Customer</li>
@endsection

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Retur Customer</h4>
        <p>Catatan barang yang dikembalikan oleh customer ke gudang.</p>
    </div>
    <a href="{{ route('retur.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Catat Retur Baru
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-9">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama barang atau no. transaksi..." value="{{ request('search') }}">
                </div>

                <div class="col-md-5">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach(['hari'=>'Hari Ini','bulan'=>'Bulan Ini','tahun'=>'Tahun Ini'] as $val=>$label)
                        <a href="{{ route('retur.index', array_merge(request()->except('periode','tanggal_dari','tanggal_sampai','page'), ['periode'=>$val])) }}"
                           class="btn btn-sm {{ request('periode')===$val ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                        @endforeach
                        @if(!in_array(request('periode'),['hari','bulan','tahun']))
                        <input type="date" name="tanggal_dari" class="form-control form-control-sm" style="width:auto;" value="{{ request('tanggal_dari') }}">
                        <input type="date" name="tanggal_sampai" class="form-control form-control-sm" style="width:auto;" value="{{ request('tanggal_sampai') }}">
                        @endif
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                    @if(request()->hasAny(['search','tanggal_dari','tanggal_sampai','periode']))
                    <a href="{{ route('retur.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th class="d-none d-md-table-cell">No. Ref (SJ)</th>
                        <th class="d-none d-md-table-cell">Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="d-none d-lg-table-cell">Lot</th>
                        <th class="text-end">Jumlah</th>
                        <th class="d-none d-lg-table-cell">Keterangan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr @if($t->is_void) style="opacity:.5;" @endif>
                        <td>
                            <a href="{{ route('transaksi.show', $t) }}" class="text-decoration-none">
                                <span class="badge badge-retur">{{ $t->no_transaksi }}</span>
                            </a>
                            @if($t->is_void)
                            <span class="badge ms-1" style="background:#fff5f5;color:#991b1b;font-size:.7rem;border:1px solid #fecaca;">Batal</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">
                            @if($t->no_ref)
                            <span class="text-muted" style="font-size:.85rem;font-weight:600;">{{ $t->no_ref }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                        <td class="d-none d-lg-table-cell">{{ $t->nomor_lot ?: '—' }}</td>
                        <td class="text-end fw-semibold" style="color:var(--info);">
                            {{ $t->quantity }} <small class="text-muted fw-normal">{{ $t->barang->satuan }}</small>
                        </td>
                        <td class="d-none d-lg-table-cell" style="color:var(--text-muted);font-size:.85rem;">{{ $t->keterangan ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('transaksi.show', $t) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="bi bi-arrow-return-left"></i>
                            <p>Belum ada catatan retur customer</p>
                        </td>
                    </tr>
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
