@extends('layouts.app')
@section('title', 'Daftar Barang')
@section('page-title', 'Daftar Barang')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Daftar Barang</h4>
        <p>Seluruh barang yang tersimpan di gudang.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="btn-group" role="group">
            <button type="button" id="btnTile" class="btn btn-sm btn-outline-secondary" onclick="setView('tile')" title="Tampilan Tile">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button type="button" id="btnTable" class="btn btn-sm btn-outline-secondary" onclick="setView('table')" title="Tampilan Tabel">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
        <a href="{{ route('barang.create') }}" class="btn btn-primary">Tambah Barang</a>
        @endif
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control"
                       placeholder="Cari nama barang, kode, atau merk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                @if(request('search'))
                <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- TILE VIEW --}}
<div id="viewTile">
    <div class="row g-3">
        @forelse($barangs as $b)
        @php $stokSekarang = $b->getStok(); $habis = $b->isStokMinimum(); @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <div class="barang-card h-100">
                <div class="barang-foto">
                    @if($b->foto)
                    <img src="{{ Storage::url($b->foto) }}" alt="{{ e($b->nama_barang) }}" loading="lazy">
                    @else
                    <div class="barang-foto-placeholder"><i class="bi bi-box-seam"></i></div>
                    @endif
                    @if($habis)
                    <span class="barang-badge-warn">Hampir Habis</span>
                    @endif
                </div>
                <div class="barang-info">
                    <div class="barang-nama">{{ $b->nama_barang }}</div>
                    <div class="barang-merk">{{ $b->merk ?: '—' }}</div>
                    <div class="barang-stok {{ $habis ? 'text-danger' : 'text-success' }}">
                        {{ $stokSekarang }}
                        <span style="font-size:.75rem;font-weight:500;opacity:.7;">{{ $b->satuan }}</span>
                    </div>
                    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
                    <div class="barang-aksi">
                        <a href="{{ route('barang.edit', $b) }}" class="btn btn-sm btn-outline-secondary w-100">Edit</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card"><div class="card-body empty-state"><i class="bi bi-inbox"></i><p>Tidak ada data barang</p></div></div>
        </div>
        @endforelse
    </div>
    @if($barangs->hasPages())
    <div class="mt-3 d-flex justify-content-center">{{ $barangs->links() }}</div>
    @endif
</div>

{{-- TABLE VIEW --}}
<div id="viewTable" style="display:none;">
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">Foto</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Merk</th>
                            <th>Satuan</th>
                            <th class="text-end">Stok</th>
                            @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
                            <th class="text-end">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangs as $b)
                        @php $stokSekarang = $b->getStok(); $habis = $b->isStokMinimum(); @endphp
                        <tr>
                            <td>
                                @if($b->foto)
                                <img src="{{ Storage::url($b->foto) }}" loading="lazy"
                                     style="width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid var(--border);" alt="">
                                @else
                                <div style="width:36px;height:36px;border-radius:6px;background:var(--bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-subtle);">
                                    <i class="bi bi-box-seam" style="font-size:.85rem;"></i>
                                </div>
                                @endif
                            </td>
                            <td><code class="code-tag">{{ $b->kode_barang }}</code></td>
                            <td class="fw-semibold">{{ $b->nama_barang }}</td>
                            <td class="text-muted">{{ $b->merk ?: '—' }}</td>
                            <td>{{ $b->satuan }}</td>
                            <td class="text-end">
                                <span class="fw-bold" style="color:{{ $habis ? 'var(--danger)' : 'var(--success)' }};">{{ $stokSekarang }}</span>
                                @if($habis)<i class="bi bi-exclamation-triangle-fill ms-1" style="color:var(--danger);font-size:.8rem;"></i>@endif
                            </td>
                            @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('barang.edit', $b) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('barang.destroy', $b) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Nonaktifkan barang ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            @endif
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
</div>

@push('styles')
<style>
.barang-card {
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--surface);
    box-shadow: var(--shadow-xs);
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
    display: flex;
    flex-direction: column;
}
.barang-card:hover { box-shadow: var(--shadow-sm); transform: translateY(-2px); }

.barang-foto {
    position: relative;
    width: 100%;
    aspect-ratio: 1;
    background: var(--bg);
    overflow: hidden;
}
.barang-foto img { width:100%; height:100%; object-fit:cover; }
.barang-foto-placeholder {
    width:100%; height:100%;
    display:flex; align-items:center; justify-content:center;
    color:var(--text-subtle); font-size:2.5rem;
}
.barang-badge-warn {
    position: absolute; top:8px; right:8px;
    background: var(--danger); color:#fff;
    font-size:.6rem; font-weight:700;
    padding:2px 7px; border-radius:4px; letter-spacing:.03em;
}
.barang-info { padding:12px; flex:1; display:flex; flex-direction:column; gap:3px; }
.barang-nama {
    font-size:.875rem; font-weight:700; color:var(--text); line-height:1.3;
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
.barang-merk { font-size:.75rem; color:var(--text-muted); margin-bottom:4px; }
.barang-stok { font-size:1.15rem; font-weight:800; letter-spacing:-.02em; margin-top:auto; padding-top:8px; }
.barang-aksi { margin-top:8px; }
</style>
@endpush

@push('scripts')
<script>
function setView(v) {
    localStorage.setItem('barangView', v);
    document.getElementById('viewTile').style.display  = v === 'tile'  ? '' : 'none';
    document.getElementById('viewTable').style.display = v === 'table' ? '' : 'none';
    document.getElementById('btnTile').className  = 'btn btn-sm ' + (v === 'tile'  ? 'btn-primary' : 'btn-outline-secondary');
    document.getElementById('btnTable').className = 'btn btn-sm ' + (v === 'table' ? 'btn-primary' : 'btn-outline-secondary');
}
setView(localStorage.getItem('barangView') || 'tile');
</script>
@endpush

@endsection
