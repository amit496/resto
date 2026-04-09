<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FoodiHub') }} Login</title>
    <link rel="icon" type="image/png" href="{{ asset('admin/assets/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700,800|manrope:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --accent: #f9185a;
            --accent-dark: #cf1249;
            --ink: #161616;
            --muted: #808080;
            --paper: rgba(255,255,255,0.92);
            --line: rgba(255,255,255,0.18);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: #fff;
            background:
                linear-gradient(180deg, rgba(12,12,12,0.2), rgba(12,12,12,0.6)),
                url('{{ asset('admin/assets/img/authentication/login-img.jpg') }}') center/cover no-repeat;
            display: grid;
            place-items: center;
            padding: 24px;
        }
        .auth-shell {
            width: min(1120px, 100%);
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 28px;
            align-items: center;
        }
        .auth-copy h1 {
            margin: 0 0 14px;
            font-family: 'Outfit', sans-serif;
            font-size: clamp(46px, 7vw, 88px);
            letter-spacing: -0.06em;
            line-height: 0.92;
        }
        .auth-copy p {
            max-width: 520px;
            margin: 0 0 22px;
            color: rgba(255,255,255,0.76);
            line-height: 1.7;
        }
        .chip-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .chip {
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.12);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .phone-shell {
            position: relative;
            padding: 18px;
            border-radius: 44px;
            background: linear-gradient(180deg, #5b6068, #1b1f24);
            box-shadow: 0 40px 90px rgba(0,0,0,0.3);
        }
        .phone-notch {
            position: absolute;
            left: 50%;
            top: 10px;
            transform: translateX(-50%);
            width: 150px;
            height: 22px;
            border-radius: 0 0 18px 18px;
            background: #111;
            z-index: 3;
        }
        .phone-screen {
            position: relative;
            overflow: hidden;
            min-height: 760px;
            border-radius: 34px;
            background:
                linear-gradient(180deg, rgba(12,12,12,0.2), rgba(12,12,12,0.72)),
                url('{{ asset('admin/assets/img/authentication/login-img.jpg') }}') center/cover no-repeat;
            padding: 34px 26px 28px;
        }
        .screen-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .screen-logo {
            width: 46px;
            height: 46px;
            padding: 10px;
            border-radius: 14px;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(12px);
        }
        .screen-title {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.04em;
        }
        .screen-copy {
            margin: 0 0 24px;
            color: rgba(255,255,255,0.82);
        }
        .kicker {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }
        .auth-card {
            margin-top: 42px;
            padding: 0;
        }
        .status-box,
        .error-box {
            padding: 12px 14px;
            border-radius: 16px;
            margin-bottom: 14px;
            font-size: 14px;
        }
        .status-box {
            background: rgba(34,160,107,0.16);
            border: 1px solid rgba(34,160,107,0.24);
        }
        .error-box {
            background: rgba(249,24,90,0.16);
            border: 1px solid rgba(249,24,90,0.24);
        }
        .field {
            margin-bottom: 12px;
        }
        .input-shell {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 56px;
            padding: 0 16px;
            border-radius: 14px;
            background: rgba(255,255,255,0.94);
            color: var(--ink);
        }
        .input-icon {
            width: 28px;
            text-align: center;
            color: var(--muted);
            font-weight: 800;
        }
        .auth-input {
            flex: 1;
            min-width: 0;
            height: 54px;
            border: 0;
            background: transparent;
            color: var(--ink);
            font: inherit;
        }
        .auth-input:focus {
            outline: none;
        }
        .submit-btn,
        .social-btn {
            width: 100%;
            min-height: 56px;
            border-radius: 14px;
            border: 0;
            font-weight: 800;
            font-family: inherit;
        }
        .submit-btn {
            margin-top: 10px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            box-shadow: 0 18px 36px rgba(249,24,90,0.28);
            cursor: pointer;
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: rgba(255,255,255,0.74);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.2);
        }
        .social-stack {
            display: grid;
            gap: 10px;
        }
        .social-btn {
            background: rgba(255,255,255,0.94);
            color: #1a1a1a;
        }
        .social-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .screen-foot {
            position: absolute;
            left: 26px;
            right: 26px;
            bottom: 24px;
            color: rgba(255,255,255,0.74);
            font-size: 12px;
            text-align: center;
        }
        .screen-foot a {
            color: #fff;
            text-decoration: underline;
        }
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
            color: rgba(255,255,255,0.82);
            font-size: 13px;
        }
        .meta-row a {
            color: #fff;
            font-weight: 800;
        }
        @media (max-width: 980px) {
            body {
                padding: 16px;
            }
            .auth-shell {
                grid-template-columns: 1fr;
            }
            .auth-copy {
                display: none;
            }
            .phone-shell {
                width: min(440px, 100%);
                margin: 0 auto;
            }
        }
        @media (max-width: 520px) {
            .phone-shell {
                padding: 12px;
                border-radius: 34px;
            }
            .phone-screen {
                min-height: 720px;
                padding: 28px 18px 24px;
                border-radius: 28px;
            }
            .screen-title {
                font-size: 24px;
            }
            .social-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="auth-copy">
            <div class="kicker">Food App Inspired Auth</div>
            <h1>Welcome back to the admin side of FoodiHub.</h1>
            <p>The web version now follows the same mobile-first visual tone as the reference: glassy overlays, bold CTA, stacked sign-in options and a strong food-background composition.</p>
            <div class="chip-row">
                <div class="chip">OTP Style UI</div>
                <div class="chip">Responsive Phone Shell</div>
                <div class="chip">Food Background</div>
            </div>
        </section>

        <section class="phone-shell">
            <div class="phone-notch"></div>
            <div class="phone-screen">
                <div class="screen-brand">
                    <img src="{{ asset('admin/assets/img/logo-small.png') }}" alt="{{ config('app.name', 'FoodiHub') }}" class="screen-logo">
                    <div>
                        <div class="screen-title">FoodiHub</div>
                        <div style="color:rgba(255,255,255,0.72);font-size:12px;">Admin Access</div>
                    </div>
                </div>

                <div class="kicker">Welcome Back</div>
                <h2 style="margin:0 0 8px;font-family:'Outfit',sans-serif;font-size:44px;letter-spacing:-0.05em;line-height:0.92;">Sign in to continue</h2>
                <p class="screen-copy">Use your admin credentials to access branches, orders, offers and payment flow.</p>

                <div class="auth-card">
                    @if (session('status'))
                        <div class="status-box">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="error-box">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="field">
                            <div class="input-shell">
                                <span class="input-icon">@</span>
                                <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Email Address">
                            </div>
                        </div>

                        <div class="field">
                            <div class="input-shell">
                                <span class="input-icon">#</span>
                                <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password" placeholder="Password">
                            </div>
                        </div>

                        <button type="submit" class="submit-btn">Send OTP Style Login</button>

                        <div class="meta-row">
                            <label style="display:inline-flex;align-items:center;gap:8px;">
                                <input id="remember_me" type="checkbox" name="remember">
                                <span>Keep me signed in</span>
                            </label>
                            <a href="{{ route('admin.password.request') }}">Forgot password?</a>
                        </div>
                    </form>

                    <div class="divider">or</div>

                    <div class="social-stack">
                        <button type="button" class="social-btn">Continue With Email</button>
                        <button type="button" class="social-btn">Continue With Apple</button>
                        <div class="social-grid">
                            <button type="button" class="social-btn">Facebook</button>
                            <button type="button" class="social-btn">Google</button>
                        </div>
                    </div>
                </div>

                <div class="screen-foot">
                    By continuing, you agree to our
                    <a href="#">Terms of Service</a>,
                    <a href="#">Privacy Policy</a> and
                    <a href="#">Content Policies</a>.
                </div>
            </div>
        </section>
    </div>
</body>
</html>


