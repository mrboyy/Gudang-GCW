@extends('layouts.app')
@section('title', 'Kelola Supplier')
@section('page-title', 'Kelola Supplier')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Supplier</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-9 col-xl-8">

@if(session('success'))
<div class="alert alert-success d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h5 class="fw-bold mb-0" style="color:var(--text);">Kelola Supplier</h5>
        <div class="text-muted" style="font-size:.78rem;">Data supplier tersimpan otomatis saat input barang masuk</div>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSupplier">
        <i class="bi bi-plus-lg me-1"></i>Tambah
    </button>
</div>

{{-- Modal Tambah Supplier --}}
<div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title fw-bold" id="modalTambahLabel">Tambah Supplier</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('supplier.store') }}">
                @csrf
                <div class="modal-body">
                    @if($errors->has('nama'))
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.82rem;">{{ $errors->first('nama') }}</div>
                    @endif
                    <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama') }}" maxlength="200" placeholder="Contoh: PT Maju Jaya" required autofocus>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-3">
        <form method="GET" class="d-flex gap-2 mb-3">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Cari nama supplier..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-outline-secondary">Cari</button>
            @if(request('search'))
            <a href="{{ route('supplier.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Supplier</th>
                        <th>Ditambahkan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $s)
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $suppliers->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ $s->nama }}</td>
                        <td class="text-muted" style="font-size:.82rem;">{{ $s->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('supplier.destroy', $s) }}"
                                  onsubmit="return confirm('Hapus supplier {{ addslashes($s->nama) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4" style="font-size:.88rem;">
                            Belum ada data supplier
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="mt-3 d-flex justify-content-end">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>

</div>
</div>
@endsection
