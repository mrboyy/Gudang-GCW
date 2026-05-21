@extends('layouts.app')
@section('title', 'Master Barang')
@section('page-title', 'Master Barang')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Data Barang</li>
@endsection

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Master Barang</h4>
        <p>Daftar semua barang yang terdaftar di sistem gudang.</p>
    </div>
    <a href="{{ route('barang.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Barang
    </a>
</div>


<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:6px 0 0 6px;background:var(--bg);border-right:none;">
                            <i class="bi bi-search text-muted" style="font-size:.85rem;"></i>
                        </span>
                        <input type="text" name="search" class="form-control" style="border-left:none;border-radius:0 6px 6px 0;"
                               placeholder="Cari nama barang atau kode..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                    @if(request('search'))
                    <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th class="d-none d-md-table-cell" style="width:60px">Foto</th>
                        <th>Nama Barang</th>
                        <th class="d-none d-md-table-cell">Kode</th>
                        <th class="text-end">Stok</th>
                        <th class="text-center d-none d-md-table-cell">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $barang)
                    <tr>
                        <td class="d-none d-md-table-cell">
                            @if($barang->foto)
                                <img src="{{ asset('storage/' . $barang->foto) }}"
                                     alt="{{ $barang->nama_barang }}"
                                     style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb">
                            @else
                                <div style="width:48px;height:48px;border-radius:6px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-box text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $barang->nama_barang }}</div>
                            <div style="font-size:.75rem;color:var(--text-muted);" class="d-md-none">{{ $barang->kode_barang }}</div>
                            @if($barang->deskripsi)
                            <div style="font-size:.78rem;color:var(--text-muted);" class="d-none d-md-block">{{ Str::limit($barang->deskripsi, 60) }}</div>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell" style="color:var(--text-muted);font-size:.88rem;">{{ $barang->kode_barang }}</td>
                        <td class="text-end">
                            @php $stok = $barang->getStok(); @endphp
                            <span class="fw-semibold" style="color:{{ $barang->isStokMinimum() ? 'var(--danger)' : 'inherit' }};">
                                {{ $stok }}
                            </span>
                            <div class="d-md-none" style="margin-top:2px;">
                                @if(!$barang->is_active)
                                    <span class="badge" style="background:#F3F4F6;color:#6B7280;border:1px solid #D1D5DB;font-size:.62rem;">Nonaktif</span>
                                @elseif($barang->isStokMinimum())
                                    <span class="badge" style="background:var(--danger-soft);color:var(--danger);border:1px solid #FCA5A5;font-size:.62rem;">Stok Rendah</span>
                                @else
                                    <span class="badge" style="background:#ECFDF5;color:#059669;border:1px solid #A7F3D0;font-size:.62rem;">Normal</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center d-none d-md-table-cell">
                            @if(!$barang->is_active)
                                <span class="badge" style="background:#F3F4F6;color:#6B7280;border:1px solid #D1D5DB;padding:4px 10px;font-size:.72rem;">
                                    <i class="bi bi-slash-circle me-1"></i>Nonaktif
                                </span>
                            @elseif($barang->isStokMinimum())
                                <span class="badge" style="background:#FEF2F2;color:#DC2626;border:1px solid #FCA5A5;padding:4px 10px;font-size:.72rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i>Stok Rendah
                                </span>
                            @else
                                <span class="badge" style="background:#ECFDF5;color:#059669;border:1px solid #A7F3D0;padding:4px 10px;font-size:.72rem;">
                                    <i class="bi bi-check-circle me-1"></i>Normal
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('barang.edit', $barang) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($barang->is_active)
                                <form action="{{ route('barang.toggle-active', $barang) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Nonaktifkan barang {{ addslashes(e($barang->nama_barang)) }}?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-warning" title="Nonaktifkan">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('barang.toggle-active', $barang) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Aktifkan kembali barang {{ addslashes(e($barang->nama_barang)) }}?')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success" title="Aktifkan">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('barang.destroy', $barang) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('HAPUS PERMANEN barang {{ addslashes(e($barang->nama_barang)) }}?\n\nTidak bisa dibatalkan!')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus Permanen">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="bi bi-box-seam"></i>
                            <p>Belum ada barang terdaftar</p>
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
@endsection
