 <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SEKAR</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            margin-bottom: 20px;
        }
        .greeting h2 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 18px;
        }
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .reset-button {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button a {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        .reset-button a:hover {
            background-color: #2563eb;
        }
        .security-notice {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .security-notice h3 {
            margin: 0 0 8px 0;
            color: #92400e;
            font-size: 14px;
            font-weight: 600;
        }
        .security-notice p {
            margin: 0;
            color: #92400e;
            font-size: 13px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        .manual-link {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 10px 0;
        }
        @media (max-width: 600px) {
            .content {
                padding: 20px 15px;
            }
            .reset-button a {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>SERIKAT KARYAWAN TELKOM</h1>
            <p>Sistem Informasi Keanggotaan</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                <h2>Reset Password Akun SEKAR</h2>
                <p>Halo <strong>{{ $user->name }}</strong>,</p>
                <p>Kami menerima permintaan untuk reset password akun SEKAR Anda dengan NIK: <strong>{{ $user->nik }}</strong></p>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p style="margin: 0; color: #1e40af; font-size: 14px;">
                    <strong>📧 Email:</strong> {{ $user->email }}<br>
                    <strong>⏰ Berlaku hingga:</strong> {{ $validUntil }}<br>
                    <strong>🔐 Token akan kadaluarsa dalam:</strong> 1 jam
                </p>
            </div>

            <!-- Reset Button -->
            <div class="reset-button">
                <a href="{{ $resetUrl }}">Reset Password Sekarang</a>
            </div>

            <!-- Manual Link -->
            <p style="color: #6b7280; font-size: 13px; text-align: center; margin-bottom: 10px;">
                Jika tombol di atas tidak berfungsi, salin dan buka link berikut di browser:
            </p>
            <div class="manual-link">
                {{ $resetUrl }}
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <h3>⚠️ Penting untuk Keamanan:</h3>
                <p>
                    • Link ini hanya berlaku selama <strong>1 jam</strong> sejak email ini dikirim<br>
                    • Jangan berikan link ini kepada orang lain<br>
                    • Anda akan diminta memasukkan password portal untuk konfirmasi<br>
                    • Jika Anda tidak meminta reset password, abaikan email ini
                </p>
            </div>

            <!-- Instructions -->
            <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 0 6px 6px 0;">
                <p style="margin: 0; color: #1e40af; font-size: 14px; font-weight: 600;">
                    📋 Langkah-langkah Reset Password:
                </p>
                <ol style="margin: 8px 0 0 0; color: #1e40af; font-size: 13px; padding-left: 20px;">
                    <li>Klik tombol "Reset Password Sekarang" di atas</li>
                    <li>Masukkan password portal Anda untuk validasi</li>
                    <li>Konfirmasi password baru</li>
                    <li>Login dengan password baru Anda</li>
                </ol>
            </div>

            <!-- Help Section -->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <p style="color: #6b7280; font-size: 13px; margin: 0;">
                    <strong>Butuh bantuan?</strong><br>
                    Jika Anda mengalami kesulitan, silakan hubungi administrator SEKAR di:
                </p>
                <p style="color: #3b82f6; font-size: 13px; margin: 5px 0 0 0;">
                    📧 admin@sekar.telkom.co.id<br>
                    📞 0800-1-SEKAR (73527)
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>SEKAR - Serikat Karyawan Telkom</strong><br>
                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.<br>
                Untuk bantuan, silakan hubungi <a href="mailto:admin@sekar.telkom.co.id">admin@sekar.telkom.co.id</a>
            </p>
            <p style="margin-top: 15px; font-size: 11px; color: #9ca3af;">
                © {{ date('Y') }} PT Telkom Indonesia. Semua hak dilindungi.<br>
                Dikirim pada {{ date('d F Y, H:i') }} WIB
            </p>
        </div>
    </div>
</body>
</html>