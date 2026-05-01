@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('page-title', 'Tambah Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Kelola User</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah User</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-6 col-xl-5">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('user.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h5 class="fw-bold mb-0" style="letter-spacing:-.02em;">Tambah Pengguna</h5>
        <div style="font-size:.78rem;color:var(--text-muted);">Buat akun baru untuk mengakses sistem</div>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="card">
    <div class="card-header">
        <i class="bi bi-person-plus me-2" style="color:var(--text-muted);"></i>
        <span>Informasi Akun</span>
    </div>
    <form method="POST" action="{{ route('user.store') }}">
    @csrf
    <div class="card-body p-4">

        <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}" required autocomplete="off">
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email <span class="text-muted small">(opsional, untuk notifikasi)</span></label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="admin@perusahaan.com">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">— Pilih role —</option>
                <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                <option value="kepala_gudang" {{ old('role') === 'kepala_gudang' ? 'selected' : '' }}>Kepala Gudang</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text text-muted">Minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <div style="border-top:1px solid var(--border-soft);padding-top:14px;">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                <label class="form-check-label fw-semibold" for="isActive" style="font-size:.88rem;">Akun Aktif</label>
            </div>
        </div>

    </div>
    <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-check2 me-1"></i>Simpan
        </button>
    </div>
    </form>
</div>

</div>
</div>
@endsection
