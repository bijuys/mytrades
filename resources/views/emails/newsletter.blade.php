<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $newsletterSubject }} - MyTrades Newsletter</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .wrapper { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); padding: 32px 28px; text-align: center; }
        .header h1 { color: #6366f1; margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { color: #cbd5e1; margin: 6px 0 0 0; font-size: 13px; font-weight: 500; }
        .content { padding: 36px 32px; }
        .greeting { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 20px; }
        .body-text { font-size: 15px; line-height: 1.7; color: #334155; }
        .body-text p { margin-bottom: 16px; }
        .footer { background: #f1f5f9; padding: 24px 32px; font-size: 12px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; }
        .btn-container { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; font-weight: 700; font-size: 14px; padding: 12px 26px; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>📈 MyTrades</h1>
            <p>Trading Journal Community &amp; Performance Insights</p>
        </div>
        <div class="content">
            <div class="greeting">Hello {{ $user->first_name }},</div>
            <div class="body-text">
                {!! nl2br(e($newsletterContent)) !!}
            </div>
            <div class="btn-container">
                <a href="{{ config('app.url') }}/trades" class="btn">Open My Trading Journal &rarr;</a>
            </div>
        </div>
        <div class="footer">
            You received this newsletter as a registered member of MyTrades.<br>
            &copy; {{ date('Y') }} MyTrades Trading Journal.
        </div>
    </div>
</body>
</html>
