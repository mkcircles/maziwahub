<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invitation to join YieldTech</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            width: 100% !important;
            height: 100% !important;
        }

        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }

        .container {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px border #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .header {
            background-color: #111c43;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }

        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }

        .content p {
            margin: 0 0 20px;
            font-size: 16px;
            color: #334155;
        }

        .cta-container {
            text-align: center;
            margin: 30px 0;
        }

        .btn-primary {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            transition: background-color 0.2s;
        }

        .notes-box {
            background-color: #f1f5f9;
            border-left: 4px solid #4f46e5;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 0 8px 8px 0;
            font-style: italic;
        }

        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }

        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>YieldTech</h1>
            </div>
            <div class="content">
                <p>Hello {{ $invitation->name ?? 'there' }},</p>

                <p>
                    You have been invited by <strong>{{ $invitation->invitedBy?->name ?? 'a administrator' }}</strong>
                    to join
                    <strong>{{ $invitation->partner?->name ?? 'the partner organisation' }}</strong> on the Yield Tech
                    platform.
                </p>

                <p>
                    Your role is assigned as:
                    <strong>{{ $invitation->role === 'partner_admin' ? 'Partner Admin' : 'Partner Agent' }}</strong>.
                </p>

                @if($invitation->notes)
                    <div class="notes-box">
                        "{{ $invitation->notes }}"
                    </div>
                @endif

                <p>Please click the button below to accept your invitation, fill in your profile details and set up your
                    password.</p>

                <div class="cta-container">
                    <a href="{{ $acceptUrl }}" class="btn-primary" target="_blank">Accept Invitation</a>
                </div>

                <p>If the button doesn't work, you can copy and paste the link below into your web browser:</p>
                <p style="word-break: break-all; font-size: 14px; color: #64748b;">
                    <a href="{{ $acceptUrl }}" target="_blank">{{ $acceptUrl }}</a>
                </p>

                <p>This invitation link will expire on {{ $invitation->expires_at->format('F j, Y, g:i a') }}.</p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} YieldTech. All rights reserved.<br>
                If you did not expect this invitation, you can safely ignore this email.
            </div>
        </div>
    </div>
</body>

</html>