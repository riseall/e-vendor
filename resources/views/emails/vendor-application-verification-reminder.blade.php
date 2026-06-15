<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reminder Verifikasi Vendor</title>
</head>
<body style="margin:0;padding:0;background:#fff;font-family:Arial,Helvetica,sans-serif;color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:38px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;">
                    <tr>
                        <td align="center" style="font-size:24px;font-weight:700;padding-bottom:22px;">E-VENDOR</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:19px;padding-bottom:14px;">
                            Reminder H-{{ $daysRemaining }} Verifikasi
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px;line-height:22px;color:#4b5563;padding-bottom:20px;">
                            Permohonan <strong>{{ $applicationNumber }}</strong> milik
                            <strong>{{ optional($application->general)->nama_perusahaan ?: optional($application->user)->name }}</strong>
                            belum selesai diverifikasi. Deadline verifikasi adalah
                            <strong>{{ $deadline->format('d/m/Y H:i') }}</strong>.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom:28px;">
                            <a href="{{ route('verifikasi.show', $application) }}"
                                style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:9px 18px;border-radius:3px;">
                                BUKA PERMOHONAN
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px;color:#6b7280;">
                            E-Vendor &copy;{{ date('Y') }} PT. Phapros, Tbk.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
