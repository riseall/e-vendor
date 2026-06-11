<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Konfirmasi Permohonan Vendor</title>
</head>

<body style="margin:0; padding:0; background:#ffffff; font-family:Arial, Helvetica, sans-serif; color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;">
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
                        <td align="center" style="font-size:26px; font-weight:700; color:#1f2933; padding-bottom:28px;">
                            E-VENDOR
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:20px; color:#1f2933; padding-bottom:14px;">
                            Permohonan Vendor Diterima
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#5f4b4b; padding:0 12px 20px;">
                            Yth. {{ optional($application->user)->name ?? 'Vendor' }}, permohonan registrasi vendor
                            Anda telah berhasil dikirim dan menunggu verifikasi tim pengadaan.
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
                                    <td style="padding:4px 10px; color:#6b7280;">Status</td>
                                    <td style="padding:4px 10px; font-weight:700;">Submitted</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Tanggal</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($application->submitted_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ route('registrasi.tracking', $applicationNumber) }}"
                                style="display:inline-block; background:#5d9cec; color:#ffffff; text-decoration:none; font-size:13px; line-height:18px; padding:6px 14px; border-radius:3px;">
                                TRACKING
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#4b5563;">
                            E-Vendor &copy;{{ date('Y') }} PT. Phapros, Tbk. - All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
