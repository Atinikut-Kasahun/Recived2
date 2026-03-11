<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Verification Code</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #F5F6FA;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .wrapper {
            max-width: 520px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
        }

        .header {
            background: #FDF22F;
            padding: 36px 40px;
            text-align: center;
        }

        .header h1 {
            color: #000000;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .header p {
            color: rgba(0, 0, 0, 0.5);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 4px;
        }

        .body {
            padding: 40px;
        }

        .intro {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .otp-block {
            background: #F5F6FA;
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            margin-bottom: 32px;
            border: 2px dashed #e0e0e0;
        }

        .otp-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #aaa;
            margin-bottom: 12px;
        }

        .otp-code {
            font-size: 48px;
            font-weight: 900;
            letter-spacing: 12px;
            color: #000000;
            font-variant-numeric: tabular-nums;
        }

        .otp-expiry {
            font-size: 12px;
            color: #aaa;
            font-weight: 600;
            margin-top: 12px;
        }

        .warning {
            background: #fffbea;
            border: 1px solid #FDF22F;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 13px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .warning strong {
            color: #000;
        }

        .footer {
            background: #F5F6FA;
            padding: 24px 40px;
            text-align: center;
            font-size: 11px;
            color: #bbb;
            font-weight: 600;
            line-height: 1.8;
        }
    </style>
</head>

<body>
    <div class="wrapper">
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
        <div class="header">
            <h1>Droga Pharma</h1>
            <p>Talent Acquisition Portal</p>
        </div>
        <div class="body">
            <p class="intro">
                You requested a verification code to apply for a position at Droga Pharma.
                Enter the code below to confirm your email address and continue your application.
            </p>

            <div class="otp-block">
                <p class="otp-label">Your verification code</p>
                <p class="otp-code">{{ $otp }}</p>
                <p class="otp-expiry">⏱ Expires in 10 minutes</p>
            </div>

            <div class="warning">
                <strong>Didn't request this?</strong> You can safely ignore this email.
                Never share this code with anyone — Droga Pharma will never ask for it.
            </div>
        </div>
        <div class="footer">
            © {{ date('Y') }} Droga Pharma · Talent Acquisition<br />
            This is an automated message, please do not reply.
        </div>
    </div>
</body>

</html>