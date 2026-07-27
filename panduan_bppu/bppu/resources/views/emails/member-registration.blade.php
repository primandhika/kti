<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Kantin IKIP Siliwangi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #996600;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #996600;
            padding: 15px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background-color: #996600;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #7a5100;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Selamat Datang di Kantin IKIP Siliwangi!</h1>
        </div>

        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>

            <p>Terima kasih telah mendaftar sebagai member Kantin BPPU IKIP Siliwangi!</p>

            <div class="info-box">
                <p><strong>Informasi Akun Anda:</strong></p>
                <p>NIM: <strong>{{ $user->member_code }}</strong></p>
                <p>Email: <strong>{{ $user->email }}</strong></p>
            </div>

            <p>Untuk mengaktifkan akun Anda, silakan klik tombol verifikasi di bawah ini:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email Saya</a>
            </div>

            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                Atau copy dan paste link berikut ke browser Anda:<br>
                <a href="{{ $verificationUrl }}" style="color: #996600; word-break: break-all;">{{ $verificationUrl }}</a>
            </p>

            <p style="margin-top: 20px; font-size: 13px; color: #999;">
                <strong>Catatan:</strong> Link verifikasi ini berlaku selama 24 jam. Jika link sudah kadaluarsa, silakan hubungi admin.
            </p>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem Kantin BPPU IKIP Siliwangi.</p>
            <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
            <p>&copy; 2026 BPPU IKIP Siliwangi. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
