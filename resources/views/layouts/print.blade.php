<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — PT Galih Cipta Wisesa</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 12px;
            color: #111;
            background: #fff;
            padding: 0;
        }
        .print-wrap { max-width: 800px; margin: 0 auto; padding: 32px 28px; }

        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #111; }
        .doc-header .company { display: flex; align-items: center; gap: 12px; }
        .doc-header .company .co-logo { height: 48px; width: auto; object-fit: contain; }
        .doc-header .company .co-name { font-size: 15px; font-weight: 700; color: #111; letter-spacing: -0.02em; }
        .doc-header .company .co-sub { font-size: 10px; color: #555; margin-top: 2px; }
        .doc-header .doc-title { text-align: right; }
        .doc-header .doc-title .dt-label { font-size: 18px; font-weight: 800; color: #111; letter-spacing: -0.03em; }
        .doc-header .doc-title .dt-no { font-size: 11px; color: #555; margin-top: 3px; font-weight: 500; }

        .doc-section-title {
            font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;
            color: #888; margin-bottom: 8px; margin-top: 20px;
        }

        table.info-table { width: 100%; border-collapse: collapse; }
        table.info-table td { padding: 5px 0; font-size: 12px; vertical-align: top; }
        table.info-table td:first-child { color: #555; width: 140px; font-weight: 500; }
        table.info-table td:nth-child(2) { width: 16px; color: #aaa; }
        table.info-table td:last-child { font-weight: 600; color: #111; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.data-table th {
            font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
            color: #555; background: #F3F4F6; padding: 7px 10px;
            border: 1px solid #E5E7EB; text-align: left;
        }
        table.data-table td { font-size: 11px; padding: 7px 10px; border: 1px solid #E5E7EB; color: #111; vertical-align: middle; }
        table.data-table tr:nth-child(even) td { background: #FAFBFC; }
        table.data-table tfoot td { font-weight: 700; background: #F3F4F6; }

        .highlight-box {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 16px;
        }
        .highlight-box .qty-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #888; margin-bottom: 4px; }
        .highlight-box .qty-val { font-size: 28px; font-weight: 800; color: #111; line-height: 1; letter-spacing: -0.04em; }
        .highlight-box .qty-unit { font-size: 13px; font-weight: 500; color: #555; margin-left: 4px; }

        .badge-jenis { font-size: 11px; }
        .badge-masuk, .badge-keluar, .badge-retur, .badge-retur-p, .badge-void { }

        .doc-footer {
            margin-top: 32px; padding-top: 14px; border-top: 1px solid #E5E7EB;
            display: flex; justify-content: space-between; align-items: center;
        }
        .doc-footer .gen-info { font-size: 9px; color: #aaa; }
        .doc-footer .sign-box { text-align: center; }
        .doc-footer .sign-box .sign-line { width: 140px; border-bottom: 1px solid #333; margin-bottom: 4px; height: 40px; }
        .doc-footer .sign-box .sign-label { font-size: 10px; color: #555; }

        .void-banner {
            background: #FEF2F2; border: 1px solid #FECACA; border-radius: 6px;
            padding: 10px 14px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
        }
        .void-banner .vb-icon { font-size: 14px; color: #DC2626; }
        .void-banner .vb-text { font-size: 11px; color: #991B1B; font-weight: 600; }

        .no-print { }
        .print-btn-bar {
            position: fixed; top: 12px; right: 16px;
            z-index: 999;
        }
        .print-btn-bar button {
            background: #fff; border: 1px solid #ccc; color: #333;
            padding: 5px 16px; border-radius: 4px; font-size: 13px;
            cursor: pointer;
        }
        .print-btn-bar button:hover { background: #f0f0f0; }

        @media print {
            .no-print, .print-btn-bar { display: none !important; }
            .print-wrap { padding: 1cm 1.2cm; max-width: 100%; box-shadow: none; border-radius: 0; margin: 0; }
            body { font-size: 11px; background: #fff; padding-top: 0; }
            @page { margin: 0; size: A4; }
        }
        @media screen {
            body { background: #F3F4F6; padding-top: 0; }
            .print-wrap { background: #fff; box-shadow: 0 4px 24px rgba(0,0,0,.08); border-radius: 8px; margin: 20px auto; }
        }
    </style>
</head>
<body>

<div class="print-btn-bar no-print">
    <button onclick="window.print()">Cetak</button>
</div>

<div class="print-wrap">
    <div class="doc-header">
        <div class="company">
            <img src="{{ asset('images/logo-pt.png') }}" alt="Logo PT GCW" class="co-logo">
            <div>
                <div class="co-name">PT Galih Cipta Wisesa</div>
                <div class="co-sub">Sistem Pencatatan Gudang</div>
            </div>
        </div>
        <div class="doc-title">
            <div class="dt-label">@yield('doc-title')</div>
            <div class="dt-no">@yield('doc-no')</div>
        </div>
    </div>

    @yield('content')

    <div class="doc-footer">
        <div class="gen-info">
            Dicetak oleh: {{ auth()->user()->username }} &nbsp;·&nbsp;
            {{ now()->format('d/m/Y H:i') }}
        </div>
        @yield('signature')
    </div>
</div>

</body>
</html>
