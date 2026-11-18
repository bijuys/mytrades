<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $msgSubject }} - MyTrades</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: #0f172a; padding: 28px 24px; text-align: center; }
        .header h1 { color: #6366f1; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { color: #94a3b8; margin: 6px 0 0 0; font-size: 13px; font-weight: 500; }
        .content { padding: 36px 30px; }
        .badge { display: inline-block; padding: 4px 10px; background: #ede9fe; color: #6d28d9; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 16px; letter-spacing: 0.5px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 16px; }
        .message-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; font-size: 15px; line-height: 1.7; color: #334155; margin: 20px 0; }
        .sender-note { font-size: 13px; color: #64748b; margin-top: 20px; }
        .btn-container { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; font-weight: 700; font-size: 14px; padding: 12px 26px; text-decoration: none; border-radius: 8px; }
        .footer { background: #f1f5f9; padding: 20px 30px; font-size: 12px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>📈 MyTrades</h1>
            <p>Admin Direct Communication</p>
        </div>
        <div class="content">
            <div class="badge">Official Message from Super Admin</div>
            <div class="greeting">Hello {{ $user->first_name }},</div>
            <p style="font-size: 14px; color: #64748b; margin-top: 0;">
                You have received a direct message from MyTrades Administrator ({{ $admin->full_name }}):
            </p>

            <div class="message-box">
                <strong style="display: block; margin-bottom: 10px; color: #0f172a; font-size: 16px;">Subject: {{ $msgSubject }}</strong>
                <div>
                    {!! nl2br(e($messageBody)) !!}
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ config('app.url') }}/trades" class="btn">Log In to Your Account &rarr;</a>
            </div>

            <div class="sender-note">
                Need to follow up? You can reply directly to this email or reach us anytime at {{ config('mail.from.address') }}.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MyTrades Trading Journal.
        </div>
    </div>
</body>
</html>
