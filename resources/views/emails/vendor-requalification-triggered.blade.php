<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Permintaan Rekualifikasi Vendor</title>
</head>

<body style="margin:0; padding:0; background:#ffffff; font-family:Arial, Helvetica, sans-serif; color:#1f2933;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:38px 16px 28px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
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
                        <td align="center" style="font-size:20px; padding-bottom:14px; color:#b45309; font-weight:700;">
                            Permintaan Rekualifikasi / Evaluasi Ulang Vendor
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:18px;">
                            Yth. {{ optional($application->general)->nama_perusahaan ?: (optional($application->user)->name ?: 'Vendor') }},
                            Tim Pengadaan / QA telah memicu permohonan <strong>Rekualifikasi (Evaluasi Ulang Data Vendor)</strong> untuk nomor permohonan <strong>{{ $applicationNumber }}</strong>.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; line-height:21px; padding:16px 20px; background:#fff8e6; border-left:4px solid #f59e0b; border-radius:4px;">
                            <strong style="color:#92400e;">Alasan / Pemicu Rekualifikasi:</strong>
                            <div style="font-size:15px; font-weight:600; color:#78350f; margin-top:6px;">
                                {{ $reasonLabel }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; line-height:22px; color:#4b5563; padding-top:20px;">
                            Silakan masuk ke portal E-Vendor dan perbarui data profil serta dokumen legalitas Anda sesuai ketentuan.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 0 34px;">
                            <a href="{{ route('registrasi.index', ['edit' => 1]) }}"
                                style="display:inline-block; background:#b45309; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 24px; border-radius:6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                PERBARUI DATA PROFIL (REKUALIFIKASI)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#6b7280; border-top:1px solid #e5e7eb; padding-top:18px;">
                            Email ini dikirimkan secara otomatis oleh Sistem E-Vendor Phapros. Harap tidak membalas email ini secara langsung.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
