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
        <p>Klik barang untuk melihat detail stok per Lot.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="btn-group" role="group">
            <button type="button" id="btnTile" class="btn btn-sm btn-primary" title="Tampilan Tile">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button type="button" id="btnTable" class="btn btn-sm btn-outline-secondary" title="Tampilan Tabel">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
        <a href="{{ route('laporan.export-stok', request()->only(['search','group_by'])) }}"
           class="btn btn-success d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-excel"></i>
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.print-stok', request()->only(['search'])) }}"
           target="_blank"
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-printer"></i>
            <span>Cetak PDF</span>
        </a>

    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" id="stokForm" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Cari</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Nama barang..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Group By</label>
                <select name="group_by" class="form-select" id="groupBySelect">
                    <option value="">— Tanpa Grup —</option>
                    <option value="satuan"  {{ request('group_by') === 'satuan'  ? 'selected' : '' }}>Per Satuan</option>
                    <option value="merk"    {{ request('group_by') === 'merk'    ? 'selected' : '' }}>Per Merk</option>
                    <option value="tanggal" {{ request('group_by') === 'tanggal' ? 'selected' : '' }}>Per Tanggal Update</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                @if(request()->hasAny(['search','group_by','update_mode','update_tanggal','update_bulan','update_tahun']))
                <a href="{{ route('laporan.stok') }}" class="btn btn-outline-secondary flex-shrink-0">Reset</a>
                @endif
            </div>

            {{-- Filter Tanggal Update — muncul saat group_by=tanggal --}}
            <div class="col-12" id="filterTanggalBox" style="{{ request('group_by') === 'tanggal' ? '' : 'display:none;' }}">
                <div class="row g-2 align-items-end pt-1">
                    <div class="col-auto">
                        <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Filter Periode</label>
                        <select name="update_mode" class="form-select" id="updateMode" style="min-width:180px;">
                            <option value="">— Semua Periode —</option>
                            <option value="hari"  {{ request('update_mode') === 'hari'  ? 'selected' : '' }}>Per Hari</option>
                            <option value="bulan" {{ request('update_mode') === 'bulan' ? 'selected' : '' }}>Per Bulan & Tahun</option>
                            <option value="tahun" {{ request('update_mode') === 'tahun' ? 'selected' : '' }}>Per Tahun</option>
                        </select>
                    </div>

                    {{-- Per Hari --}}
                    <div class="col-auto" id="inputHariBox" style="display:none;">
                        <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Tanggal</label>
                        <input type="date" name="update_tanggal" class="form-control" value="{{ request('update_tanggal') }}">
                    </div>

                    {{-- Per Bulan & Tahun --}}
                    <div class="col-auto" id="inputBulanBox" style="display:none;">
                        <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Bulan</label>
                        <select name="update_bulan" class="form-select">
                            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num=>$nama)
                            <option value="{{ $num }}" {{ request('update_bulan') === $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto" id="inputTahunBulanBox" style="display:none;">
                        <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Tahun</label>
                        <input type="number" name="update_tahun" class="form-control" style="width:100px;"
                               value="{{ request('update_tahun', date('Y')) }}" min="2020" max="2099">
                    </div>

                    {{-- Per Tahun saja --}}
                    <div class="col-auto" id="inputTahunBox" style="display:none;">
                        <label class="form-label" style="font-size:.78rem;font-weight:600;color:var(--text-muted);">Tahun</label>
                        <input type="number" name="update_tahun" class="form-control" style="width:100px;"
                               value="{{ request('update_tahun', date('Y')) }}" min="2020" max="2099">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ═══ TILE VIEW (Grid 2 Kolom) ═══ --}}
<div id="viewTile">
    @php $prevGroupTile = null; @endphp
    <div class="row g-3">
    @forelse($barangs as $b)
        @php
            $stok    = $b->getStok();
            $habis   = $b->isStokMinimum();
            $lots    = $b->stoks->sortBy('nomor_lot');
            $curGroupTile = match($groupBy) {
                'satuan'  => $b->satuan,
                'merk'    => ($b->merk ?: '— Tanpa Merk —'),
                'tanggal' => ($b->last_update ? \Carbon\Carbon::parse($b->last_update)->format('d/m/Y') : '— Belum Ada Update —'),
                default   => null,
            };
        @endphp

        @if($groupBy && $curGroupTile !== $prevGroupTile)
            <div class="col-12">
                <div class="stok-group-header mt-2">
                    <i class="bi bi-tag-fill me-2" style="color:var(--brand);font-size:.8rem;"></i>{{ $curGroupTile }}
                </div>
            </div>
            @php $prevGroupTile = $curGroupTile; @endphp
        @endif

        <div class="col-6">
            <div class="stok-tile-new {{ $habis ? 'border-danger' : '' }}" onclick="toggleLotDetails('tile-lot-{{ $b->id }}', this)">
                <div class="stok-tile-main">
                    <div class="stok-tile-img-wrap">
                        @if($b->foto)
                            <img src="{{ asset('storage/' . $b->foto) }}" alt="{{ $b->nama_barang }}" class="stok-tile-img-ec">
                        @else
                            <div class="stok-tile-ph-ec"><i class="bi bi-person-bounding-box"></i></div>
                        @endif
                    </div>
                    <div class="stok-tile-content p-2">
                        <div class="stok-tile-title">{{ $b->nama_barang }}</div>
                        <div class="stok-tile-sub mb-1"><code style="font-size:.65rem;color:var(--text-subtle);">{{ $b->kode_barang }}</code></div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="stok-tile-value {{ $habis ? 'text-danger' : 'text-success' }}">
                                {{ number_format($stok) }}<small class="text-muted" style="font-size:.6rem;font-weight:500;margin-left:2px;">{{ $b->satuan }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                @if($habis)
                                    <span class="stok-badge-ec stok-badge-warn">Min</span>
                                @endif
                                <i class="bi bi-chevron-down stok-tile-chevron-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden Lot Details --}}
                <div id="tile-lot-{{ $b->id }}" class="stok-tile-details" style="display:none;">
                    <div class="p-3 pt-0 border-top">
                        <div class="lot-table-mini mt-2">
                            @if($lots->isNotEmpty())
                                @foreach($lots as $s)
                                <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                    <span class="text-muted small"><i class="bi bi-upc me-1"></i>{{ $s->nomor_lot ?? 'Tanpa Lot' }}</span>
                                    <span class="fw-bold small">{{ $s->stok_akhir }} {{ $b->satuan }}</span>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted small py-2">Tidak ada detail lot</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card"><div class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada data barang</p></div></div>
        </div>
    @endforelse
    </div>

    @if($barangs->hasPages())
    <div class="mt-4">{{ $barangs->links() }}</div>
    @endif
</div>

{{-- ═══ TABLE VIEW ═══ --}}
<div id="viewTable" style="display:none;">
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
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
                            'satuan'  => $b->satuan,
                            'merk'    => ($b->merk ?: '— Tanpa Merk —'),
                            'tanggal' => ($b->last_update ? \Carbon\Carbon::parse($b->last_update)->format('d/m/Y') : '— Belum Ada Update —'),
                            default   => null,
                        };
                    @endphp
                    @if($groupBy && $curGroup !== $prevGroup)
                    <tr class="group-header-row">
                        <td colspan="7">
                            <i class="bi bi-tag-fill me-2" style="color:var(--brand);font-size:.8rem;"></i>
                            <span>{{ $curGroup }}</span>
                        </td>
                    </tr>
                    @php $prevGroup = $curGroup; @endphp
                    @endif

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

                    @if($hasLots)
                    <tr class="lot-detail-row" id="lot-{{ $b->id }}" style="display:none;">
                        <td colspan="7" style="padding:0;background:#FAFBFC;border-bottom:2px solid var(--border);">
                            <div style="padding:10px 52px 14px;">
                                <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:8px;">Detail Per Lot</div>
                                <table class="table table-sm mb-0" style="background:transparent;">
                                    <thead>
                                        <tr style="background:transparent;">
                                            <th style="background:transparent;font-size:.68rem;padding:6px 12px;">Lot</th>
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
                                            <td class="text-end fw-bold" style="color:var(--text);border-bottom:1px solid var(--border-soft);padding:8px 12px;">
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
</div>

@push('styles')
<style>
/* ═══ E-COMMERCE TILE (PT GCW) ═══ */
.stok-tile-new {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all .2s ease;
    box-shadow: var(--shadow-xs);
    height: 100%;
    display: flex;
    flex-direction: column;
}
.stok-tile-new:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
    border-color: var(--brand);
}
.stok-tile-main { display: flex; flex-direction: row; flex: 1; align-items: flex-start; }
/* Foto area — portrait 3×4 */
.stok-tile-img-wrap {
    width: 38px;
    height: 50px;
    background: var(--bg);
    overflow: hidden;
    flex-shrink: 0;
    border-radius: 4px;
    margin: 8px 0 8px 8px;
    border: 1px solid var(--border-soft);
}
.stok-tile-img-ec {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.stok-tile-ph-ec {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-subtle);
    font-size: .75rem;
}
/* Badge status */
.stok-badge-ec {
    font-size: .56rem;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 20px;
    letter-spacing: .02em;
}
.stok-badge-ok  { background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; }
.stok-badge-warn{ background:#FEF2F2; color:#DC2626; border:1px solid #FCA5A5; }
/* Content area */
.stok-tile-content { flex: 1; }
.stok-tile-title {
    font-size: .82rem;
    font-weight: 700;
    color: var(--text);
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.3;
}
.stok-tile-sub { margin-top: 2px; }
.stok-tile-value {
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: -.02em;
    line-height: 1;
}
.stok-tile-chevron-sm {
    color: var(--text-subtle);
    font-size: .8rem;
    transition: transform .3s;
    flex-shrink: 0;
}
.stok-tile-new.open .stok-tile-chevron-sm { transform: rotate(180deg); color: var(--brand); }
/* Lot details */
.stok-tile-details {
    background: var(--bg);
    border-top: 1px solid var(--border-soft);
    animation: slideDown .25s ease-out;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
.lot-table-mini .text-muted { color: var(--text-muted) !important; }
.lot-table-mini .fw-bold { color: var(--text) !important; }
.lot-table-mini .border-bottom { border-bottom-color: var(--border-soft) !important; }

/* Indikator Group */
.stok-group-header {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 7px 14px;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-muted);
    margin-bottom: 8px;
    margin-top: 4px;
}

/* ═══ OLD TILE (Keep for reference) ═══ */
.stok-tile {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 8px;
    box-shadow: var(--shadow-xs);
    transition: box-shadow .15s, transform .15s;
}
.stok-tile:hover { box-shadow: var(--shadow-sm); transform: translateY(-1px); }
.stok-tile-warn { border-left: 3px solid var(--danger); }

.stok-tile-foto {
    flex-shrink: 0;
}
.stok-tile-foto img {
    width: 56px; height: 56px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.stok-tile-foto-ph {
    width: 56px; height: 56px;
    border-radius: 8px;
    background: #F3F4F6;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-subtle);
    font-size: 1.4rem;
    border: 1px solid var(--border);
}

.stok-tile-body { flex: 1; min-width: 0; }
.stok-tile-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 4px;
}
.stok-tile-info { min-width: 0; }
.stok-tile-name {
    font-size: .92rem;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -.01em;
}
.stok-tile-merk {
    font-size: .75rem;
    color: var(--text-muted);
}
.stok-tile-qty {
    font-size: 1.4rem;
    font-weight: 800;
    letter-spacing: -.03em;
    line-height: 1;
    white-space: nowrap;
    flex-shrink: 0;
}
.stok-tile-qty small {
    font-size: .7rem;
    font-weight: 500;
    color: var(--text-muted);
    margin-left: 2px;
}
.stok-tile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
    margin-bottom: 6px;
}
.stok-tile-lots {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.stok-lot-chip {
    font-size: .7rem;
    background: #F3F4F6;
    color: var(--text-muted);
    padding: 2px 8px;
    border-radius: 4px;
    border: 1px solid var(--border);
}
.stok-lot-chip strong { color: var(--text); }

.stok-group-header {
    background: #F3F4F6;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 7px 14px;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #374151;
    margin-bottom: 8px;
    margin-top: 4px;
}

/* ═══ TABLE STYLES ═══ */
.lot-chevron {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px; height: 26px;
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
.stok-row.has-lots:hover .lot-chevron { background: #E5E7EB; border-color: #9CA3AF; }
.stok-row.open .lot-chevron { background: var(--brand-soft); border-color: var(--brand); }
.stok-row.open .lot-chevron .toggle-icon { color: var(--brand); transform: rotate(90deg); }
.stok-row.has-lots:hover { background: #F9FAFB; }
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

@media (max-width: 768px) {
    .stok-tile-foto img, .stok-tile-foto-ph { width: 48px; height: 48px; }
    .stok-tile-qty { font-size: 1.2rem; }
    .stok-tile-name { font-size: .88rem; }
}

@media print {
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .btn, .card-footer, #viewTile { display: none !important; }
    #viewTable { display: block !important; }
    .lot-detail-row { display: table-row !important; }
    .lot-chevron { display: none !important; }
}
</style>
@endpush

@push('scripts')
<script>
// Toggle Lot Details for New Tile View
function toggleLotDetails(id, el) {
    var detail = document.getElementById(id);
    var isOpen = el.classList.contains('open');

    if (isOpen) {
        el.classList.remove('open');
        detail.style.display = 'none';
    } else {
        el.classList.add('open');
        detail.style.display = 'block';
    }
}

// View toggle
const btnTile  = document.getElementById('btnTile');
const btnTable = document.getElementById('btnTable');
const viewTile = document.getElementById('viewTile');
const viewTable= document.getElementById('viewTable');

const savedView = localStorage.getItem('laporan_stok_view') || 'tile';
if (savedView === 'table') switchTable();

function switchTile() {
    viewTile.style.display  = '';
    viewTable.style.display = 'none';
    btnTile.classList.replace('btn-outline-secondary','btn-primary');
    btnTable.classList.replace('btn-primary','btn-outline-secondary');
    localStorage.setItem('laporan_stok_view','tile');
}
function switchTable() {
    viewTile.style.display  = 'none';
    viewTable.style.display = '';
    btnTable.classList.replace('btn-outline-secondary','btn-primary');
    btnTile.classList.replace('btn-primary','btn-outline-secondary');
    localStorage.setItem('laporan_stok_view','table');
}
btnTile.addEventListener('click', switchTile);
btnTable.addEventListener('click', switchTable);

// Table lot expand
document.querySelectorAll('.stok-row.has-lots').forEach(function(row) {
    row.addEventListener('click', function() {
        var detail = document.getElementById(row.dataset.target);
        var isOpen = row.classList.contains('open');
        row.classList.toggle('open', !isOpen);
        detail.style.display = isOpen ? 'none' : 'table-row';
    });
});
</script>
<script>
// ── Filter Tanggal Update ──
const groupBySelect    = document.getElementById('groupBySelect');
const filterTanggalBox = document.getElementById('filterTanggalBox');
const updateMode       = document.getElementById('updateMode');
const inputHariBox     = document.getElementById('inputHariBox');
const inputBulanBox    = document.getElementById('inputBulanBox');
const inputTahunBulanBox = document.getElementById('inputTahunBulanBox');
const inputTahunBox    = document.getElementById('inputTahunBox');

function syncFilterVisibility() {
    const isTanggal = groupBySelect.value === 'tanggal';
    filterTanggalBox.style.display = isTanggal ? '' : 'none';
    if (!isTanggal) return;

    const mode = updateMode.value;
    inputHariBox.style.display      = mode === 'hari'  ? '' : 'none';
    inputBulanBox.style.display     = mode === 'bulan' ? '' : 'none';
    inputTahunBulanBox.style.display= mode === 'bulan' ? '' : 'none';
    inputTahunBox.style.display     = mode === 'tahun' ? '' : 'none';
}

groupBySelect.addEventListener('change', syncFilterVisibility);
updateMode.addEventListener('change', syncFilterVisibility);
syncFilterVisibility();
</script>
@endpush

@endsection
