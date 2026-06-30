<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Hasil Risk Assessment - On-Desk Audit</title>
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
                        <td align="center" style="font-size:20px; color:#f59e0b; padding-bottom:14px;">
                            Risk Assessment - On-Desk Audit
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:20px;">
                            Permohonan <strong>{{ $applicationNumber }}</strong> milik
                            <strong>{{ optional($application->general)->nama_perusahaan ?? '-' }}</strong>
                            telah melalui proses Risk Assessment dengan hasil: <strong>MEDIUM RISK</strong>.
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:14px; line-height:20px; color:#4b5563; padding-bottom:16px;">
                            Berdasarkan penilaian risiko, permohonan Anda memerlukan audit lebih lanjut melalui
                            proses on-desk audit. Tim audit kami akan menghubungi Anda untuk mengumpulkan informasi
                            dan dokumentasi tambahan melalui aplikasi E-Vendor.
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f3f4f6; border-radius:6px; padding:16px;">
                                <tr>
                                    <td style="font-size:13px; color:#374151;">
                                        <strong>Detail Assessment:</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px; color:#6b7280; padding-top:8px;">
                                        Total Score: <strong>{{ $qualification->total_score ?? '-' }}</strong><br />
                                        Risk Level: <strong>MEDIUM</strong><br />
                                        Audit Type: <strong>On-Desk Audit</strong><br />
                                        Assessment Date: <strong>{{ now()->format('d M Y') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ route('registrasi.tracking', $applicationNumber) }}"
                                style="display:inline-block; background:#f59e0b; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
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
