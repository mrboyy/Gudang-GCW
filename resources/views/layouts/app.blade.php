<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Manajemen Gudang PT Galih Cipta Wisesa">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">
    <title>@yield('title', 'Gudang') — PT Galih Cipta Wisesa</title>
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        window.APP_URL = "{{ url('/') }}";
    </script>
    <style>
        :root {
            --brand: #F5821F;
            --brand-hover: #D96E10;
            --brand-soft: #FFF4E8;

            --bg: #F3F4F6;
            --surface: #FFFFFF;
            --border: #E5E7EB;
            --border-soft: #F0F1F3;

            --text: #111827;
            --text-muted: #6B7280;
            --text-subtle: #9CA3AF;

            --sidebar-bg: #111827;
            --sidebar-border: #1F2937;
            --sidebar-text: #9CA3AF;
            --sidebar-text-hover: #F3F4F6;
            --sidebar-active-bg: rgba(245,130,31,.09);

            --success: #0a7c5a;
            --success-soft: #f0fdf8;
            --danger: #b91c1c;
            --danger-soft: #fff5f5;
            --warning: #b45309;
            --warning-soft: #fefce8;
            --info: #1d4ed8;
            --info-soft: #f0f7ff;

            --shadow-xs: 0 1px 3px rgba(16,24,40,.06);
            --shadow-sm: 0 2px 8px rgba(16,24,40,.08), 0 1px 2px rgba(16,24,40,.04);
            --shadow-md: 0 4px 16px rgba(16,24,40,.08), 0 2px 4px rgba(16,24,40,.04);
        }

        [data-bs-theme="dark"] {
            --bg: #0f172a;
            --surface: #1e293b;
            --border: #334155;
            --border-soft: #1e293b;

            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --text-subtle: #475569;

            --sidebar-active-bg: rgba(245,130,31,.12);
            --brand-soft: rgba(245,130,31,.08);

            --shadow-xs: 0 1px 3px rgba(0,0,0,.3);
            --shadow-sm: 0 4px 12px rgba(0,0,0,.4);
            
            --table-header-bg: #1e293b;
        }

        [data-bs-theme="dark"] .bg-light { background-color: var(--bg) !important; }
        [data-bs-theme="dark"] .bg-white { background-color: var(--surface) !important; }
        [data-bs-theme="dark"] .text-dark { color: var(--text) !important; }

        [data-bs-theme="dark"] .table thead th {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
            border-bottom-color: #334155 !important;
        }
        [data-bs-theme="dark"] .table > :not(caption) > * > * {
            background-color: var(--surface) !important;
            color: var(--text) !important;
            border-bottom-color: #1e293b !important;
        }
        [data-bs-theme="dark"] .card-footer {
            background-color: #162032 !important;
            border-top-color: #334155 !important;
        }
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e293b;
            border-color: #334155;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #f1f5f9;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #334155;
        }
        [data-bs-theme="dark"] .input-group-text {
            background-color: #162032;
            border-color: #334155;
            color: #94a3b8;
        }

        :root {
            --table-header-bg: #f8fafc;
        }

        .table > :not(caption) > * > * {
            background-color: var(--surface);
            color: var(--text);
        }
        .table thead th {
            background-color: var(--table-header-bg) !important;
            color: var(--text-muted) !important;
            border-bottom: 1px solid var(--border) !important;
        }

        body.theme-transitioning, body.theme-transitioning * {
            transition: background-color .35s ease, color .35s ease, border-color .35s ease,
                        fill .35s ease, stroke .35s ease, box-shadow .35s ease !important;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        /* ═══════ SIDEBAR ═══════ */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 244px;
            height: 100vh;
            background: #1e1e1e;
            display: flex;
            flex-direction: column;
            z-index: 1100;
            transition: transform .25s cubic-bezier(.4,0,.2,1);
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,.04);
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: #2a3142; border-radius: 2px; }

        .sidebar-brand {
            padding: 18px 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }
        .sidebar-brand img {
            width: 38px; height: 38px;
            object-fit: contain;
            background: #fff;
            border-radius: 8px;
            padding: 4px;
        }
        .sidebar-brand .b-name {
            font-size: .88rem;
            font-weight: 700;
            color: #F3F4F6;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }
        .sidebar-brand .b-sub {
            font-size: .68rem;
            color: #5A6478;
            font-weight: 500;
            margin-top: 2px;
        }

        .nav-sep {
            font-size: .72rem;
            font-weight: 600;
            color: #4a5568;
            padding: 16px 18px 5px;
        }

        .sidebar .slink {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            margin: 1px 8px;
            border-radius: 7px;
            color: #94a3b8; /* Forced light color for sidebar links */
            font-size: .875rem;
            font-weight: 500;
            text-decoration: none;
            transition: background .15s, color .15s;
            overflow: hidden;
        }
        .sidebar .slink i {
            font-size: 1rem;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar .slink:hover {
            background: rgba(255,255,255,.05);
            color: #f1f5f9;
        }
        .sidebar .slink.active {
            background: rgba(255,255,255,.08);
            color: #fff;
            font-weight: 600;
        }
        .sidebar .slink.active::before {
            content: '';
            position: absolute;
            left: 0; top: 6px; bottom: 6px;
            width: 3px;
            background: var(--brand);
            border-radius: 0 3px 3px 0;
        }
        .sidebar .slink.active i { color: var(--brand); }

        /* Collapsible Menu */
        .sidebar .nav-dropdown {
            display: none;
            background: rgba(0,0,0,.2);
            margin: 0 8px 8px;
            border-radius: 0 0 8px 8px;
            padding: 4px 0;
            border-left: 1px solid rgba(255,255,255,.05);
            border-right: 1px solid rgba(255,255,255,.05);
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .sidebar .nav-dropdown.show { display: block; }
        .sidebar .slink-drop { cursor: pointer; justify-content: space-between !important; }
        .sidebar .slink-drop .drop-chevron { 
            font-size: .75rem; 
            transition: transform .2s; 
            color: #64748b;
        }
        .sidebar .slink-drop.active .drop-chevron, 
        .sidebar .slink-drop[aria-expanded="true"] .drop-chevron { 
            transform: rotate(180deg); 
            color: var(--brand);
        }
        .sidebar .nav-dropdown .slink {
            margin: 0 4px;
            padding: 7px 14px 7px 38px;
            font-size: .82rem;
            color: #94a3b8;
        }
        .sidebar .nav-dropdown .slink:hover {
            color: #f1f5f9;
        }
        .sidebar .nav-dropdown .slink.active {
            color: #fff;
        }
        .sidebar .nav-dropdown .slink::before { left: 12px; }

        .sidebar-foot {
            margin-top: auto;
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,.06);
            flex-shrink: 0;
        }
        .sidebar-foot .u-avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #2D3748, #1A202C);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #CBD5E0;
            font-weight: 700;
            font-size: .78rem;
            border: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-foot .u-name {
            font-size: .82rem;
            font-weight: 600;
            color: #E2E8F0;
            letter-spacing: -0.01em;
        }
        .sidebar-foot .u-role {
            font-size: .68rem;
            color: #5A6478;
            font-weight: 500;
        }
        .btn-logout {
            background: transparent;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 6px;
            padding: 6px 9px;
            color: #5A6478;
            cursor: pointer;
            transition: all .15s;
            flex-shrink: 0;
        }
        .btn-logout:hover {
            border-color: rgba(239,68,68,.4);
            color: #F87171;
            background: rgba(239,68,68,.06);
        }

        /* ═══════ TOPBAR ═══════ */
        .topbar {
            position: fixed;
            top: 0;
            left: 244px; right: 0;
            height: 54px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 6px rgba(16,24,40,.04);
            display: flex;
            align-items: center;
            padding: 0 22px;
            gap: 12px;
            z-index: 900;
        }
        .topbar .ptitle {
            font-size: .95rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
            flex: 1;
        }
        .topbar .tdate {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            color: var(--text-muted);
            font-weight: 500;
        }
        .btn-ham {
            display: none;
            border: 1px solid var(--border);
            background: var(--surface);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 1.1rem;
            cursor: pointer;
            color: var(--text-muted);
        }

        /* ═══════ TOPBAR USER (mobile logout) ═══════ */
        .topbar-user-mobile { display: none; align-items: center; gap: 8px; flex-shrink: 0; }
        .tum-avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, #374151, #1F2937);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            color: #D1D5DB; font-weight: 700; font-size: .78rem;
            flex-shrink: 0;
        }
        .tum-logout {
            display: flex; align-items: center; gap: 5px;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 6px;
            padding: 6px 10px;
            color: #DC2626;
            font-size: .8rem; font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }
        .tum-logout:hover { background: #DC2626; color: #fff; border-color: #DC2626; }
        .tum-logout i { font-size: .9rem; }

        /* ═══════ MAIN ═══════ */
        .main-wrap {
            margin-left: 244px;
            padding-top: 54px;
            min-height: 100vh;
        }
        .page-body { padding: 24px 22px; max-width: 1440px; }

        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(17,24,39,.5);
            z-index: 1050;
        }

        /* ═══════ CARDS ═══════ */
        .card {
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            box-shadow: var(--shadow-xs);
        }
        .card-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border-soft);
            border-radius: 8px 8px 0 0 !important;
            padding: 14px 20px;
            font-size: .875rem;
            font-weight: 600;
        }
        .card-footer {
            background: #FAFBFC;
            border-top: 1px solid var(--border-soft);
            padding: 12px 20px;
            border-radius: 0 0 8px 8px;
        }

        /* ═══════ TABLES ═══════ */
        .table {
            margin-bottom: 0;
            color: var(--text);
        }
        .table th {
            font-size: .82rem;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--table-header-bg) !important;
            padding: 10px 18px;
            border-bottom: 1px solid var(--border);
            border-top: none;
        }
        .table td {
            font-size: .875rem;
            padding: 12px 18px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-soft);
            color: var(--text);
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background: rgba(0,0,0,.035); }
        [data-bs-theme="dark"] .table tbody tr:hover { background: rgba(255,255,255,.02); }

        /* ═══════ FORMS ═══════ */
        .form-label {
            font-size: .84rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
            letter-spacing: -0.005em;
        }
        .form-control, .form-select {
            font-size: .9rem;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            height: auto;
            color: var(--text);
            background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
            font-family: inherit;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(245,130,31,.12);
            outline: none;
            background: var(--surface);
        }
        .form-control::placeholder { color: var(--text-subtle); }
        
        .input-group-text {
            font-size: .88rem;
            padding: 9px 13px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--text-muted);
            border-radius: 0 6px 6px 0;
            font-weight: 600;
        }
        .input-group .form-control { border-radius: 6px 0 0 6px; }
        .input-group .form-control:focus { z-index: 3; }

        /* ═══════ BUTTONS ═══════ */
        .btn {
            font-size: .875rem;
            font-weight: 500;
            border-radius: 5px;
            padding: 7px 16px;
            transition: background .12s, border-color .12s, color .12s;
            font-family: inherit;
        }
        .btn:focus { outline: none; box-shadow: none; }
        .btn-lg { font-size: .9rem; padding: 9px 20px; }
        .btn-sm { font-size: .8rem; padding: 4px 11px; }

        .btn-primary {
            background: var(--brand);
            border: 1px solid var(--brand);
            color: #fff;
        }
        .btn-primary:hover { background: var(--brand-hover); border-color: var(--brand-hover); color: #fff; }

        .btn-outline-primary {
            border: 1px solid #D1D5DB;
            color: var(--brand);
            background: var(--surface);
        }
        .btn-outline-primary:hover { background: var(--brand-soft); border-color: var(--brand); color: var(--brand-hover); }

        .btn-success {
            background: var(--success);
            border: 1px solid var(--success);
            color: #fff;
            box-shadow: 0 1px 3px rgba(5,150,105,.2);
        }
        .btn-success:hover { background: #047857; border-color: #047857; color: #fff; }

        .btn-outline-secondary {
            border: 1px solid #D1D5DB;
            color: var(--text-muted);
            background: var(--surface);
            font-weight: 500;
        }
        .btn-outline-secondary:hover { background: #F3F4F6; color: var(--text); border-color: #C4C9D4; }

        .btn-outline-danger { border: 1px solid #D1D5DB; color: var(--danger); background: var(--surface); }
        .btn-outline-danger:hover { background: var(--danger-soft); border-color: #FCA5A5; color: var(--danger); }

        .btn-warning { background: var(--warning); border-color: var(--warning); color: #fff; }
        .btn-warning:hover { background: #B45309; border-color: #B45309; color: #fff; }

        .btn-info { background: var(--info); border-color: var(--info); color: #fff; }
        .btn-info:hover { background: #1D4ED8; border-color: #1D4ED8; color: #fff; }

        /* ═══════ ALERTS ═══════ */
        .alert {
            font-size: .875rem;
            border-radius: 8px;
            padding: 12px 16px;
            border-width: 1px;
            font-weight: 500;
        }
        .alert-success { background: #f0fdf8; color: #0a6147; border-color: #d1f5e8; }
        .alert-danger  { background: #fff5f5; color: #991b1b; border-color: #fecaca; }
        .alert-warning { background: #fefce8; color: #7c3e0a; border-color: #fde89a; }
        .alert-info    { background: #f0f7ff; color: #1e40af; border-color: #bfdbfe; }

        /* ═══════ STAT CARDS (dashboard) ═══════ */
        .stat-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 18px 20px;
            background: var(--surface);
            box-shadow: var(--shadow-xs);
            height: 100%;
            display: flex;
            align-items: center;
        }
        .stat-card > .d-flex { flex: 1; }
        .stat-card .s-icon {
            width: 44px; height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .stat-card .s-label {
            font-size: .78rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 4px;
        }
        .stat-card .s-num {
            font-size: 1.7rem;
            font-weight: 700;
            line-height: 1;
            color: var(--text);
        }

        /* ═══════ QUICK ACTIONS ═══════ */
        .btn-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 18px 10px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            font-size: .85rem;
            font-weight: 500;
            color: var(--text);
            text-decoration: none;
            transition: border-color .12s, background .12s;
            min-height: 88px;
        }
        .btn-action .ba-icon {
            width: 38px; height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .btn-action:hover {
            border-color: #b0b5be;
            background: #fafafa;
            color: var(--text);
        }

        /* ═══════ BADGES ═══════ */
        .badge {
            font-size: .7rem;
            padding: 3px 9px;
            border-radius: 5px;
            font-weight: 600;
            letter-spacing: .02em;
        }

        /* ═══════ PAGINATION ═══════ */
        .pagination { margin: 0; gap: 2px; }
        .page-link {
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: .84rem;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px !important;
        }
        .page-link:hover { background: #F3F4F6; color: var(--text); border-color: #D1D5DB; }
        .page-item.active .page-link { background: var(--brand); border-color: var(--brand); color: #fff; }

        /* ═══════ PAGE HEADER ═══════ */
        .page-header {
            padding-bottom: 16px;
            margin-bottom: 4px;
            border-bottom: 1px solid var(--border-soft);
        }
        .page-header h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .page-header p {
            font-size: .875rem;
            color: var(--text-muted);
            margin: 0;
        }

        /* ═══════ CODE TAG ═══════ */
        .code-tag {
            background: #F3F4F6;
            color: #374151;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: .82rem;
            font-family: 'SFMono-Regular', Consolas, monospace;
        }

        /* ═══════ BADGE VARIANTS ═══════ */
        .badge-masuk {
            background: #f0fdf8;
            color: #0a6147;
            border: 1px solid #d1f5e8;
        }
        .badge-keluar {
            background: #fff7f0;
            color: #8a3010;
            border: 1px solid #fddcc4;
        }
        .badge-retur {
            background: #f0f7ff;
            color: #1e40af;
            border: 1px solid #dbeafe;
        }
        .badge-retur-p {
            background: #faf5ff;
            color: #6b21a8;
            border: 1px solid #e9d5ff;
        }
        .badge-status-ok {
            background: #f0fdf8;
            color: var(--success);
            border: 1px solid #d1f5e8;
        }
        .badge-status-warn {
            background: #fff5f5;
            color: var(--danger);
            border: 1px solid #fecaca;
        }

        /* ═══════ EMPTY STATE ═══════ */
        .empty-state {
            text-align: center;
            padding: 52px 24px;
            color: var(--text-subtle);
        }
        .empty-state i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 10px;
            opacity: .4;
        }
        .empty-state p { font-size: .875rem; margin: 0; }

        /* ═══════ NOTIFICATION TOAST ═══════ */
        .notif-sukses {
            position: fixed;
            top: 68px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            color: #1a1a1a;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: .875rem;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,.12);
            z-index: 9999;
            white-space: nowrap;
            border: 1px solid #e0e0e0;
        }
        .notif-sukses i { color: #16a34a; font-size: 1rem; }

        /* ═══════ BOTTOM NAV (mobile) ═══════ */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0; left: 0; right: 0;
            height: 62px;
            background: var(--surface);
            border-top: 1px solid var(--border);
            z-index: 1200;
            box-shadow: 0 -2px 16px rgba(16,24,40,.07);
        }
        .mbn-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: var(--text-subtle);
            font-size: .62rem;
            font-weight: 600;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 8px 4px;
            -webkit-tap-highlight-color: transparent;
            transition: color .15s;
            min-height: 62px;
        }
        .mbn-item i { font-size: 1.35rem; line-height: 1; }
        .mbn-item.active { color: var(--brand); }
        .mbn-item.mbn-primary {
            color: #fff;
            background: var(--brand);
            border-radius: 0;
        }
        .mbn-item.mbn-primary.active { background: var(--brand-hover); }

        /* ═══════ MOBILE ═══════ */
        @media (max-width: 768px) {
            .sidebar { display: none !important; }
            .sidebar-overlay { display: none !important; }
            .btn-ham { display: none !important; }
            .main-wrap { margin-left: 0; padding-top: 54px; }
            .topbar { left: 0; padding: 0 14px; }
            .tdate { display: none; }
            .topbar .ptitle { font-size: .92rem; }
            .topbar-user-mobile { display: flex; }
            .page-body { padding: 14px 12px 82px; }
            .mobile-bottom-nav { display: flex; }

            .form-control, .form-select {
                font-size: 16px !important;
                padding: 11px 13px !important;
                min-height: 46px;
            }
            .input-group-text { padding: 11px 12px !important; min-height: 46px; }
            .btn { min-height: 44px; font-size: .9rem !important; }
            .btn-sm { min-height: 36px !important; font-size: .8rem !important; }
            .btn-lg { min-height: 50px !important; font-size: .95rem !important; }

            .card-footer .d-flex { flex-direction: column-reverse; gap: 10px !important; }
            .card-footer .btn { width: 100%; }

            .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .table th, .table td { white-space: nowrap; font-size: .82rem; padding: 10px 14px; }
            .card-body { padding: 14px !important; }

            .stat-card .s-num { font-size: 1.5rem; }
            .stat-card .s-icon { width: 38px; height: 38px; font-size: 1.1rem; }

            .btn-action { min-height: 82px; padding: 14px 8px; }
            .btn-action .ba-icon { width: 38px; height: 38px; font-size: 1.1rem; }

            .notif-sukses { left: 12px; right: 12px; transform: none; white-space: normal; bottom: 72px; top: auto; border-radius: 10px; }
            @keyframes sdn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

            /* Page header inside flex container — remove clipped border */
            .page-header {
                border-bottom: none !important;
                padding-bottom: 2px !important;
                margin-bottom: 0 !important;
                width: 100%;
            }
            .page-header h4 { font-size: 1.1rem; }
            .page-header p { font-size: .82rem; }

            /* Header action button group — full width when wrapping */
            .page-header ~ .btn,
            .page-header ~ .d-flex { width: 100%; }
            .page-header ~ .d-flex .btn { flex: 1; }

            /* Detail page back-nav header */
            .d-flex.align-items-center.gap-3.mb-4 h5 { font-size: 1rem; }

            /* Breadcrumb */
            .breadcrumb { font-size: .78rem; margin-bottom: .5rem; }

            /* Filter card */
            .card-body.py-3 { padding-top: 12px !important; padding-bottom: 12px !important; }
        }

        /* ═══════ NOTIFICATION BELL ═══════ */
        .btn-notif-bell {
            position: relative;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 7px;
            padding: 6px 9px;
            cursor: pointer;
            color: #DC2626;
            font-size: 1rem;
            transition: all .15s;
            display: flex; align-items: center;
        }
        .btn-notif-bell:hover { background: #FEE2E2; }
        .notif-count {
            position: absolute;
            top: -5px; right: -5px;
            background: #DC2626; color: #fff;
            font-size: .6rem; font-weight: 700;
            min-width: 16px; height: 16px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4px;
            border: 1.5px solid #fff;
        }

        @media print {
            .sidebar, .topbar, .mobile-bottom-nav { display: none !important; }
            .main-wrap { margin-left: 0; padding-top: 0; }
            body { background: #fff; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/logo-pt.png') }}" alt="Logo">
            <div>
                <div class="b-name">Sistem Gudang</div>
                <div class="b-sub">PT Galih Cipta Wisesa</div>
            </div>
        </div>
    </div>

    <nav class="py-1 flex-fill">
        <div class="nav-sep">Menu Utama</div>
        <a href="{{ route('dashboard') }}" class="slink {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i> Beranda
        </a>
        <a href="{{ route('barang.index') }}" class="slink {{ request()->routeIs('barang.*') ? 'active' : '' }}">
            <i class="bi bi-archive"></i> Daftar Barang
        </a>

        <div class="nav-sep">Transaksi</div>
        <a href="{{ route('transaksi.masuk') }}" class="slink {{ request()->routeIs('transaksi.masuk','transaksi.create-masuk') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-in-down"></i> Barang Masuk
        </a>
        <a href="{{ route('transaksi.keluar') }}" class="slink {{ request()->routeIs('transaksi.keluar','transaksi.create-keluar') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-up"></i> Barang Keluar
        </a>

        @if(auth()->user()->isOperator() || auth()->user()->isKepalaGudang() || auth()->user()->isAdmin())
        <div class="nav-sep">Laporan</div>
        <a href="{{ route('laporan.transaksi') }}" class="slink {{ request()->routeIs('laporan.transaksi') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i> Laporan Transaksi
        </a>
        @endif
        @if(auth()->user()->isKepalaGudang() || auth()->user()->isAdmin())
        <a href="{{ route('laporan.stok') }}" class="slink {{ request()->routeIs('laporan.stok') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data"></i> Laporan Stok
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="nav-sep">Pengaturan</div>
        <a href="{{ route('user.index') }}" class="slink {{ request()->routeIs('user.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Kelola Pengguna
        </a>
        <a href="{{ route('supplier.index') }}" class="slink {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Kelola Supplier
        </a>
        <a href="{{ route('audit.index') }}" class="slink {{ request()->routeIs('audit.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> Audit Log
        </a>
        @endif
    </nav>

    <div class="sidebar-foot">
        <div class="d-flex align-items-center gap-2">
            <div class="u-avatar">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</div>
            <div class="flex-fill overflow-hidden">
                <div class="u-name text-truncate">{{ auth()->user()->username }}</div>
                <div class="u-role">
                    {{ ['admin'=>'Administrator','kepala_gudang'=>'Kepala Gudang','operator'=>'Operator'][auth()->user()->role] ?? auth()->user()->role }}
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout" title="Keluar">
                    <i class="bi bi-box-arrow-right" style="font-size:.95rem;"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="topbar">
    <button class="btn-ham" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
    <span class="ptitle">@yield('page-title', 'Beranda')</span>
    <span class="tdate">
        <i class="bi bi-calendar3"></i>
        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </span>

    <button id="themeToggle" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Ganti Tema">
        <i class="bi bi-sun-fill" id="themeIcon"></i>
    </button>

    @if(auth()->user()->isAdmin() || auth()->user()->isKepalaGudang())
    @php
        $stokMinCount = \App\Models\Barang::where('is_active', true)
            ->whereRaw('stok_minimum >= (SELECT COALESCE(SUM(stok_akhir),0) FROM stoks WHERE id_barang = barangs.id)')
            ->count();
    @endphp
    @if($stokMinCount > 0)
    <div class="dropdown" style="flex-shrink:0;">
        <button class="btn-notif-bell" data-bs-toggle="dropdown" aria-expanded="false" title="Stok hampir habis">
            <i class="bi bi-bell-fill"></i>
            <span class="notif-count">{{ $stokMinCount }}</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-2" style="min-width:260px;max-width:320px;">
            <div style="padding:6px 8px 8px;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);">
                Stok Hampir Habis
            </div>
            @php
                $barangMin = \App\Models\Barang::where('is_active', true)
                    ->whereRaw('stok_minimum >= (SELECT COALESCE(SUM(stok_akhir),0) FROM stoks WHERE id_barang = barangs.id)')
                    ->withSum('stoks as stok_total', 'stok_akhir')
                    ->orderBy('nama_barang')->limit(8)->get();
            @endphp
            @foreach($barangMin as $b)
            <a href="{{ route('barang.index', ['search' => $b->nama_barang]) }}" class="dropdown-item d-flex justify-content-between align-items-center" style="border-radius:5px;padding:6px 8px;font-size:.82rem;">
                <span class="text-truncate" style="max-width:180px;">{{ $b->nama_barang }}</span>
                <span class="badge" style="background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;flex-shrink:0;margin-left:6px;">
                    {{ $b->stok_total ?? 0 }} {{ $b->satuan }}
                </span>
            </a>
            @endforeach
            @if($stokMinCount > 8)
            <div style="padding:4px 8px;font-size:.78rem;color:var(--text-muted);">+{{ $stokMinCount - 8 }} lainnya</div>
            @endif
            <div style="margin-top:6px;padding-top:6px;border-top:1px solid var(--border-soft);">
                <a href="{{ route('laporan.stok') }}" class="btn btn-sm btn-outline-secondary w-100" style="font-size:.78rem;">Lihat Laporan Stok</a>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- Logout mobile — selalu terlihat di topbar, tidak perlu buka sidebar --}}
    <div class="topbar-user-mobile">
        <div class="tum-avatar">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</div>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="tum-logout" title="Keluar">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</div>

<div class="main-wrap">
    <div class="page-body">

        @if(session('success'))
        <div class="notif-sukses" id="ns">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        <script>
            setTimeout(()=>{const e=document.getElementById('ns');if(e){e.style.opacity='0';e.style.transition='opacity .3s';setTimeout(()=>e.remove(),300);}},3000);
        </script>
        @endif

        @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        @hasSection('breadcrumb')
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                @yield('breadcrumb')
            </ol>
        </nav>
        @endif

        @yield('content')
    </div>
</div>

{{-- BOTTOM NAV (mobile only) --}}
<nav class="mobile-bottom-nav">
    <a href="{{ route('dashboard') }}" class="mbn-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house"></i>
        <span>Beranda</span>
    </a>
    <a href="{{ route('barang.index') }}" class="mbn-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
        <i class="bi bi-archive"></i>
        <span>Barang</span>
    </a>
    @if(auth()->user()->isOperator() || auth()->user()->isAdmin())
    <a href="{{ route('transaksi.create-masuk') }}" class="mbn-item mbn-primary {{ request()->routeIs('transaksi.create-masuk') ? 'active' : '' }}">
        <i class="bi bi-box-arrow-in-down"></i>
        <span>Masuk</span>
    </a>
    <a href="{{ route('transaksi.create-keluar') }}" class="mbn-item {{ request()->routeIs('transaksi.create-keluar') ? 'active' : '' }}">
        <i class="bi bi-box-arrow-up"></i>
        <span>Keluar</span>
    </a>
    <a href="{{ route('retur.index') }}" class="mbn-item {{ request()->routeIs('retur.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-return-left"></i>
        <span>Retur</span>
    </a>
    @endif
    @if(auth()->user()->isKepalaGudang())
    <a href="{{ route('laporan.stok') }}" class="mbn-item mbn-primary {{ request()->routeIs('laporan.stok') ? 'active' : '' }}">
        <i class="bi bi-clipboard-data"></i>
        <span>Stok</span>
    </a>
    <a href="{{ route('laporan.transaksi') }}" class="mbn-item {{ request()->routeIs('laporan.transaksi') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i>
        <span>Laporan</span>
    </a>
    <a href="{{ route('transaksi.masuk') }}" class="mbn-item {{ request()->routeIs('transaksi.masuk','transaksi.keluar') ? 'active' : '' }}">
        <i class="bi bi-list-check"></i>
        <span>Riwayat</span>
    </a>
    @endif
    @if(auth()->user()->isAdmin())
    <a href="{{ route('laporan.transaksi') }}" class="mbn-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i>
        <span>Laporan</span>
    </a>
    <a href="{{ route('user.index') }}" class="mbn-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i>
        <span>User</span>
    </a>
    @endif
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay').classList.toggle('open');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('overlay').classList.remove('open');}

function toggleNavDropdown(id) {
    const drop = document.getElementById(id);
    const trigger = drop.previousElementSibling;
    const isShow = drop.classList.contains('show');
    
    if (isShow) {
        drop.classList.remove('show');
        trigger.setAttribute('aria-expanded', 'false');
    } else {
        drop.classList.add('show');
        trigger.setAttribute('aria-expanded', 'true');
    }
}

// Dark Mode Toggle
const themeToggle = document.getElementById('themeToggle');
const themeIcon   = document.getElementById('themeIcon');
const htmlEl      = document.documentElement;

function updateIcon(theme) {
    if (theme === 'dark') {
        themeIcon.className = 'bi bi-moon-stars-fill text-warning';
    } else {
        themeIcon.className = 'bi bi-sun-fill text-muted';
    }
}

// Set initial icon
updateIcon(htmlEl.getAttribute('data-bs-theme'));

themeToggle.addEventListener('click', () => {
    const currentTheme = htmlEl.getAttribute('data-bs-theme');
    const newTheme     = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.body.classList.add('theme-transitioning');
    htmlEl.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateIcon(newTheme);
    setTimeout(() => document.body.classList.remove('theme-transitioning'), 400);
});

// Global: anti double-submit + loading state
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = form.querySelector('button[type="submit"]');
            if (!btn || btn.dataset.noLoading) return;
            if (btn.disabled) return;
            btn.disabled = true;
            btn.dataset.origHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;border-width:2px;"></span>Menyimpan...';
            setTimeout(() => {
                if (btn.disabled) { btn.disabled = false; btn.innerHTML = btn.dataset.origHtml; }
            }, 12000);
        });
    });

    // Search debounce
    document.querySelectorAll('input[name="search"]').forEach(function(input) {
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                input.closest('form').submit();
            }, 500);
        });
    });
});
</script>
@stack('scripts')
<script>
// ── Session Timeout Warning — hanya di halaman create/edit ─────────────────
(function() {
    var TIMEOUT_MS = 30 * 60 * 1000;
    var WARNING_MS = 25 * 60 * 1000;
    var PING_URL   = window.APP_URL + '/api/last-update';
    var path       = window.location.pathname;

    if (!path.match(/\/(create|edit)(\/|$)/)) return;

    var lastActivity = Date.now();

    ['mousemove','keydown','click','touchstart','scroll'].forEach(function(e) {
        document.addEventListener(e, function() {
            lastActivity = Date.now();
            hideWarn();
        }, { passive: true });
    });

    function showWarn(remaining) {
        var el = document.getElementById('sw-toast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'sw-toast';
            el.style.cssText = 'position:fixed;bottom:24px;right:20px;z-index:9999;background:#fff;' +
                'border:1px solid #FCA5A5;border-left:4px solid #DC2626;border-radius:8px;' +
                'padding:14px 16px;box-shadow:0 4px 20px rgba(0,0,0,.14);min-width:290px;display:none;';
            el.innerHTML =
                '<div style="display:flex;align-items:center;gap:10px;">' +
                    '<i class="bi bi-clock-history" style="color:#DC2626;font-size:1.2rem;flex-shrink:0;"></i>' +
                    '<div style="flex:1;">' +
                        '<div style="font-weight:600;font-size:.88rem;color:#111827;">Sesi Hampir Berakhir</div>' +
                        '<div style="font-size:.8rem;color:#6B7280;">Logout otomatis dalam <strong id="sw-cd">—</strong></div>' +
                    '</div>' +
                    '<button id="sw-btn" style="background:#F5821F;border:none;border-radius:5px;color:#fff;' +
                        'font-size:.78rem;font-weight:600;padding:6px 12px;cursor:pointer;flex-shrink:0;">' +
                        'Lanjutkan' +
                    '</button>' +
                '</div>';
            document.body.appendChild(el);
            document.getElementById('sw-btn').addEventListener('click', pingSession);
        }
        var mins = Math.floor(remaining / 60000);
        var secs = Math.floor((remaining % 60000) / 1000);
        var cd = document.getElementById('sw-cd');
        if (cd) cd.textContent = (mins > 0 ? mins + ' mnt ' : '') + secs + ' dtk';
        el.style.display = 'block';
    }

    function hideWarn() {
        var el = document.getElementById('sw-toast');
        if (el) el.style.display = 'none';
    }

    function pingSession() {
        fetch(PING_URL, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function() { lastActivity = Date.now(); hideWarn(); })
            .catch(function() {});
    }

    setInterval(function() {
        var idle      = Date.now() - lastActivity;
        var remaining = TIMEOUT_MS - idle;
        if (idle >= WARNING_MS && remaining > 0) showWarn(remaining);
    }, 1000);
})();

// ── Global auto-refresh: berlaku di semua halaman ──────────────────────────
(function() {
    var CHECK_URL = window.APP_URL + '/api/last-update';
    var knownTs   = null;

    function isSafePage() {
        var p = window.location.pathname;
        return !p.match(/\/(create|edit)(\/|$)/);
    }

    function check() {
        if (!isSafePage()) return;
        fetch(CHECK_URL, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.ok ? r.json() : null; })
            .then(function(data) {
                if (!data) return;
                if (knownTs === null) { knownTs = data.ts; return; }
                if (data.ts > knownTs) { window.location.reload(); }
            })
            .catch(function() {});
    }

    setInterval(check, 45000);
    check();
})();
</script>
</body>
</html>
