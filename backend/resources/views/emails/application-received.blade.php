<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #111;
            margin: 0;
            padding: 0;
            background-color: #F5F6FA;
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
            margin: 0;
        }

        .content {
            padding: 40px;
        }

        .intro {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .highlight {
            color: #000000;
            font-weight: 900;
            background-color: #FDF22F;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .company-name {
            font-weight: 900;
            color: #000000;
        }

        .signature {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #e0e0e0;
            font-size: 15px;
            color: #444;
        }

        .footer {
            background: #F5F6FA;
            padding: 24px 40px;
            text-align: center;
            font-size: 11px;
            color: #bbb;
            font-weight: 600;
            line-height: 1.8;
            text-transform: uppercase;
            letter-spacing: 1px;
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
            <h1>Application Received</h1>
        </div>
        <div class="content">
            <p class="intro">Hello <strong>{{ $applicant->name }}</strong>,</p>
            <p class="intro">Thank you for applying for the <span class="highlight">{{ $job->title }}</span> position at
                <span class="company-name">{{ $tenant->name }}</span>.
            </p>
            <p class="intro">We've successfully received your application. Our hiring team is currently reviewing your
                profile and qualifications.</p>
            <p class="intro">We will reach out to you via email or phone if your profile is shortlisted for the next
                steps.</p>
            <div class="signature">
                <p style="margin: 0;">Best regards,<br><span class="company-name">{{ $tenant->name }} – Hiring
                        Team</span></p>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $tenant->name }} &bull; Powered by Droga TAS
        </div>
    </div>
</body>

</html>