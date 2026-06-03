<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            background-color: #ff914d;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            color: #ffffff;
            font-size: 22px;
        }
        .content {
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa; /* Light grey background */
            border-radius: 5px;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 10px;
        }
        @media screen and (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }
            .header {
                font-size: 20px;
                padding: 10px;
            }
            .content {
                padding: 15px;
            }
            .otp {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{ setting('site_name') }} - Password Reset
        </div>
        <div class="content">
            <p>Dear {{ $name }},</p>
            <p>Here is your one-time verification code to reset your password:</p>
            <div class="otp">{{ $otp }}</div>
            <p>This code is valid for a limited time. If you did not request a password reset, please ignore this email.</p>
            <p>Thank you,<br> The {{ setting('site_name') }} Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ setting('site_name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
