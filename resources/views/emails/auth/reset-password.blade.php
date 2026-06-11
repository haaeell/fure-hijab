<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password FURE</title>
</head>

<body style="margin:0;background:#F8FBF8;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F8FBF8;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:24px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#5F4A3A;padding:28px 32px;color:#ffffff;">
                            <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#D6C4B0;font-weight:700;">FURE</div>
                            <h1 style="margin:8px 0 0;font-size:26px;line-height:1.25;">Reset password akun</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.7;">Halo {{ $user->name }},</p>
                            <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#4b5563;">
                                Kami menerima permintaan untuk mengganti password akun FURE Anda. Klik tombol di bawah untuk membuat password baru.
                            </p>
                            <p style="margin:0 0 26px;text-align:center;">
                                <a href="{{ $url }}" style="display:inline-block;background:#A78B6F;color:#ffffff;text-decoration:none;font-weight:700;padding:14px 24px;border-radius:14px;">
                                    Reset Password
                                </a>
                            </p>
                            <p style="margin:0 0 14px;font-size:13px;line-height:1.7;color:#6b7280;">
                                Link ini berlaku selama {{ $expiresIn }} menit. Jika Anda tidak meminta reset password, abaikan email ini.
                            </p>
                            <p style="margin:18px 0 0;font-size:12px;line-height:1.6;color:#9ca3af;">
                                Jika tombol tidak dapat dibuka, salin link berikut ke browser:<br>
                                <span style="word-break:break-all;color:#5F4A3A;">{{ $url }}</span>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px;background:#F1F8E9;color:#5F4A3A;font-size:12px;text-align:center;">
                            Email otomatis dari FURE. Mohon tidak membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
