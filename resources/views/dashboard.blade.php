@extends('layouts.app')
@section('title', 'Beranda')
@section('page-title', 'Beranda')

@section('content')

@if(!auth()->user()->isOperator() && $barangStokMinimum->count() > 0)
<div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0 mt-1"></i>
    <div>
        <strong>Stok Hampir Habis.</strong> Barang berikut perlu segera ditambah:
        <ul class="mb-0 mt-1">
            @foreach($barangStokMinimum as $b)
            <li>{{ $b->nama_barang }} — sisa <strong>{{ $b->stok_total ?? 0 }} {{ $b->satuan }}</strong></li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="page-header mb-4">
    <h4>Selamat datang, {{ auth()->user()->username }}</h4>
    <p>Ringkasan aktivitas gudang hari ini &amp; 7 hari terakhir.</p>
</div>

{{-- AKSI CEPAT --}}
<div class="mb-4">
    <div class="nav-sep-page">Aksi Cepat</div>
    <div class="row g-3">
        @if(auth()->user()->isOperator() || auth()->user()->isAdmin())
        <div class="col-6 col-md-3">
            <a href="{{ route('transaksi.create-masuk') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#ECFDF5;color:#059669;"><i class="bi bi-box-arrow-in-down"></i></div>
                <span>Catat Masuk</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('transaksi.create-keluar') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#FFF4E8;color:#E8751A;"><i class="bi bi-box-arrow-up"></i></div>
                <span>Catat Keluar</span>
            </a>
        </div>
        @endif

        @if(auth()->user()->isKepalaGudang())
        <div class="col-6 col-md-3">
            <a href="{{ route('laporan.stok') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#ECFDF5;color:#059669;"><i class="bi bi-clipboard-data"></i></div>
                <span>Monitor Stok</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('laporan.transaksi') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#EFF6FF;color:#2563EB;"><i class="bi bi-journal-text"></i></div>
                <span>Laporan Transaksi</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('transaksi.masuk') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#F3F4F6;color:#4B5563;"><i class="bi bi-list-check"></i></div>
                <span>Riwayat Masuk</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('transaksi.keluar') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#FFF4E8;color:#D97706;"><i class="bi bi-list-check"></i></div>
                <span>Riwayat Keluar</span>
            </a>
        </div>
        @endif

        <div class="col-6 col-md-3">
            <a href="{{ route('barang.index') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#F3F4F6;color:#4B5563;"><i class="bi bi-archive"></i></div>
                <span>Daftar Barang</span>
            </a>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="col-6 col-md-3">
            <a href="{{ route('laporan.transaksi') }}" class="btn-action w-100">
                <div class="ba-icon" style="background:#EFF6FF;color:#2563EB;"><i class="bi bi-journal-text"></i></div>
                <span>Laporan</span>
            </a>
        </div>
        @endif
    </div>
</div>

{{-- STATISTIK --}}
<div class="nav-sep-page mb-2">Statistik</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="s-icon" style="background:#F3F4F6;color:#4B5563;"><i class="bi bi-archive-fill"></i></div>
                <div>
                    <div class="s-label">Jenis Barang</div>
                    <div class="s-num" id="stat-total-barang">{{ $stats['total_barang'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="s-icon" style="background:#ECFDF5;color:#059669;"><i class="bi bi-box-arrow-in-down"></i></div>
                <div>
                    <div class="s-label">Masuk Hari Ini</div>
                    <div class="s-num" id="stat-masuk-hari" style="color:#059669;">{{ $stats['masuk_hari_ini'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="s-icon" style="background:#FFF4E8;color:#E8751A;"><i class="bi bi-box-arrow-up"></i></div>
                <div>
                    <div class="s-label">Keluar Hari Ini</div>
                    <div class="s-num" id="stat-keluar-hari" style="color:#E8751A;">{{ $stats['keluar_hari_ini'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @if(!auth()->user()->isOperator())
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="s-icon" style="background:#FEF2F2;color:#DC2626;"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div>
                    <div class="s-label">Stok Hampir Habis</div>
                    <div class="s-num" id="stat-stok-min" style="color:{{ $stats['stok_minimum'] > 0 ? '#DC2626' : 'var(--text)' }};">{{ $stats['stok_minimum'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- CHART --}}
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-semibold">Transaksi 7 Hari Terakhir</span>
    </div>
    <div class="card-body">
        <canvas id="transaksiChart" height="100"></canvas>
    </div>
</div>

{{-- TRANSAKSI TERBARU --}}
<div class="nav-sep-page mb-2">Transaksi Terbaru</div>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-box-arrow-in-down me-2" style="color:#059669;"></i>Masuk Terbaru</span>
                <a href="{{ route('transaksi.masuk') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>No. Transaksi</th><th>Barang</th><th class="text-end">Qty</th><th>Tanggal</th></tr></thead>
                    <tbody id="tbody-masuk">
                        @forelse($recentMasuk as $t)
                        <tr>
                            <td><span class="badge badge-masuk">{{ $t->no_transaksi }}</span></td>
                            <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                            <td class="text-end fw-semibold" style="color:#059669;">{{ $t->quantity }}</td>
                            <td style="color:var(--text-muted);">{{ $t->tanggal->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada transaksi masuk</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-box-arrow-up me-2" style="color:#E8751A;"></i>Keluar Terbaru</span>
                <a href="{{ route('transaksi.keluar') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>No. Transaksi</th><th>Barang</th><th class="text-end">Qty</th><th>Tanggal</th></tr></thead>
                    <tbody id="tbody-keluar">
                        @forelse($recentKeluar as $t)
                        <tr>
                            <td><span class="badge badge-keluar">{{ $t->no_transaksi }}</span></td>
                            <td class="fw-semibold">{{ $t->barang->nama_barang }}</td>
                            <td class="text-end fw-semibold" style="color:#DC2626;">{{ $t->quantity }}</td>
                            <td style="color:var(--text-muted);">{{ $t->tanggal->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada transaksi keluar</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.nav-sep-page {
    font-size: .72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-subtle);
    margin-bottom: 10px;
}

</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('transaksiChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: @json($chartMasuk),
                    backgroundColor: 'rgba(5, 150, 105, 0.7)',
                    borderRadius: 4,
                },
                {
                    label: 'Barang Keluar',
                    data: @json($chartKeluar),
                    backgroundColor: 'rgba(220, 38, 38, 0.7)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }
        }
    });
}
</script>
@endpush

@endsection
