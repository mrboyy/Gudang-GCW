@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh">
    <div style="font-size: 4rem; font-weight: 800; color: #F5821F; line-height: 1;">404</div>
    <h4 class="mt-2 mb-1">Halaman Tidak Ditemukan</h4>
    <p class="text-muted mb-4">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary">
        <i class="bi bi-house me-1"></i> Kembali ke Dashboard
    </a>
</div>
@endsection
