@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('page-title', 'Barang Keluar')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Barang Keluar</h4>
        <p>Catatan seluruh barang yang keluar dari gudang.</p>
    </div>
    <a href="{{ route('transaksi.create-keluar') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Catat Barang Keluar
    </a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari nama barang, merk, no. transaksi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-5">
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach(['hari'=>'Hari Ini','bulan'=>'Bulan Ini','tahun'=>'Tahun Ini'] as $val=>$label)
                        <a href="{{ route('transaksi.keluar', array_merge(request()->except('periode','tanggal_dari','tanggal_sampai','page'), ['periode'=>$val])) }}"
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
                    <a href="{{ route('transaksi.keluar') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>No. Lot</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr @if($t->is_void) style="opacity:.5;" @endif>
                        <td>
                            <span class="badge badge-keluar">{{ $t->no_transaksi }}</span>
                            @if($t->is_void)
                            <span class="badge ms-1" style="background:#FEE2E2;color:#DC2626;font-size:.7rem;">Batal</span>
                            @endif
                        </td>
                        <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                        <td>{{ $t->nomor_lot ?: '—' }}</td>
                        <td class="text-end fw-semibold" style="color:{{ $t->is_void ? 'var(--text-muted)' : 'var(--danger)' }};">
                            {{ $t->quantity }} <small class="text-muted fw-normal">{{ $t->barang->satuan }}</small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('transaksi.show', $t) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada transaksi barang keluar</p></td></tr>
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
