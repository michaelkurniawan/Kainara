<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update on Your Kainara Artisan Application</title>
    <style>
        /* CSS sederhana untuk email agar terlihat rapi di berbagai email client */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f4;
            padding: 20px 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #dddddd;
        }
        .email-header h1 {
            font-size: 24px;
            color: #333333;
            margin: 0;
        }
        .email-body {
            padding: 20px 0;
        }
        .email-body p {
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .email-footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dddddd;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                {{-- Anda bisa menaruh logo di sini jika mau --}}
                {{-- <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Kainara Logo" style="max-width: 150px;"> --}}
                <h1>Application Update</h1>
            </div>
            <div class="email-body">
                {{-- Variabel $profile akan otomatis tersedia dari Mailable --}}
                <p>Hello, {{ $profile->name }},</p>

                <p>Thank you for your interest and for taking the time to apply to become a part of the Kainara artisan community. We have carefully reviewed your submission.</p>

                <p>We regret to inform you that we are unable to move forward with your application at this time. We receive many applications and the selection process is highly competitive, often based on our current curation needs and brand alignment.</p>

                <p>This decision does not reflect on the quality of your craft, and we sincerely appreciate you sharing your work with us. We encourage you to re-apply in the future as our needs evolve.</p>

                <p>We wish you all the best in your future endeavors.</p>

                <p>
                    Warm regards,<br>
                    The Kainara Team
                </p>
            </div>
            <div class="email-footer">
                <p>© {{ date('Y') }} Kainara. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>