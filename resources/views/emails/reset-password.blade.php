<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - MyTrades</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .wrapper { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 28px; text-align: center; }
        .header h1 { color: #38bdf8; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: #94a3b8; margin: 6px 0 0 0; font-size: 13px; }
        .content { padding: 32px 28px; }
        .greeting { font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 16px; }
        .message { font-size: 15px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: #2563eb; color: #ffffff !important; font-weight: 600; font-size: 15px; padding: 14px 28px; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(37,99,235,0.25); }
        .footer { background: #f1f5f9; padding: 20px 28px; font-size: 12px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; }
        .token-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 12px; font-family: monospace; font-size: 13px; word-break: break-all; color: #334155; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ config('app.url') }}/images/logo.png" alt="MyTrades" style="height: 44px; width: auto; max-width: 100%; display: block; margin: 0 auto 8px auto;">
            <p>Smart Trading Journal & Performance Analytics</p>
        </div>
        <div class="content">
            <div class="greeting">Hello {{ $user->first_name }},</div>
            <div class="message">
                You recently requested to reset your password for your <strong>MyTrades</strong> account. Click the button below to choose a new password:
            </div>
            <div class="btn-container">
                <a href="{{ $resetUrl }}" class="btn">Reset My Password</a>
            </div>
            <div class="message">
                This password reset link will expire in 60 minutes. If you did not request a password reset, you can safely ignore this email.
            </div>
            <div class="token-box">
                If the button above does not work, copy and paste this link into your browser:<br>
                <a href="{{ $resetUrl }}" style="color: #2563eb;">{{ $resetUrl }}</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MyTrades Trading Journal. All rights reserved.
        </div>
    </div>
</body>
</html>
