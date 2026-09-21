<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Evaluasi Kinerja Vendor Disahkan</title>
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
                        <td align="center" style="font-size:24px; font-weight:700; padding-bottom:20px; color:#1f2937;">E-VENDOR PHAPROS</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:18px; padding-bottom:16px; color:#15803d; font-weight:700;">
                            Laporan Evaluasi Tahunan Vendor Tahun {{ $annual->year }} Telah Disahkan
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; line-height:22px; color:#4b5563; padding-bottom:16px;">
                            Yth. <strong>{{ optional($annual->vendor)->name ?: 'Vendor' }}</strong>,
                            <br><br>
                            Kami informasikan bahwa proses evaluasi kinerja tahunan vendor untuk periode <strong>Tahun {{ $annual->year }}</strong> telah selesai diverifikasi oleh Manajer Pengadaan dan resmi disahkan oleh General Manager Pengadaan PT Phapros Tbk.
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 20px; background:#f3f4f6; border-radius:6px; margin-bottom:20px;">
                            <table width="100%" cellpadding="4" cellspacing="0" style="font-size:14px;">
                                <tr>
                                    <td width="40%" style="color:#6b7280;">Skor Akhir Tahunan:</td>
                                    <td style="font-weight:700; color:#1f2937; font-size:16px;">{{ number_format($annual->final_score, 2) }} / 100</td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Kategori Predikat:</td>
                                    <td>
                                        @if($annual->category === 'BAIK')
                                            <span style="display:inline-block; padding:3px 10px; background:#dcfce7; color:#15803d; font-weight:700; border-radius:4px;">BAIK</span>
                                        @elseif($annual->category === 'CUKUP')
                                            <span style="display:inline-block; padding:3px 10px; background:#fef3c7; color:#b45309; font-weight:700; border-radius:4px;">CUKUP</span>
                                        @else
                                            <span style="display:inline-block; padding:3px 10px; background:#fee2e2; color:#b91c1c; font-weight:700; border-radius:4px;">KURANG</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#6b7280;">Tanggal Disahkan:</td>
                                    <td style="color:#1f2937;">{{ $annual->gm_approved_at ? $annual->gm_approved_at->format('d F Y H:i') : now()->format('d F Y') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @if($annual->category === 'CUKUP' || $annual->category === 'KURANG')
                        <tr>
                            <td style="padding-top:16px;">
                                <div style="padding:14px 18px; background:#fffbeb; border-left:4px solid #f59e0b; border-radius:4px; font-size:13px; color:#92400e; line-height:20px;">
                                    <strong>Peringatan Khusus Kinerja:</strong><br>
                                    @if($annual->category === 'CUKUP')
                                        Kategori CUKUP memerlukan perhatian dan peningkatan mutu layanan/pengiriman untuk menjaga status kemitraan aktif Anda di PT Phapros Tbk.
                                    @else
                                        Kategori KURANG mewajibkan Anda segera menyusun dan mengirimkan Rencana Tindakan Perbaikan (Corrective Action Plan / CAP) kepada Tim Pengadaan PT Phapros Tbk.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td align="center" style="padding:28px 0 24px;">
                            <a href="{{ route('vendor.evaluasi.index', ['year' => $annual->year]) }}"
                                style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 26px; border-radius:6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                LIHAT DETAIL EVALUASI TAHUNAN
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#9ca3af; border-top:1px solid #e5e7eb; padding-top:16px;">
                            Email ini dikirimkan otomatis oleh Portal E-Vendor PT Phapros Tbk. Harap tidak membalas email ini secara langsung.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
