@php
    $orange = '#F97316';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email Galeri Magang</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f5f7; font-family:Arial, Helvetica, sans-serif; color:#333333;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f5f7; padding:30px 12px;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="max-width:560px; background-color:#ffffff; border-radius:12px; overflow:hidden;">

                    {{-- HEADER --}}
                    <tr>
                        <td align="center" style="padding:32px 24px 22px;">
                            <h1 style="margin:0; font-size:23px; font-weight:800; letter-spacing:1px; color:{{ $orange }};">
                                Galeri Magang
                            </h1>

                            <p style="margin:7px 0 0; font-size:12px; color:#888888; letter-spacing:0.5px;">
                                PT Equityworld Futures
                            </p>
                        </td>
                    </tr>

                    {{-- GARIS --}}
                    <tr>
                        <td style="padding:0 28px;">
                            <div style="height:1px; background-color:#eeeeee;"></div>
                        </td>
                    </tr>

                    {{-- ISI --}}
                    <tr>
                        <td style="padding:30px 28px 10px;">

                            <h2 style="margin:0 0 22px; font-size:22px; line-height:1.4; color:#252525; text-align:center;">
                                Verifikasi Email Kamu
                            </h2>

                            <p style="margin:0 0 16px; font-size:14px; line-height:1.8;">
                                Halo, {{ $name }}!
                            </p>

                            <p style="margin:0 0 16px; font-size:14px; line-height:1.8; color:#555555;">
                                Terima kasih telah mendaftarkan akun di portal rekrutmen PT Equityworld Futures.
                            </p>

                            <p style="margin:0 0 24px; font-size:14px; line-height:1.8; color:#555555;">
                                Untuk melanjutkan proses pendaftaran, silakan verifikasi alamat email kamu dengan menekan tombol di bawah ini.
                            </p>

                            {{-- TOMBOL VERIFIKASI --}}
                            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 26px;">
                                <tr>
                                    <td align="center" bgcolor="{{ $orange }}" style="border-radius:7px;">
                                        <a href="{{ $url }}"
                                            style="display:inline-block; padding:15px 28px; font-size:13px; font-weight:bold; letter-spacing:0.4px; color:#ffffff; text-decoration:none; border-radius:7px;">
                                            VERIFIKASI EMAIL
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- LINK ALTERNATIF --}}
                            <p style="margin:0 0 8px; font-size:12px; line-height:1.7; color:#777777;">
                                Jika tombol di atas tidak dapat dibuka, kamu dapat mengakses tautan berikut:
                            </p>

                            <p style="margin:0 0 24px; font-size:12px; line-height:1.7; word-break:break-all;">
                                <a href="{{ $url }}" style="color:{{ $orange }}; text-decoration:underline;">
                                    {{ $url }}
                                </a>
                            </p>

                            <p style="margin:0 0 16px; font-size:12px; line-height:1.8; color:#888888;">
                                Link verifikasi ini memiliki batas waktu penggunaan. Silakan lakukan verifikasi sebelum link kedaluwarsa.
                            </p>

                            <p style="margin:0 0 26px; font-size:12px; line-height:1.8; color:#888888;">
                                Jika kamu tidak merasa melakukan pendaftaran ini, abaikan email ini.
                            </p>

                            <p style="margin:0; font-size:13px; line-height:1.8; color:#555555;">
                                Salam,<br>
                                <strong style="color:#333333;">Tim Recruitment</strong><br>
                                PT Equityworld Futures
                            </p>

                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td align="center" style="padding:24px 20px 28px;">
                            <div style="height:1px; background-color:#eeeeee; margin-bottom:20px;"></div>

                            <p style="margin:0; font-size:11px; line-height:1.7; color:#aaaaaa;">
                                Email ini dikirim secara otomatis oleh Galeri Magang.<br>
                                Mohon tidak membalas email ini.
                            </p>

                            <p style="margin:10px 0 0; font-size:10px; color:#bbbbbb;">
                                &copy; {{ date('Y') }} Galeri Magang · PT Equityworld Futures
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>