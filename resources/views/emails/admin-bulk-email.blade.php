<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f7fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
        }
        .shell {
            max-width: 680px;
            margin: 0 auto;
            padding: 32px 16px;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.08);
        }
        .header {
            padding: 28px 32px;
            background: linear-gradient(135deg, #111827, #374151);
            color: #fff;
        }
        .content {
            padding: 32px;
            line-height: 1.7;
            font-size: 15px;
        }
        .footer {
            padding: 0 32px 28px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="card">
            <div class="header">
                <div style="font-size:12px;letter-spacing:.16em;text-transform:uppercase;opacity:.8;">{{ config('app.name') }}</div>
                <div style="font-size:26px;font-weight:700;margin-top:6px;">{{ config('app.name') }} message</div>
            </div>
            <div class="content">
                {!! nl2br(e($body)) !!}
            </div>
            <div class="footer">
                This email was sent from the admin panel of {{ config('app.name') }}.
            </div>
        </div>
    </div>
</body>
</html>
