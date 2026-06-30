<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Revisi Vendor Telah Dikirim</title>
</head>

<body style="margin:0; padding:0; background:#ffffff; font-family:Arial, Helvetica, sans-serif; color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:38px 16px 28px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;">
                    <tr>
                        <td align="center" style="padding-bottom:22px;">
                            <img src="https://app.phapros.co.id/peha_id/gbricon/logo1.png" alt="Phapros" width="190"
                                style="display:block; border:0; outline:none; text-decoration:none; max-width:190px;">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:26px; font-weight:700; padding-bottom:28px;">
                            E-VENDOR
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:20px; padding-bottom:14px;">
                            Revisi Vendor Telah Dikirim
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding:0 12px 20px;">
                            Vendor telah memperbaiki dan mengirim ulang data permohonan. Silakan lakukan
                            pemeriksaan kembali terhadap bagian yang sebelumnya diminta untuk direvisi.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"
                                style="margin:0 auto; font-size:14px; color:#384150; text-align:left;">
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Nomor</td>
                                    <td style="padding:4px 10px; font-weight:700;">{{ $applicationNumber }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Perusahaan</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($application->general)->nama_perusahaan ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Revisi ke</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ $application->revision_count ?: 1 }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Dikirim ulang</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($application->revision_submitted_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Deadline baru</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($deadline)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ route('verifikasi.show', $application) }}"
                                style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                PERIKSA REVISI
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
