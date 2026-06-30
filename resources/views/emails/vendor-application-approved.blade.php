<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Permohonan Vendor Disetujui</title>
</head>

<body style="margin:0; padding:0; background:#ffffff; font-family:Arial, Helvetica, sans-serif; color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
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
                        <td align="center" style="font-size:26px; font-weight:700; padding-bottom:28px;">E-VENDOR</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:20px; color:#15803d; padding-bottom:14px;">
                            Permohonan Disetujui
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:20px;">
                            Selamat! Permohonan <strong>{{ $applicationNumber }}</strong> milik
                            <strong>{{ optional($application->general)->nama_perusahaan ?? '-' }}</strong>
                            telah <strong>DISETUJUI</strong> oleh sistem kami.
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:14px; line-height:20px; color:#4b5563; padding-bottom:16px;">
                            Anda telah berhasil melalui semua tahap proses verifikasi dan penilaian risiko.
                            Vendor Anda sekarang terdaftar dalam sistem E-Vendor kami dan dapat melakukan transaksi
                            dengan PT. Phapros, Tbk.
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f3f4f6; border-radius:6px; padding:16px;">
                                <tr>
                                    <td style="font-size:13px; color:#374151;">
                                        <strong>Detail Persetujuan:</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px; color:#6b7280; padding-top:8px;">
                                        Status: <strong>APPROVED</strong><br />
                                        Approval Date: <strong>{{ now()->format('d M Y') }}</strong><br />
                                        Valid Until:
                                        <strong>{{ optional($qualification->application)->valid_until ?? now()->addYears(5)->format('d M Y') }}</strong><br />
                                        Risk Level: <strong>{{ ucfirst($qualification->risk_level ?? 'N/A') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ route('registrasi.tracking', $applicationNumber) }}"
                                style="display:inline-block; background:#15803d; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                LIHAT STATUS
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#6b7280;">
                            E-Vendor &copy;{{ date('Y') }} PT. Phapros, Tbk. - All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
