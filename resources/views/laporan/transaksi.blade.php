@extends('layouts.app')
@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi</li>
@endsection

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Laporan Transaksi</h4>
        <p>Riwayat seluruh transaksi barang masuk, keluar, dan retur.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        {{-- Toggle view --}}
        <div class="btn-group" role="group">
            <button type="button" id="btnTile" class="btn btn-sm btn-primary" title="Tampilan Tile">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button type="button" id="btnTable" class="btn btn-sm btn-outline-secondary" title="Tampilan Tabel">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
        <a href="{{ route('laporan.export-excel', request()->all()) }}"
           class="btn btn-success d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Export CSV</span>
        </a>
        <a href="{{ route('laporan.print-transaksi', request()->only(['tanggal_dari','tanggal_sampai','jenis_transaksi'])) }}"
           target="_blank"
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-printer"></i>
            <span>Cetak PDF</span>
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
                    <div class="s-num" style="color:var(--info);">{{ $transaksis->whereIn('jenis_transaksi',['retur_customer','retur_produksi'])->sum('quantity') }}</div>
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
            <div class="col-md-4">
                <input type="text" name="nomor_lot" class="form-control"
                       placeholder="Filter No. Lot atau Nama Barang..." value="{{ request('nomor_lot') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Tampilkan</button>
                <a href="{{ route('laporan.transaksi') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-2 px-1">
    <span style="font-size:.82rem;color:var(--text-muted);">
        Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
    </span>
    <span class="badge" style="background:#F3F4F6;color:#374151;font-size:.8rem;border:1px solid var(--border);">{{ $transaksis->count() }} transaksi</span>
</div>

{{-- ═══ TILE VIEW ═══ --}}
<div id="viewTile">
    @forelse($transaksis as $t)
    @php
        $jenis = $t->jenis_transaksi;
        $isMasuk = $jenis === 'masuk';
        $isKeluar = $jenis === 'keluar';
        $jenisLabel = match($jenis) { 'masuk'=>'MASUK','keluar'=>'KELUAR','retur_customer'=>'RETUR','retur_produksi'=>'RETUR PROD', default=>strtoupper($jenis) };
        $accentClr  = match($jenis) { 'masuk'=>'var(--success)','keluar'=>'var(--danger)','retur_customer'=>'var(--info)', default=>'var(--warning)' };
        $accentBg   = match($jenis) { 'masuk'=>'var(--success-soft)','keluar'=>'var(--danger-soft)','retur_customer'=>'var(--info-soft)', default=>'var(--warning-soft)' };
        $icon       = match($jenis) { 'masuk'=>'box-arrow-in-down','keluar'=>'box-arrow-up','retur_customer'=>'arrow-return-left', default=>'arrow-counterclockwise' };
    @endphp
    <div class="txn-tile {{ $t->is_void ? 'txn-void' : '' }}">
        {{-- Left accent bar --}}
        <div class="txn-bar" style="background:{{ $accentClr }};"></div>

        {{-- Foto barang --}}
        <div class="txn-foto">
            @if($t->barang->foto)
                <img src="{{ asset('storage/' . $t->barang->foto) }}" alt="{{ $t->barang->nama_barang }}">
            @else
                <div class="txn-foto-placeholder"><i class="bi bi-box"></i></div>
            @endif
        </div>

        {{-- Content --}}
        <div class="txn-content">
            <div class="txn-top">
                <div>
                    <span class="txn-badge" style="background:{{ $accentBg }};color:{{ $accentClr }};">
                        <i class="bi bi-{{ $icon }} me-1"></i>{{ $jenisLabel }}
                    </span>
                    @if($t->is_void)
                    <span class="txn-badge ms-1" style="background:#FEE2E2;color:#DC2626;">BATAL</span>
                    @endif
                </div>
                <div class="txn-date">{{ $t->tanggal->format('d/m/Y') }}</div>
            </div>

            <a href="{{ route('transaksi.show', $t) }}" class="txn-name">{{ $t->barang->nama_barang }}</a>

            <div class="txn-meta">
                <span class="txn-qty" style="color:{{ $accentClr }};">
                    {{ $isMasuk ? '+' : ($isKeluar ? '-' : '±') }}{{ $t->quantity }}
                    <small>{{ $t->barang->satuan }}</small>
                </span>
                @if($t->nomor_lot)
                <span class="txn-lot"><i class="bi bi-upc me-1"></i>{{ $t->nomor_lot }}</span>
                @endif
                @if($t->nama_supplier && $isMasuk)
                <span class="txn-supplier"><i class="bi bi-truck me-1"></i>{{ $t->nama_supplier }}</span>
                @endif
            </div>

            <div class="txn-footer">
                <span class="txn-no">{{ $t->no_transaksi }}</span>
                <span class="txn-op"><i class="bi bi-person me-1"></i>{{ $t->user->username }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="empty-state"><i class="bi bi-journal-x"></i><p>Tidak ada transaksi pada periode ini</p></div>
    </div>
    @endforelse
</div>

{{-- ═══ TABLE VIEW ═══ --}}
<div id="viewTable" style="display:none;">
    <div class="card">
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
                            <th>Supplier</th>
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
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('transaksi.show', $t) }}" class="text-decoration-none">
                                    <span class="badge {{ $badgeClass }}">{{ $t->no_transaksi }}</span>
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $jenisColor }};color:#fff;font-size:.7rem;letter-spacing:.04em;">{{ $jenisLabel }}</span>
                            </td>
                            <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                            <td>{!! $t->nomor_lot ? '<code class="code-tag">'.e($t->nomor_lot).'</code>' : '—' !!}</td>
                            <td class="text-muted" style="font-size:.82rem;">{{ $t->nama_supplier ?: '—' }}</td>
                            <td class="text-end fw-semibold" style="color:{{ $jenisColor }};">
                                {{ $t->quantity }}
                                <small class="text-muted fw-normal">{{ $t->barang->satuan }}</small>
                            </td>
                            <td class="text-muted">{{ $t->user->username }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="empty-state"><i class="bi bi-journal-x"></i><p>Tidak ada transaksi pada periode ini</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ═══ TRANSACTION TILE ═══ */
.txn-tile {
    display: flex;
    align-items: stretch;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    margin-bottom: 10px;
    overflow: hidden;
    box-shadow: var(--shadow-xs);
    transition: box-shadow .15s, transform .15s;
}
.txn-tile:hover { box-shadow: var(--shadow-sm); transform: translateY(-1px); }
.txn-void { opacity: .55; }

.txn-bar { width: 4px; flex-shrink: 0; }

.txn-foto {
    width: 72px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 8px;
}
.txn-foto img {
    width: 52px; height: 52px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.txn-foto-placeholder {
    width: 52px; height: 52px;
    border-radius: 8px;
    background: #F3F4F6;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-subtle);
    font-size: 1.3rem;
    border: 1px solid var(--border);
}

.txn-content {
    flex: 1;
    padding: 12px 14px 10px 6px;
    min-width: 0;
}
.txn-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 4px;
}
.txn-badge {
    display: inline-flex;
    align-items: center;
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .05em;
    padding: 2px 8px;
    border-radius: 4px;
}
.txn-date {
    font-size: .75rem;
    color: var(--text-muted);
    font-weight: 500;
    white-space: nowrap;
}
.txn-name {
    display: block;
    font-size: .92rem;
    font-weight: 700;
    color: var(--text);
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -.01em;
}
.txn-name:hover { color: var(--brand); }
.txn-merk {
    font-size: .75rem;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.txn-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
    margin-bottom: 6px;
}
.txn-qty {
    font-size: 1.05rem;
    font-weight: 800;
    letter-spacing: -.02em;
}
.txn-qty small { font-size: .72rem; font-weight: 500; color: var(--text-muted); margin-left: 2px; }
.txn-lot, .txn-supplier {
    font-size: .72rem;
    color: var(--text-muted);
    background: #F3F4F6;
    padding: 2px 7px;
    border-radius: 4px;
    font-weight: 500;
}
.txn-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}
.txn-no {
    font-size: .7rem;
    font-family: 'SFMono-Regular', Consolas, monospace;
    color: var(--text-subtle);
}
.txn-op {
    font-size: .72rem;
    color: var(--text-muted);
    font-weight: 500;
}

@media (max-width: 768px) {
    .txn-foto { width: 60px; padding: 10px 6px; }
    .txn-foto img, .txn-foto-placeholder { width: 44px; height: 44px; }
    .txn-content { padding: 10px 12px 8px 4px; }
    .txn-name { font-size: .88rem; }
    .txn-qty { font-size: .95rem; }
}
</style>
@endpush

@push('scripts')
<script>
const btnTile  = document.getElementById('btnTile');
const btnTable = document.getElementById('btnTable');
const viewTile = document.getElementById('viewTile');
const viewTable= document.getElementById('viewTable');

const savedView = localStorage.getItem('laporan_txn_view') || 'tile';
if (savedView === 'table') switchTable();

function switchTile() {
    viewTile.style.display  = '';
    viewTable.style.display = 'none';
    btnTile.classList.replace('btn-outline-secondary','btn-primary');
    btnTable.classList.replace('btn-primary','btn-outline-secondary');
    localStorage.setItem('laporan_txn_view','tile');
}
function switchTable() {
    viewTile.style.display  = 'none';
    viewTable.style.display = '';
    btnTable.classList.replace('btn-outline-secondary','btn-primary');
    btnTile.classList.replace('btn-primary','btn-outline-secondary');
    localStorage.setItem('laporan_txn_view','table');
}

btnTile.addEventListener('click', switchTile);
btnTable.addEventListener('click', switchTable);
</script>
@endpush
