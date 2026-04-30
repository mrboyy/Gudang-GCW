@extends('layouts.app')
@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div class="page-header">
        <h4>Audit Log</h4>
        <p>Riwayat seluruh perubahan data sistem.</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Cari deskripsi..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="action" class="form-select form-select-sm">
                        <option value="">Semua Aksi</option>
                        @foreach(['create'=>'Tambah','update'=>'Ubah','delete'=>'Hapus/Nonaktif','void'=>'Void','login'=>'Login'] as $v=>$l)
                        <option value="{{ $v }}" @selected(request('action')===$v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="model_type" class="form-select form-select-sm">
                        <option value="">Semua Modul</option>
                        @foreach(['Barang','Transaksi','User'] as $m)
                        <option value="{{ $m }}" @selected(request('model_type')===$m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua User</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id')==$u->id)>{{ $u->username }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <input type="date" name="tanggal_dari" class="form-control form-control-sm" value="{{ request('tanggal_dari') }}">
                </div>
                <div class="col-md-1">
                    <input type="date" name="tanggal_sampai" class="form-control form-control-sm" value="{{ request('tanggal_sampai') }}">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    @if(request()->hasAny(['search','action','model_type','user_id','tanggal_dari','tanggal_sampai']))
                    <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-secondary">✕</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Modul</th>
                        <th>Deskripsi</th>
                        <th>IP</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;color:var(--text-muted);font-size:.8rem;">
                            {{ $log->created_at->format('d/m/Y') }}<br>
                            <span style="font-size:.75rem;">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                        <td>
                            @if($log->user)
                            <span class="fw-semibold" style="font-size:.85rem;">{{ $log->user->username }}</span><br>
                            <span style="font-size:.72rem;color:var(--text-muted);">{{ $log->user->role }}</span>
                            @else
                            <span style="color:var(--text-subtle);font-size:.82rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                            $actionMeta = [
                                'create' => ['bg'=>'#ECFDF5','clr'=>'#065F46','bdr'=>'#6EE7B7','lbl'=>'Tambah'],
                                'update' => ['bg'=>'#EFF6FF','clr'=>'#1E40AF','bdr'=>'#BFDBFE','lbl'=>'Ubah'],
                                'delete' => ['bg'=>'#FEF2F2','clr'=>'#991B1B','bdr'=>'#FECACA','lbl'=>'Hapus'],
                                'void'   => ['bg'=>'#FFF7ED','clr'=>'#9A3412','bdr'=>'#FDBA74','lbl'=>'Void'],
                                'login'  => ['bg'=>'#F3F4F6','clr'=>'#374151','bdr'=>'#D1D5DB','lbl'=>'Login'],
                            ];
                            $m = $actionMeta[$log->action] ?? ['bg'=>'#F3F4F6','clr'=>'#374151','bdr'=>'#D1D5DB','lbl'=>ucfirst($log->action)];
                            @endphp
                            <span class="badge" style="background:{{ $m['bg'] }};color:{{ $m['clr'] }};border:1px solid {{ $m['bdr'] }};">{{ $m['lbl'] }}</span>
                        </td>
                        <td style="font-size:.82rem;color:var(--text-muted);">{{ $log->model_type ?? '—' }}@if($log->model_id)<span class="text-subtle"> #{{ $log->model_id }}</span>@endif</td>
                        <td style="font-size:.85rem;max-width:320px;">{{ $log->description }}</td>
                        <td style="font-size:.75rem;color:var(--text-subtle);">{{ $log->ip_address ?? '—' }}</td>
                        <td>
                            @if($log->old_values || $log->new_values)
                            <button class="btn btn-sm btn-outline-secondary" style="padding:3px 8px;font-size:.75rem;"
                                data-bs-toggle="modal" data-bs-target="#detailModal{{ $log->id }}">
                                Detail
                            </button>
                            {{-- Modal --}}
                            <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header py-2 px-3">
                                            <h6 class="modal-title fw-bold" style="font-size:.88rem;">Perubahan Data — {{ $log->description }}</h6>
                                            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            <div class="row g-0">
                                                @if($log->old_values)
                                                <div class="col-md-6" style="border-right:1px solid var(--border);">
                                                    <div style="padding:10px 14px;background:#FEF2F2;border-bottom:1px solid #FECACA;">
                                                        <span style="font-size:.72rem;font-weight:700;color:#991B1B;text-transform:uppercase;letter-spacing:.05em;">Sebelum</span>
                                                    </div>
                                                    <div style="padding:12px 14px;">
                                                        @foreach($log->old_values as $k => $v)
                                                        <div style="margin-bottom:6px;">
                                                            <div style="font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">{{ $k }}</div>
                                                            <div style="font-size:.85rem;color:var(--text);">{{ is_array($v) ? json_encode($v) : ($v ?? '—') }}</div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                                @if($log->new_values)
                                                <div class="col-md-{{ $log->old_values ? '6' : '12' }}">
                                                    <div style="padding:10px 14px;background:#ECFDF5;border-bottom:1px solid #A7F3D0;">
                                                        <span style="font-size:.72rem;font-weight:700;color:#065F46;text-transform:uppercase;letter-spacing:.05em;">Sesudah</span>
                                                    </div>
                                                    <div style="padding:12px 14px;">
                                                        @foreach($log->new_values as $k => $v)
                                                        <div style="margin-bottom:6px;">
                                                            <div style="font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;">{{ $k }}</div>
                                                            <div style="font-size:.85rem;color:var(--text);">{{ is_array($v) ? json_encode($v) : ($v ?? '—') }}</div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-clock-history"></i><p>Belum ada log aktivitas</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
