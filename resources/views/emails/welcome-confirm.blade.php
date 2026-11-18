<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MyTrades - Confirm Your Email</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); padding: 32px 28px; text-align: center; }
        .header h1 { color: #38bdf8; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: #cbd5e1; margin: 8px 0 0 0; font-size: 13px; font-weight: 500; }
        .content { padding: 36px 32px; }
        .greeting { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 16px; }
        .message { font-size: 15px; line-height: 1.65; color: #334155; margin-bottom: 20px; }
        .highlight-box { background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 8px; padding: 16px 20px; margin: 24px 0; font-size: 14px; line-height: 1.6; color: #14532d; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff !important; font-weight: 700; font-size: 15px; padding: 14px 32px; text-decoration: none; border-radius: 10px; box-shadow: 0 4px 14px rgba(79,70,229,0.35); }
        .feedback-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 20px; margin: 28px 0; font-size: 14px; line-height: 1.6; color: #475569; }
        .feedback-box strong { color: #1e293b; }
        .footer { background: #f1f5f9; padding: 24px 32px; font-size: 12px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; }
        .token-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-family: monospace; font-size: 12px; word-break: break-all; color: #475569; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1 style="color: #6366f1; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; margin: 0 0 6px 0;">📈 MyTrades</h1>
            <p>Smart Trading Journal &amp; Performance Analytics</p>
        </div>
        <div class="content">
            <div class="greeting">Welcome aboard, {{ $user->first_name }}! 👋</div>
            
            <div class="message">
                We are absolutely thrilled to welcome you to the <strong>MyTrades</strong> family! Wishing you the very best on your trading journey ahead. 
            </div>

            <div class="message">
                Trading is a marathon of discipline, continuous learning, and risk management. With MyTrades, you now have the professional workspace to track every entry, exit, lesson, and metric that leads to long-term profitability.
            </div>

            <div class="highlight-box">
                <strong>Next Step: Confirm Your Email to Submit for Admin Approval</strong><br>
                To keep our community secure and ensure high-touch platform quality, all trader accounts undergo a quick Super Admin review. Please confirm your email address below so your registration moves forward to the admin queue!
            </div>

            <div class="btn-container">
                <a href="{{ $confirmUrl }}" class="btn">Confirm Email &amp; Activate Signup &rarr;</a>
            </div>

            <div class="feedback-box">
                💬 <strong>We'd Love Your Thoughts &amp; Feedback!</strong><br>
                What markets do you trade? What strategies are you focusing on? What tools would make your trading journal even better? 
                <br><br>
                Please feel free to <strong>reply directly to this email</strong> with your feedback, suggestions, or simply to say hello. Our team reads and replies to every message!
            </div>

            <div class="token-box">
                If the button above does not work, copy and paste this verification link into your web browser:<br>
                <a href="{{ $confirmUrl }}" style="color: #4f46e5;">{{ $confirmUrl }}</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} MyTrades Trading Journal. Built for disciplined, growing traders.
        </div>
    </div>
</body>
</html>
