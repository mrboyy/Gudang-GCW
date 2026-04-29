@extends('layouts.app')
@section('title', 'Daftar Barang')
@section('page-title', 'Daftar Barang')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Daftar Barang</h4>
        <p>Seluruh barang yang tersimpan di gudang.</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
    <a href="{{ route('barang.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Barang
    </a>
    @endif
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

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
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
                        <td><code class="code-tag">{{ $b->kode_barang }}</code></td>
                        <td class="fw-semibold">{{ $b->nama_barang }}</td>
                        <td class="text-muted">{{ $b->merk ?: '—' }}</td>
                        <td>{{ $b->satuan }}</td>
                        <td class="text-end">
                            <span class="fw-bold" style="color:{{ $habis ? 'var(--danger)' : 'var(--success)' }};">
                                {{ $stokSekarang }}
                            </span>
                            @if($habis)
                            <i class="bi bi-exclamation-triangle-fill ms-1" style="color:var(--danger);font-size:.8rem;" title="Stok hampir habis"></i>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('barang.edit', $b) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('barang.destroy', $b) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Nonaktifkan barang ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-state"><i class="bi bi-inbox"></i><p>Tidak ada data barang</p></td></tr>
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
