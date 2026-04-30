@extends('layouts.app')
@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Laporan Transaksi</h4>
        <p>Riwayat seluruh transaksi barang masuk, keluar, dan retur.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.export-excel', request()->all()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
        </a>
        <a href="{{ route('laporan.print-transaksi', request()->only(['tanggal_dari','tanggal_sampai','jenis_transaksi','merk'])) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-pdf me-2"></i>Cetak PDF
        </a>
    </div>
</div>

{{-- Summary strip --}}
@if($transaksis->count())
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="s-icon" style="background:var(--success-soft);color:var(--success);"><i class="bi bi-box-arrow-in-down"></i></div>
                <div>
                    <div class="s-label">Total Masuk</div>
                    <div class="s-num" style="color:var(--success);">{{ $transaksis->where('jenis_transaksi','masuk')->sum('quantity') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="s-icon" style="background:var(--danger-soft);color:var(--danger);"><i class="bi bi-box-arrow-up"></i></div>
                <div>
                    <div class="s-label">Total Keluar</div>
                    <div class="s-num" style="color:var(--danger);">{{ $transaksis->where('jenis_transaksi','keluar')->sum('quantity') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="s-icon" style="background:var(--info-soft);color:var(--info);"><i class="bi bi-arrow-return-left"></i></div>
                <div>
                    <div class="s-label">Total Retur</div>
                    <div class="s-num" style="color:var(--info);">{{ $transaksis->where('jenis_transaksi','retur_customer')->sum('quantity') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            {{-- Periode quick buttons --}}
            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap mb-1">
                    @foreach(['hari'=>'Hari Ini','bulan'=>'Bulan Ini','tahun'=>'Tahun Ini'] as $val=>$label)
                    <a href="{{ route('laporan.transaksi', array_merge(request()->except('periode','tanggal_dari','tanggal_sampai'), ['periode'=>$val])) }}"
                       class="btn btn-sm {{ request('periode')===$val ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
                    @endforeach
                    @if(request('periode'))
                    <a href="{{ route('laporan.transaksi', request()->except('periode')) }}" class="btn btn-sm btn-outline-secondary">Range Tanggal</a>
                    @endif
                </div>
            </div>
            @if(!in_array(request('periode'),['hari','bulan','tahun']))
            <div class="col-md-2">
                <input type="date" name="tanggal_dari" class="form-control" value="{{ $tanggalDari }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal_sampai" class="form-control" value="{{ $tanggalSampai }}">
            </div>
            @else
            <input type="hidden" name="periode" value="{{ request('periode') }}">
            @endif
            <div class="col-md-2">
                <select name="jenis_transaksi" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="masuk" {{ request('jenis_transaksi') === 'masuk' ? 'selected' : '' }}>Masuk</option>
                    <option value="keluar" {{ request('jenis_transaksi') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="retur_customer" {{ request('jenis_transaksi') === 'retur_customer' ? 'selected' : '' }}>Retur Customer</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="merk" class="form-select">
                    <option value="">Semua Merk</option>
                    @foreach($merks as $m)
                    <option value="{{ $m }}" {{ request('merk') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="nomor_lot" class="form-control"
                       placeholder="No. Lot..." value="{{ request('nomor_lot') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Tampilkan</button>
                <a href="{{ route('laporan.transaksi') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</span>
        <span class="badge" style="background:#F3F4F6;color:#374151;font-size:.8rem;border:1px solid var(--border);">{{ $transaksis->count() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>No. Lot</th>
                        <th class="text-end">Qty</th>
                        <th>Operator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    @php
                        $jenis = $t->jenis_transaksi;
                        $badgeClass = $jenis === 'masuk' ? 'badge-masuk' : ($jenis === 'keluar' ? 'badge-keluar' : 'badge-retur');
                        $jenisLabel = $jenis === 'masuk' ? 'MASUK' : ($jenis === 'keluar' ? 'KELUAR' : 'RETUR');
                        $jenisColor = $jenis === 'masuk' ? 'var(--success)' : ($jenis === 'keluar' ? 'var(--danger)' : 'var(--info)');
                        $qtyColor   = $jenis === 'masuk' ? 'var(--success)' : ($jenis === 'keluar' ? 'var(--danger)' : 'var(--info)');
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('transaksi.show', $t) }}" class="text-decoration-none">
                                <span class="badge {{ $badgeClass }}">{{ $t->no_transaksi }}</span>
                            </a>
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $jenisColor }};color:#fff;font-size:.7rem;letter-spacing:.04em;">
                                {{ $jenisLabel }}
                            </span>
                        </td>
                        <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                        <td>{!! $t->nomor_lot ? '<code class="code-tag">'.e($t->nomor_lot).'</code>' : '—' !!}</td>
                        <td class="text-end fw-semibold" style="color:{{ $qtyColor }};">
                            {{ $t->quantity }}
                            <small class="text-muted fw-normal">{{ $t->barang->satuan }}</small>
                        </td>
                        <td class="text-muted">{{ $t->user->username }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-journal-x"></i><p>Tidak ada transaksi pada periode ini</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
