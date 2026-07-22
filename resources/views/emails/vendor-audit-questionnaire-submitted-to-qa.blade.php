<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notifikasi Pengiriman Kuesioner Vendor</title>
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
                        <td align="center" style="font-size:20px; color:#0284c7; padding-bottom:14px;">
                            {{ $isRevision ? 'Revisi Kuesioner Dikirim' : 'Kuesioner Baru Dikirim' }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:20px;">
                            Halo Tim QA & Super Admin,<br><br>
                            Vendor <strong>{{ $application->general->nama_perusahaan ?? '-' }}</strong> 
                            telah mengirimkan {{ $isRevision ? 'revisi kuesioner' : 'jawaban kuesioner' }} 
                            untuk nomor permohonan <strong>{{ $applicationNumber }}</strong>.
                        </td>
                    </tr>
                    
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ url('/qa/audit/' . $audit->id) }}"
                                style="display:inline-block; background:#0284c7; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                REVIEW KUESIONER
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
