@extends('layouts.error')

@section('title', '500 - Kesalahan Server')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 80vh">
    <div style="font-size: 4rem; font-weight: 800; color: #DC2626; line-height: 1;">500</div>
    <h4 class="mt-2 mb-1">Kesalahan Server</h4>
    <p class="text-muted mb-4">Terjadi kesalahan pada server. Tim teknis sudah diberitahu.</p>
    <a href="{{ url('/') }}" class="btn btn-primary" style="background:#F5821F;border-color:#F5821F">
        <i class="bi bi-house me-1"></i> Kembali ke Beranda
    </a>
</div>
@endsection
