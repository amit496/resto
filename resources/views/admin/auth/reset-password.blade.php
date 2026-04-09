<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FoodiHub') }} Reset Admin Password</title>
    <link rel="icon" type="image/png" href="{{ asset('admin/assets/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800|manrope:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root { --accent:#f9185a; --accent-dark:#cf1249; --ink:#161616; --paper:rgba(255,255,255,0.92); }
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;font-family:'Manrope',sans-serif;color:#fff;background:linear-gradient(180deg,rgba(12,12,12,0.2),rgba(12,12,12,0.6)),url('{{ asset('admin/assets/img/authentication/login-img.jpg') }}') center/cover no-repeat;display:grid;place-items:center;padding:24px}
        .auth-shell{width:min(1120px,100%);display:grid;grid-template-columns:1fr 420px;gap:28px;align-items:center}
        .auth-copy h1{margin:0 0 14px;font-family:'Outfit',sans-serif;font-size:clamp(46px,7vw,88px);letter-spacing:-0.06em;line-height:.92}
        .auth-copy p{max-width:520px;margin:0 0 22px;color:rgba(255,255,255,0.76);line-height:1.7}
        .chip-row{display:flex;gap:12px;flex-wrap:wrap}
        .chip{padding:10px 14px;border-radius:999px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.12);font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.08em}
        .phone-shell{position:relative;padding:18px;border-radius:44px;background:linear-gradient(180deg,#5b6068,#1b1f24);box-shadow:0 40px 90px rgba(0,0,0,0.3)}
        .phone-notch{position:absolute;left:50%;top:10px;transform:translateX(-50%);width:150px;height:22px;border-radius:0 0 18px 18px;background:#111;z-index:3}
        .phone-screen{position:relative;overflow:hidden;min-height:760px;border-radius:34px;background:linear-gradient(180deg,rgba(12,12,12,0.2),rgba(12,12,12,0.72)),url('{{ asset('admin/assets/img/authentication/login-img.jpg') }}') center/cover no-repeat;padding:34px 26px 28px}
        .screen-brand{display:flex;align-items:center;gap:12px;margin-bottom:28px}
        .screen-logo{width:46px;height:46px;padding:10px;border-radius:14px;background:rgba(255,255,255,0.18);backdrop-filter:blur(12px)}
        .screen-title{font-family:'Outfit',sans-serif;font-size:28px;font-weight:800;letter-spacing:-0.04em}
        .screen-copy{margin:0 0 24px;color:rgba(255,255,255,0.82)}
        .kicker{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.18);text-transform:uppercase;letter-spacing:.12em;font-size:11px;font-weight:800}
        .auth-card{margin-top:28px;background:var(--paper);border-radius:22px;padding:22px;color:var(--ink);box-shadow:0 20px 50px rgba(0,0,0,0.2)}
        .auth-input{width:100%;height:48px;border-radius:14px;border:1px solid #e7e7e7;padding:0 14px 0 40px;font:600 14px/1 'Manrope',sans-serif;color:#1f1f1f;background:#fff}
        .input-shell{position:relative;margin-bottom:14px}
        .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9a9a9a;font-weight:700}
        .submit-btn{width:100%;height:50px;border:0;border-radius:16px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font:800 14px/1 'Manrope',sans-serif;cursor:pointer;margin-top:6px}
        .error-box{border-radius:14px;padding:12px 14px;font-size:13px;margin-bottom:14px;background:#ffe9e6;color:#9b2e1b}
        .secondary-links{margin-top:16px;text-align:center;font-size:13px;color:#6a6a6a}
        .secondary-links a{color:#1f1f1f;font-weight:700;text-decoration:none}
        .screen-foot{margin-top:24px;font-size:12px;color:rgba(255,255,255,0.72);line-height:1.6}
        .screen-foot a{color:#fff;font-weight:700;text-decoration:none}
        @media (max-width:980px){.auth-shell{grid-template-columns:1fr}.auth-copy{display:none}.phone-shell{width:min(440px,100%);margin:0 auto}}
        @media (max-width:520px){.phone-shell{padding:12px;border-radius:34px}.phone-screen{min-height:720px;padding:28px 18px 24px;border-radius:28px}.screen-title{font-size:24px}}
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="auth-copy">
            <div class="kicker">FoodiHub Admin Access</div>
            <h1>Set a new admin password.</h1>
            <p>Create a new password to regain admin access.</p>
            <div class="chip-row">
                <div class="chip">Secure</div>
                <div class="chip">Admin Only</div>
                <div class="chip">Fast Reset</div>
            </div>
        </section>

        <section class="phone-shell">
            <div class="phone-notch"></div>
            <div class="phone-screen">
                <div class="screen-brand">
                    <img src="{{ asset('admin/assets/img/logo-small.png') }}" alt="{{ config('app.name', 'FoodiHub') }}" class="screen-logo">
                    <div>
                        <div class="screen-title">FoodiHub</div>
                        <div style="color:rgba(255,255,255,0.72);font-size:12px;">Admin Reset</div>
                    </div>
                </div>

                <div class="kicker">New Credentials</div>
                <h2 style="margin:0 0 8px;font-family:'Outfit',sans-serif;font-size:44px;letter-spacing:-0.05em;line-height:0.92;">Set new password</h2>
                <p class="screen-copy">Use a strong password for your admin account.</p>

                <div class="auth-card">
                    @if ($errors->any())
                        <div class="error-box">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="input-shell">
                            <span class="input-icon">@</span>
                            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email', request('email')) }}" required autofocus placeholder="Admin Email">
                        </div>
                        <div class="input-shell">
                            <span class="input-icon">*</span>
                            <input id="password" class="auth-input" type="password" name="password" required placeholder="New Password">
                        </div>
                        <div class="input-shell">
                            <span class="input-icon">*</span>
                            <input id="password_confirmation" class="auth-input" type="password" name="password_confirmation" required placeholder="Confirm Password">
                        </div>
                        <button type="submit" class="submit-btn">Reset password</button>
                    </form>

                    <div class="secondary-links">
                        Back to <a href="{{ route('admin.login') }}">Admin login</a>
                    </div>
                </div>

                <div class="screen-foot">
                    Keep this password private and secure.
                </div>
            </div>
        </section>
    </div>
</body>
</html>
