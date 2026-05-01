@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Laporan Stok</li>
@endsection

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Laporan Stok Barang</h4>
        <p>Klik baris barang untuk melihat detail stok per nomor lot.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.export-stok', request()->only(['search','merk','group_by'])) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
        </a>
        <a href="{{ route('laporan.print-stok', request()->only(['search','merk'])) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-pdf me-2"></i>Cetak PDF
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Cari</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Nama barang atau merk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Filter Merk</label>
                <select name="merk" class="form-select">
                    <option value="">Semua Merk</option>
                    @foreach($merks as $m)
                    <option value="{{ $m }}" {{ request('merk') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Group By</label>
                <select name="group_by" class="form-select">
                    <option value="">— Tanpa Grup —</option>
                    <option value="merk"   {{ request('group_by') === 'merk'   ? 'selected' : '' }}>Merk</option>
                    <option value="satuan" {{ request('group_by') === 'satuan' ? 'selected' : '' }}>Satuan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                @if(request()->hasAny(['search','merk','group_by']))
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
                        <th style="width:40px;"></th>
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
                    @php $prevGroup = null; @endphp
                    @forelse($barangs as $b)
                    @php
                        $stok    = $b->getStok();
                        $habis   = $b->isStokMinimum();
                        $lots    = $b->stoks->sortBy('nomor_lot');
                        $hasLots = $b->stoks->isNotEmpty();

                        $curGroup = match($groupBy) {
                            'merk'   => ($b->merk ?: '(Tanpa Merk)'),
                            'satuan' => $b->satuan,
                            default  => null,
                        };
                    @endphp

                    {{-- Group header --}}
                    @if($groupBy && $curGroup !== $prevGroup)
                    <tr class="group-header-row">
                        <td colspan="8">
                            <i class="bi bi-tag-fill me-2" style="color:var(--brand);font-size:.8rem;"></i>
                            <span>{{ $curGroup }}</span>
                        </td>
                    </tr>
                    @php $prevGroup = $curGroup; @endphp
                    @endif

                    {{-- Baris barang --}}
                    <tr class="stok-row {{ $hasLots ? 'has-lots' : '' }}"
                        data-target="lot-{{ $b->id }}"
                        style="cursor:{{ $hasLots ? 'pointer' : 'default' }};">
                        <td class="text-center" style="padding:10px 6px;">
                            @if($hasLots)
                            <span class="lot-chevron">
                                <i class="bi bi-chevron-right toggle-icon"></i>
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

                    {{-- Lot detail --}}
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
                                            <th class="text-end" style="background:transparent;font-size:.68rem;padding:6px 12px;">Stok</th>
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
                                                style="color:var(--text);border-bottom:1px solid var(--border-soft);padding:8px 12px;">
                                                {{ $s->stok_akhir }}
                                                <small class="text-muted fw-normal">{{ $b->satuan }}</small>
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
/* Toggle chevron — lingkaran kecil abu */
.lot-chevron {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #F3F4F6;
    border: 1.5px solid #D1D5DB;
    flex-shrink: 0;
}
.lot-chevron .toggle-icon {
    font-size: .82rem;
    color: #4B5563;
    transition: transform .2s;
    display: inline-block;
}
.stok-row.has-lots:hover .lot-chevron {
    background: #E5E7EB;
    border-color: #9CA3AF;
}
.stok-row.open .lot-chevron {
    background: var(--brand-soft);
    border-color: var(--brand);
}
.stok-row.open .lot-chevron .toggle-icon {
    color: var(--brand);
    transform: rotate(90deg);
}
.stok-row.has-lots:hover { background: #F9FAFB; }

/* Group header row */
.group-header-row td {
    background: #F3F4F6;
    border-top: 2px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 8px 18px;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #374151;
}

@media print {
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .btn, .card-footer { display: none !important; }
    .lot-detail-row { display: table-row !important; }
    .lot-chevron { display: none !important; }
}
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.stok-row.has-lots').forEach(function(row) {
    row.addEventListener('click', function() {
        var detail = document.getElementById(row.dataset.target);
        var isOpen = row.classList.contains('open');
        row.classList.toggle('open', !isOpen);
        detail.style.display = isOpen ? 'none' : 'table-row';
    });
});
</script>
@endpush

@endsection
