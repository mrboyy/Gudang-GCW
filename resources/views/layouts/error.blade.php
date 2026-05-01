<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') — Gudang GCW</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .brand-bar { background: linear-gradient(135deg, #0D1117 0%, #1A202E 100%); padding: 1rem 1.5rem; }
        .brand-name { color: #F5821F; font-weight: 700; font-size: 1.1rem; letter-spacing: .5px; }
    </style>
</head>
<body>
    <div class="brand-bar">
        <span class="brand-name"><i class="bi bi-boxes me-2"></i>Gudang GCW</span>
    </div>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
