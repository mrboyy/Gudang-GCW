@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('page-title', 'Barang Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Barang Keluar</li>
@endsection

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Barang Keluar</h4>
        <p>Catatan seluruh barang yang keluar dari gudang.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('retur.create-combined') }}" class="btn btn-outline-info">
            <i class="bi bi-arrow-return-left me-2"></i>Catat Retur
        </a>
        <a href="{{ route('transaksi.create-keluar') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Catat Barang Keluar
        </a>
    </div>
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
                        <th class="d-none d-md-table-cell">No. SJ</th>
                        <th class="d-none d-md-table-cell">Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="d-none d-lg-table-cell">Lot</th>
                        <th class="d-none d-md-table-cell">Tujuan</th>
                        <th class="text-end">Jumlah</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prevTglKeluar = null; @endphp
                    @forelse($transaksis as $t)
                    @php
                        $tglStr2 = $t->tanggal->locale('id')->isoFormat('dddd, D MMMM Y');
                    @endphp
                    @if($tglStr2 !== $prevTglKeluar)
                    <tr style="background:var(--bg);"><td colspan="7" style="padding:7px 18px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);border-bottom:1px solid var(--border);">
                        <i class="bi bi-calendar3 me-2" style="color:var(--brand);"></i>{{ $tglStr2 }}</td></tr>
                    @php $prevTglKeluar = $tglStr2; @endphp
                    @endif
                    @php
                        $isKeluar = $t->jenis_transaksi === 'keluar';
                        $badgeClass = match($t->jenis_transaksi) {
                            'keluar'         => 'badge-keluar',
                            'retur_customer' => 'badge-retur',
                            'retur_produksi' => 'badge-retur-p',
                            default          => 'badge-secondary'
                        };
                        $label = match($t->jenis_transaksi) {
                            'keluar'         => 'BK',
                            'retur_customer' => 'RC',
                            'retur_produksi' => 'RP',
                            default          => '??'
                        };
                    @endphp
                    <tr @if($t->is_void) style="opacity:.5;" @endif>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $t->no_transaksi }}</span>
                            @if($t->is_void)
                            <span class="badge ms-1" style="background:#fff5f5;color:#991b1b;font-size:.7rem;border:1px solid #fecaca;">Batal</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell" style="font-size:.82rem;font-weight:600;">
                            {{ $t->no_surat_jalan ?: '—' }}
                        </td>
                        <td class="d-none d-md-table-cell">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                        <td class="d-none d-lg-table-cell">{{ $t->nomor_lot ?: '—' }}</td>
                        <td class="d-none d-md-table-cell">
                            @if($t->tujuan_keluar)
                                @if(str_contains($t->tujuan_keluar, 'Internal:'))
                                    <span class="badge" style="font-size:.7rem;background:#f0f7ff;color:#1e40af;border:1px solid #dbeafe;">Internal</span>
                                    <div class="text-muted" style="font-size:.75rem;">{{ str_replace('Internal: ', '', $t->tujuan_keluar) }}</div>
                                @else
                                    <span class="badge" style="font-size:.7rem;border:1px solid var(--border);background:var(--bg);color:var(--text);">{{ $t->tujuan_keluar }}</span>
                                @endif
                            @elseif($t->no_ref)
                                <div class="text-muted" style="font-size:.75rem;">Ref: <strong>{{ $t->no_ref }}</strong></div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold" style="color:{{ $t->is_void ? 'var(--text-muted)' : ($isKeluar ? 'var(--danger)' : 'var(--success)') }};">
                            {{ $isKeluar ? '-' : '+' }}{{ $t->quantity }} <small class="text-muted fw-normal">{{ $t->barang->satuan }}</small>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                @if($isKeluar && !$t->is_void)
                                @php
                                    $isCustomerTx = str_starts_with($t->tujuan_keluar ?? '', 'Customer');
                                    $returHref = $isCustomerTx
                                        ? route('retur.create', ['no_ref' => $t->no_surat_jalan])
                                        : route('retur.produksi.create', ['no_ref' => $t->no_surat_jalan]);
                                @endphp
                                <a href="{{ $returHref }}" class="btn btn-xs btn-outline-info" style="font-size:.65rem;padding:2px 6px;font-weight:700;">
                                    RETUR
                                </a>
                                @endif
                                <a href="{{ route('transaksi.show', $t) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada transaksi barang keluar</p></td></tr>
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
