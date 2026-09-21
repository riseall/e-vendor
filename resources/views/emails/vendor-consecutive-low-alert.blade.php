<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Peringatan Evaluasi Vendor 2 Tahun Berturut-turut KURANG</title>
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
                        <td align="center" style="font-size:24px; font-weight:700; padding-bottom:18px; color:#991b1b;">PERINGATAN KHUSUS KINERJA VENDOR</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:16px; padding-bottom:14px; color:#b91c1c; font-weight:700;">
                            Vendor Berkategori KURANG Selama 2 Tahun Berturut-turut
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; line-height:22px; color:#4b5563; padding-bottom:16px;">
                            Yth. <strong>Tim Pengadaan & Quality Assurance (QA)</strong> PT Phapros Tbk,
                            <br><br>
                            Sistem mendeteksi bahwa vendor berikut memperoleh predikat evaluasi <strong>KURANG selama 2 tahun berturut-turut</strong> (Tahun {{ $annual->year - 1 }} dan Tahun {{ $annual->year }}).
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 20px; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; margin-bottom:20px;">
                            <table width="100%" cellpadding="4" cellspacing="0" style="font-size:14px;">
                                <tr>
                                    <td width="38%" style="color:#6b7280;">Nama Vendor:</td>
                                    <td style="font-weight:700; color:#1f2937;">{{ optional($annual->vendor)->name ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Supplier Code:</td>
                                    <td style="font-weight:600; color:#1f2937;">{{ optional($annual->vendor)->qad_supplier_code ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Skor Evaluasi {{ $annual->year }}:</td>
                                    <td style="font-weight:700; color:#dc2626;">{{ number_format($annual->final_score, 2) }} (KURANG)</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Status Saat Ini:</td>
                                    <td style="font-weight:600; color:#1f2937;">{{ strtoupper(str_replace('_', ' ', $annual->decision_status ?? 'none')) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; line-height:22px; color:#4b5563; padding-top:16px;">
                            Sesuai SOP Evaluasi Kinerja Vendor, diperlukan koordinasi antara Tim Pengadaan dan QA untuk memutuskan salah satu tindakan berikut:
                            <ul style="margin:8px 0; padding-left:20px;">
                                <li><strong>Terminated</strong> (Pemutusan Kemitraan)</li>
                                <li><strong>Suspended</strong> (Pembekuan Sementara Kemitraan)</li>
                                <li><strong>Qualified dengan Catatan</strong> (Pemantauan Ketat & Rencana Perbaikan Khusus)</li>
                                <li><strong>Proses Rekualifikasi</strong> (Audit Ulang Kualifikasi)</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:24px 0 20px;">
                            <a href="{{ route('admin.evaluasi.index', ['year' => $annual->year, 'tab' => 'annual']) }}"
                                style="display:inline-block; background:#dc2626; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 24px; border-radius:6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                BUKA MENU PENGESAHAN & EKSEKUSI TINDAKAN
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#9ca3af; border-top:1px solid #e5e7eb; padding-top:16px;">
                            Email ini dikirimkan otomatis oleh Portal E-Vendor PT Phapros Tbk kepada Tim Pengadaan dan QA.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
