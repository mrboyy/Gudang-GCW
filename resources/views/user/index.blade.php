@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Kelola Pengguna</h4>
        <p>Akun yang dapat mengakses sistem gudang.</p>
    </div>
    <a href="{{ route('user.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Tambah Pengguna
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th class="text-center">Status</th>
                        <th>Terdaftar</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    @php
                        $roleMap = [
                            'admin'         => ['label'=>'Administrator', 'bg'=>'#FEF2F2',  'color'=>'#991B1B'],
                            'kepala_gudang' => ['label'=>'Kepala Gudang', 'bg'=>'#EFF6FF',  'color'=>'#1D4ED8'],
                            'operator'      => ['label'=>'Operator',      'bg'=>'#F3F4F6',  'color'=>'#374151'],
                        ];
                        $rc = $roleMap[$u->role] ?? ['label'=>ucfirst($u->role),'bg'=>'#F3F4F6','color'=>'#374151'];
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:34px;height:34px;border-radius:8px;background:var(--sidebar-active-bg,#242A3A);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:.85rem;color:#E5E7EB;">
                                    {{ strtoupper(substr($u->username,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $u->username }}</div>
                                    @if($u->id === auth()->id())
                                    <span style="font-size:.72rem;color:var(--text-muted);font-weight:500;">Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $rc['bg'] }};color:{{ $rc['color'] }};border:1px solid {{ $rc['color'] }}22;">
                                {{ $rc['label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($u->is_active)
                            <span class="badge" style="background:var(--success-soft);color:var(--success);border:1px solid #A7F3D0;">
                                <i class="bi bi-check-circle-fill me-1"></i>Aktif
                            </span>
                            @else
                            <span class="badge" style="background:#F3F4F6;color:var(--text-muted);border:1px solid var(--border);">
                                <i class="bi bi-dash-circle me-1"></i>Nonaktif
                            </span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('user.edit', $u) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                <form action="{{ route('user.destroy', $u) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Nonaktifkan pengguna {{ $u->username }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-person-dash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:var(--text-subtle);">
                            <i class="bi bi-people" style="font-size:2.5rem;display:block;margin-bottom:8px;"></i>
                            Tidak ada pengguna
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection
