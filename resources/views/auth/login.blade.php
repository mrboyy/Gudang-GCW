<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Gudang PT GCW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            /* Abu-abu silver dari logo GCW */
            background: linear-gradient(145deg, #C8CAC9 0%, #9EA2A2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-font-smoothing: antialiased;
            padding: 24px 16px;
        }

        .login-wrap { width: 100%; max-width: 400px; }

        .login-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,.18), 0 1px 4px rgba(0,0,0,.1);
            overflow: hidden;
        }

        /* ── Header: abu-abu gelap + garis oranye atas (dua warna logo) ── */
        .login-head {
            background: linear-gradient(160deg, #4B5563 0%, #1F2937 100%);
            border-top: 5px solid #F5821F;
            padding: 26px 30px 22px;
            position: relative;
            overflow: hidden;
        }
        .login-head::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 130px; height: 130px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
        }
        .login-head::after {
            content: '';
            position: absolute;
            bottom: -30px; left: 20px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(245,130,31,.06);
        }
        .login-head-top {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-bottom: 14px;
            position: relative;
        }
        .logo-box {
            width: 46px; height: 46px;
            background: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
        }
        .logo-box img {
            width: 30px; height: 30px;
            object-fit: contain;
        }
        .login-company {
            font-size: .79rem;
            font-weight: 600;
            color: rgba(255,255,255,.7);
            letter-spacing: .01em;
        }
        .login-head-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
            position: relative;
            letter-spacing: -.01em;
        }
        .login-head-sub {
            font-size: .81rem;
            color: rgba(255,255,255,.5);
            margin-top: 3px;
            position: relative;
        }

        /* ── Body ── */
        .login-body { padding: 26px 30px 30px; }

        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            display: block;
        }

        .field-wrap { position: relative; }
        .field-wrap .f-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: .9rem;
            pointer-events: none;
            z-index: 2;
        }
        .field-wrap input {
            width: 100%;
            padding: 10px 12px 10px 34px;
            font-size: .9rem;
            font-family: inherit;
            border: 1.5px solid #D1D5DB;
            border-radius: 6px;
            color: #111827;
            background: #F9FAFB;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .field-wrap input:focus {
            background: #fff;
            border-color: #F5821F;
            box-shadow: 0 0 0 3px rgba(245,130,31,.12);
        }
        .field-wrap input.has-toggle { padding-right: 40px; }
        .field-wrap .toggle-pass {
            position: absolute;
            right: 0; top: 0; bottom: 0;
            width: 40px;
            background: none;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            z-index: 2;
        }
        .field-wrap .toggle-pass:hover { color: #6B7280; }

        .alert-error {
            background: #FEF2F2;
            border-left: 3px solid #DC2626;
            border-radius: 5px;
            padding: 10px 14px;
            color: #B91C1C;
            font-size: .83rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .btn-login {
            width: 100%;
            background: #F5821F;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: .9rem;
            font-weight: 700;
            font-family: inherit;
            padding: 11px 16px;
            margin-top: 6px;
            cursor: pointer;
            transition: background .15s, box-shadow .15s;
            box-shadow: 0 2px 8px rgba(245,130,31,.35);
        }
        .btn-login:hover  { background: #D96E10; box-shadow: 0 4px 14px rgba(245,130,31,.4); }
        .btn-login:active { background: #C26209; }

        .login-hint {
            font-size: .76rem;
            color: #9CA3AF;
            text-align: center;
            margin-top: 14px;
        }

        .login-footer {
            text-align: center;
            color: rgba(0,0,0,.35);
            font-size: .72rem;
            margin-top: 14px;
        }

        @media (max-width: 460px) {
            .login-head  { padding: 22px 22px 18px; }
            .login-body  { padding: 22px 22px 26px; }
        }
    </style>
</head>
<body>

<div class="login-wrap">
    <div class="login-card">

        <div class="login-head">
            <div class="login-head-top">
                <div class="logo-box">
                    <img src="{{ asset('images/logo-pt.png') }}" alt="Logo PT GCW">
                </div>
                <span class="login-company">PT Galih Cipta Wisesa</span>
            </div>
            <div class="login-head-title">Sistem Pencatatan Gudang</div>
            <div class="login-head-sub">Masuk untuk melanjutkan</div>
        </div>

        <div class="login-body">

            @if($errors->any())
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label">Username</label>
                    <div class="field-wrap">
                        <i class="bi bi-person f-icon"></i>
                        <input type="text" name="username"
                               value="{{ old('username') }}"
                               placeholder="Masukkan username"
                               autofocus autocomplete="username"
                               class="{{ $errors->has('username') ? 'is-invalid' : '' }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="field-wrap">
                        <i class="bi bi-lock f-icon"></i>
                        <input type="password" name="password" id="passInput"
                               class="has-toggle"
                               placeholder="Masukkan password"
                               autocomplete="current-password">
                        <button type="button" class="toggle-pass" onclick="togglePass()" tabindex="-1">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <p class="login-hint">Lupa password? Hubungi administrator.</p>
        </div>
    </div>

    <p class="login-footer">&copy; {{ date('Y') }} PT Galih Cipta Wisesa</p>
</div>

<script>
function togglePass() {
    const inp = document.getElementById('passInput');
    const ico = document.getElementById('eyeIcon');
    if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; ico.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
