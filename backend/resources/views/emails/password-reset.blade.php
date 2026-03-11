<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #F5F6FA;
            color: #111;
        }

        .wrapper {
            padding: 40px 16px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .header {
            background-color: #FDF22F;
            padding: 48px 40px;
            text-align: center;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background-color: #000000;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 28px;
            line-height: 64px;
        }

        .header h1 {
            color: #000000;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .header p {
            color: rgba(0, 0, 0, 0.6);
            font-size: 14px;
            font-weight: 500;
        }

        /* Content */
        .content {
            background-color: #ffffff;
            padding: 48px 40px;
        }

        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #000;
            margin-bottom: 16px;
        }

        .body-text {
            font-size: 15px;
            color: #4B5563;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .body-text strong {
            color: #000;
            font-weight: 800;
        }

        /* CTA Button */
        .btn-wrapper {
            text-align: center;
            margin: 36px 0;
        }

        .btn {
            display: inline-block;
            background-color: #FDF22F;
            color: #000000 !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 18px 40px;
            border-radius: 50px;
            border: 2px solid #000000;
        }

        /* Copy link box */
        .copy-box {
            background-color: #F5F6FA;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }

        .copy-label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #9CA3AF;
            margin-bottom: 8px;
        }

        .copy-url {
            font-size: 12px;
            color: #000;
            word-break: break-all;
            font-weight: 600;
            line-height: 1.6;
        }

        /* Warning box */
        .warning-box {
            background-color: #FFFBEB;
            border: 1px solid #FDF22F;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }

        .warning-box p {
            font-size: 13px;
            color: #92400E;
            line-height: 1.6;
        }

        .warning-box strong {
            color: #78350F;
        }

        /* Expiry note */
        .expiry {
            font-size: 13px;
            color: #9CA3AF;
            text-align: center;
            margin-top: 8px;
        }

        /* Footer */
        .footer {
            background-color: #000000;
            padding: 32px 40px;
            text-align: center;
        }

        .footer-logo {
            font-size: 16px;
            font-weight: 900;
            color: #FDF22F;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .footer-sub {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.35);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 16px;
        }

        .footer-note {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.25);
            line-height: 1.6;
        }

        .footer-year {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">

            <!-- Logo Header (Workable styled) -->
            <div style="background-color: #ffffff; padding: 32px 40px 16px; text-align: center;">
                <div style="font-family: Arial, sans-serif; display: inline-block;">
                    <span
                        style="display: inline-block; background-color: #FDF22F; color: #000000; padding: 4px 10px; border-radius: 6px; font-weight: 900; font-size: 20px; letter-spacing: -1px; margin-right: 6px; border: 2px solid #000000; box-shadow: 2px 2px 0px #000000; vertical-align: middle;">D</span>
                    <span
                        style="color: #000000; font-weight: 900; font-size: 22px; letter-spacing: -1px; vertical-align: middle;">DROGA
                        GROUP</span>
                    <span
                        style="color: #666666; font-weight: 300; font-size: 22px; letter-spacing: -0.5px; vertical-align: middle; margin-left: 6px;">HIRING
                        HUB</span>
                </div>
            </div>

            <!-- Header -->
            <div class="header">
                <div class="header-icon">🔐</div>
                <h1>Password Reset Request</h1>
                <p>We received a request to reset your password</p>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="greeting">Hello, {{ $applicantName }},</p>

                <p class="body-text">
                    We received a request to reset the password for your <strong>Droga Pharma</strong> applicant
                    account.
                    Click the button below to set a new password. This link is valid for <strong>60 minutes</strong>.
                </p>

                <!-- CTA -->
                <div class="btn-wrapper">
                    <a href="{{ $resetUrl }}" class="btn">Reset My Password →</a>
                </div>

                <!-- Copy link -->
                <div class="copy-box">
                    <div class="copy-label">Or copy and paste this link into your browser:</div>
                    <div class="copy-url">{{ $resetUrl }}</div>
                </div>

                <!-- Warning -->
                <div class="warning-box">
                    <p>⚠️ <strong>Didn't request this?</strong> If you didn't request a password reset, you can safely
                        ignore this email. Your password will remain unchanged. No action is required on your part.</p>
                </div>

                <p class="expiry">For your security, this link expires in 60 minutes and can only be used once.</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-logo">Droga Pharma</div>
                <div class="footer-sub">Talent Acquisition Platform</div>
                <div class="footer-note">This email was sent because a password reset was requested for your account.
                </div>
                <div class="footer-year">&copy; {{ date('Y') }} All rights reserved.</div>
            </div>

        </div>
    </div>
</body>

</html>