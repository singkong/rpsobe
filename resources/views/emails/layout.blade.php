<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RPS OBE</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #1e293b;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #206bc4;
            padding: 24px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 20px;
            margin: 0;
            font-weight: 600;
        }
        .email-body {
            padding: 32px 24px;
        }
        .email-body h2 {
            color: #1e293b;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .email-body p {
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .email-body .highlight {
            font-weight: 600;
            color: #1e293b;
        }
        .email-button {
            display: inline-block;
            background-color: #206bc4;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            margin: 16px 0;
        }
        .email-footer {
            background-color: #f4f6fa;
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .email-divider {
            border-top: 1px solid #e2e8f0;
            margin: 24px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>RPS OBE</h1>
        </div>
        <div class="email-body">
            @yield('content')
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} RPS OBE. Sistem Manajemen RPS Berbasis OBE.</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
