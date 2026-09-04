<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 540px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 36px 32px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h1 {
            font-size: 22px;
            color: #1e293b;
            margin: 0;
            font-weight: 700;
        }
        .content {
            font-size: 15px;
            line-height: 1.6;
            color: #475569;
        }
        .otp-box {
            margin: 28px 0;
            text-align: center;
        }
        .otp-code {
            display: inline-block;
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 6px;
            color: #2563eb;
            background-color: #eff6ff;
            border: 2px dashed #93c5fd;
            border-radius: 8px;
            padding: 12px 28px;
        }
        .footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Verification</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset the password for your account. Please use the One-Time Password (OTP) below to proceed:</p>
            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
            </div>
            <p>This OTP is valid for <strong>{{ $expiresInMinutes }} minutes</strong>. For your security, do not share this code with anyone.</p>
            <p>If you did not request a password reset, you can safely ignore this email. Your account remains secure.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Tour & Trip') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
