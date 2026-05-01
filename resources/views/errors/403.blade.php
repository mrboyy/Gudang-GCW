@extends('layouts.error')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh">
    <div style="font-size: 4rem; font-weight: 800; color: #DC2626; line-height: 1;"><i class="bi bi-slash-circle"></i></div>
    <h4 class="mt-3 mb-1">Akses Ditolak</h4>
    <p class="text-muted mb-4">Halaman ini tidak dapat diakses dengan akun Anda.<br>
    Hubungi administrator jika Anda merasa ini keliru.</p>
    <a href="{{ url('/') }}" class="btn btn-primary" style="background:#F5821F;border-color:#F5821F">
        <i class="bi bi-house me-1"></i> Kembali ke Beranda
    </a>
</div>
@endsection
