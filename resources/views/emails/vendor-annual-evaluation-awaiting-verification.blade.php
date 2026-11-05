<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Permohonan Verifikasi Evaluasi Tahunan Vendor (Tahun {{ $year }})</title>
</head>

<body style="margin:0; padding:0; background:#ffffff; font-family:Arial, Helvetica, sans-serif; color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;">
        <tr>
            <td align="center" style="padding:38px 16px 28px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;">
                    <tr>
                        <td align="center" style="padding-bottom:22px;">
                            <img src="https://app.phapros.co.id/peha_id/gbricon/logo1.png" alt="Phapros" width="190"
                                style="display:block; border:0; outline:none; text-decoration:none; max-width:190px;">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:24px; font-weight:700; color:#1f2933; padding-bottom:24px;">
                            E-VENDOR
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:18px; font-weight:700; color:#005db6; padding-bottom:12px;">
                            Verifikasi Evaluasi Tahunan Vendor (Tahun {{ $year }})
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:14px; line-height:22px; color:#4b5563; padding:0 12px 20px;">
                            Yth. <strong>Manager Pengadaan</strong>,<br>
                            Kalkulasi evaluasi tahunan untuk <strong>Tahun {{ $year }}</strong> telah berhasil digenerate dan siap untuk diverifikasi.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"
                                style="margin:0 auto; font-size:14px; color:#384150; text-align:left; width:100%; max-width:480px; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb; width:45%;">Tahun Evaluasi</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">Tahun {{ $year }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Total Vendor Terhitung</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">{{ $vendorCount }} Vendor</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Tahapan Alur</td>
                                    <td style="padding:10px 14px; font-weight:700; color:#005db6; border-bottom:1px solid #e5e7eb;">
                                        Tahap 1: Verifikasi Manager Pengadaan
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280;">Waktu Kalkulasi</td>
                                    <td style="padding:10px 14px; font-weight:600; color:#1f2937;">
                                        {{ now()->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 32px;">
                            <a href="{{ route('admin.evaluasi.index', ['year' => $year, 'tab' => 'annual']) }}"
                                style="display:inline-block; background:#005db6; color:#ffffff; text-decoration:none; font-size:13px; font-weight:700; line-height:18px; padding:12px 24px; border-radius:6px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                                Tinjau & Verifikasi Evaluasi Tahunan
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#6b7280; border-top:1px solid #e5e7eb; padding-top:16px;">
                            PT Phapros Tbk &copy; {{ date('Y') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>

