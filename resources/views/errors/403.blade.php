@extends('layouts.app')
@section('title', 'Akses Ditolak')
@section('page-title', 'Akses Ditolak')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-12 col-md-6 text-center">
        <div style="font-size:5rem;margin-bottom:16px;">🚫</div>
        <h3 class="fw-bold mb-2">Akses Ditolak</h3>
        <p class="text-muted mb-4">Halaman ini tidak dapat diakses dengan akun Anda.<br>
        Hubungi administrator jika Anda merasa ini keliru.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
</div>
@endsection
