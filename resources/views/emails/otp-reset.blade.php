<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #246BCE 0%, #1E40AF 100%); padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 600; }
        .header p { margin: 8px 0 0 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #1F2937; margin: 0 0 20px 0; }
        .message { color: #6B7280; line-height: 1.6; margin: 0 0 30px 0; }
        .otp-box { background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); border: 2px dashed #246BCE; border-radius: 12px; padding: 30px; text-align: center; margin: 30px 0; }
        .otp-label { color: #6B7280; font-size: 14px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px; }
        .otp-code { font-size: 42px; font-weight: 700; color: #246BCE; letter-spacing: 10px; margin: 15px 0; font-family: "Courier New", monospace; }
        .otp-expiry { color: #DC2626; font-size: 14px; margin: 10px 0 0 0; font-weight: 500; }
        .instructions { background: #F9FAFB; border-left: 4px solid #246BCE; padding: 20px; margin: 30px 0; border-radius: 4px; }
        .instructions h3 { margin: 0 0 15px 0; color: #1F2937; font-size: 16px; }
        .instructions ol { margin: 0; padding-left: 20px; color: #6B7280; }
        .instructions li { margin: 8px 0; line-height: 1.5; }
        .warning { background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px 20px; margin: 20px 0; border-radius: 4px; }
        .warning strong { color: #92400E; display: block; margin-bottom: 5px; }
        .warning p { margin: 0; color: #78350F; font-size: 14px; }
        .footer { background: #F9FAFB; padding: 30px; text-align: center; color: #9CA3AF; font-size: 13px; }
        .footer p { margin: 5px 0; }
        .signature { margin: 30px 0 0 0; color: #6B7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 PosCare Nganjuk</h1>
            <p>Sistem Informasi Digital Posyandu</p>
        </div>
        <div class="content">
            <p class="greeting">Halo, <strong>Pengguna PosCare</strong>!</p>
            <p class="message">Kami menerima permintaan untuk mengatur ulang kata sandi akun PosCare Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password.</p>
            <div class="otp-box">
                <p class="otp-label">Kode OTP Anda</p>
                <div class="otp-code">{{ $otp }}</div>
                <p class="otp-expiry">⏱ Berlaku selama 10 menit</p>
            </div>
            <div class="instructions">
                <h3>📋 Cara Menggunakan Kode OTP:</h3>
                <ol>
                    <li>Kembali ke halaman login PosCare</li>
                    <li>Masukkan <strong>kode OTP</strong> di atas pada form yang tersedia</li>
                    <li>Buat <strong>kata sandi baru</strong> untuk akun Anda</li>
                    <li>Klik tombol <strong>"Atur Ulang Kata Sandi"</strong></li>
                </ol>
            </div>
            <div class="warning">
                <strong>⚠️ Perhatian Penting:</strong>
                <p>Jika Anda TIDAK meminta reset kata sandi, abaikan email ini. Kode OTP akan otomatis kedaluwarsa dalam 10 menit.</p>
            </div>
            <p class="signature">
                Terima kasih,<br>
                <strong>Tim PosCare Nganjuk</strong><br>
                <small style="color: #9CA3AF;">Admin Digital Posyandu</small>
            </p>
        </div>
        <div class="footer">
            <p><strong>Email Otomatis - Mohon Tidak Membalas</strong></p>
            <p>Email ini dikirim dari sistem PosCare Nganjuk</p>
            <p>&copy; {{ date('Y') }} PosCare Nganjuk. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
