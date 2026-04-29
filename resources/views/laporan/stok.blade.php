@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Laporan Stok Barang</h4>
        <p>Klik baris barang untuk melihat detail stok per nomor lot.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.export-stok') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Excel
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
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                @if(request('search'))
                <a href="{{ route('laporan.stok') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:80px;"></th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Satuan</th>
                        <th class="text-end">Stok Total</th>
                        <th class="text-end">Min.</th>
                        <th class="text-center">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $b)
                    @php
                        $stok      = $b->getStok();
                        $habis     = $b->isStokMinimum();
                        $lots      = $b->stoks->sortByDesc('tanggal_update');
                        $hasLots   = $b->stoks->isNotEmpty();
                    @endphp

                    {{-- Main row --}}
                    <tr class="stok-row {{ $hasLots ? 'has-lots' : '' }}"
                        data-target="lot-{{ $b->id }}"
                        style="cursor:{{ $hasLots ? 'pointer' : 'default' }};">
                        <td style="padding:10px 8px;white-space:nowrap;">
                            @if($hasLots)
                            <span class="lot-toggle-btn">
                                <i class="bi bi-chevron-right toggle-icon"></i>
                                {{ $lots->count() }} lot
                            </span>
                            @endif
                        </td>
                        <td><code class="code-tag">{{ $b->kode_barang }}</code></td>
                        <td class="fw-semibold">{{ $b->nama_barang }}</td>
                        <td class="text-muted">{{ $b->merk ?: '—' }}</td>
                        <td>{{ $b->satuan }}</td>
                        <td class="text-end fw-bold"
                            style="color:{{ $habis ? 'var(--danger)' : 'var(--success)' }};">{{ $stok }}</td>
                        <td class="text-end text-muted">{{ $b->stok_minimum }}</td>
                        <td class="text-center">
                            @if($habis)
                            <span class="badge badge-status-warn">Hampir Habis</span>
                            @else
                            <span class="badge badge-status-ok">Normal</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Lot detail sub-row --}}
                    @if($hasLots)
                    <tr class="lot-detail-row" id="lot-{{ $b->id }}" style="display:none;">
                        <td colspan="8" style="padding:0;background:#FAFBFC;border-bottom:2px solid var(--border);">
                            <div style="padding:10px 52px 14px;">
                                <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;
                                            letter-spacing:.07em;color:var(--text-muted);margin-bottom:8px;">
                                    Detail Per Nomor Lot
                                </div>
                                <table class="table table-sm mb-0" style="background:transparent;">
                                    <thead>
                                        <tr style="background:transparent;">
                                            <th style="background:transparent;font-size:.68rem;padding:6px 12px;">Nomor Lot</th>
                                            <th class="text-end" style="background:transparent;font-size:.68rem;padding:6px 12px;">Stok Akhir</th>
                                            <th style="background:transparent;font-size:.68rem;padding:6px 12px;">Update Terakhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lots as $s)
                                        <tr style="background:transparent;">
                                            <td style="border-bottom:1px solid var(--border-soft);padding:8px 12px;">
                                                @if($s->nomor_lot)
                                                <code class="code-tag">{{ $s->nomor_lot }}</code>
                                                @else
                                                <span class="text-muted" style="font-style:italic;font-size:.8rem;">Tanpa Lot</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold"
                                                style="color:{{ $s->stok_akhir <= 0 ? 'var(--danger)' : 'var(--text)' }};
                                                       border-bottom:1px solid var(--border-soft);padding:8px 12px;">
                                                {{ $s->stok_akhir }}
                                                <small class="text-muted fw-normal">{{ $b->satuan }}</small>
                                            </td>
                                            <td class="text-muted"
                                                style="font-size:.82rem;border-bottom:1px solid var(--border-soft);padding:8px 12px;">
                                                {{ $s->tanggal_update->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endif

                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="bi bi-inbox"></i><p>Tidak ada data barang</p>
                        </td>
                    </tr>
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
<style>
.stok-row.has-lots:hover { background: #F5F7FA; }
.lot-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: var(--brand-soft);
    color: var(--brand);
    border: 1px solid #FDDDB5;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: .75rem;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
    user-select: none;
    transition: background .15s, border-color .15s;
}
.stok-row.has-lots:hover .lot-toggle-btn {
    background: #FFE9CC;
    border-color: var(--brand);
}
.stok-row.open .lot-toggle-btn {
    background: var(--brand);
    color: #fff;
    border-color: var(--brand);
}
.stok-row.open .toggle-icon { transform: rotate(90deg); }
.lot-toggle-btn .toggle-icon { transition: transform .2s; display: inline-block; font-size: .7rem; }
@media print {
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .btn, .card-footer { display: none !important; }
    .lot-detail-row { display: table-row !important; }
    .toggle-icon { display: none !important; }
}
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.stok-row.has-lots').forEach(function(row) {
    row.addEventListener('click', function() {
        var targetId = row.dataset.target;
        var detail   = document.getElementById(targetId);
        var isOpen   = row.classList.contains('open');
        row.classList.toggle('open', !isOpen);
        detail.style.display = isOpen ? 'none' : 'table-row';
    });
});
</script>
@endpush

@endsection
