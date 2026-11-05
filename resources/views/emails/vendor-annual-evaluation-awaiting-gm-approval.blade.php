<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Permohonan Pengesahan Evaluasi Tahunan Vendor: {{ optional($annual->vendor)->name ?: '-' }}</title>
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
                            Permohonan Pengesahan Evaluasi Tahunan Vendor
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:14px; line-height:22px; color:#4b5563; padding:0 12px 20px;">
                            Yth. <strong>General Manager Pengadaan</strong>,<br>
                            Evaluasi tahunan untuk vendor di bawah ini telah selesai diverifikasi oleh <strong>Manager Pengadaan</strong> dan saat ini menunggu pengesahan (final approval) dari Bapak/Ibu.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"
                                style="margin:0 auto; font-size:14px; color:#384150; text-align:left; width:100%; max-width:480px; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb; width:45%;">Nama Vendor</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">
                                        {{ optional($annual->vendor)->name ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Supplier Code (QAD)</td>
                                    <td style="padding:10px 14px; font-weight:600; border-bottom:1px solid #e5e7eb;">
                                        {{ optional($annual->vendor)->qad_supplier_code ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Tahun Evaluasi</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">
                                        Tahun {{ $annual->year }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Skor Akhir & Kategori</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">
                                        {{ number_format($annual->final_score, 2) }}
                                        @if ($annual->category === 'BAIK')
                                            <span style="display:inline-block; margin-left:6px; padding:2px 8px; background:#dcfce7; color:#15803d; border-radius:4px; font-size:12px;">BAIK</span>
                                        @elseif ($annual->category === 'CUKUP')
                                            <span style="display:inline-block; margin-left:6px; padding:2px 8px; background:#fef3c7; color:#b45309; border-radius:4px; font-size:12px;">CUKUP</span>
                                        @else
                                            <span style="display:inline-block; margin-left:6px; padding:2px 8px; background:#fee2e2; color:#b91c1c; border-radius:4px; font-size:12px;">KURANG</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Diverifikasi Oleh</td>
                                    <td style="padding:10px 14px; font-weight:600; color:#1f2937; border-bottom:1px solid #e5e7eb;">
                                        {{ optional($annual->managerApprover)->name ?: (Auth::user() ? Auth::user()->name : 'Manager Pengadaan') }}
                                        <div style="font-size:12px; color:#6b7280; font-weight:400; margin-top:2px;">
                                            {{ $annual->manager_approved_at ? $annual->manager_approved_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                </tr>
                                @if (!empty($annual->manager_notes))
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Catatan Manager</td>
                                    <td style="padding:10px 14px; color:#1f2937; border-bottom:1px solid #e5e7eb;">
                                        {{ $annual->manager_notes }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280;">Tahapan Alur</td>
                                    <td style="padding:10px 14px; font-weight:700; color:#15803d;">
                                        Tahap 2: Pengesahan GM Pengadaan
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 32px;">
                            <a href="{{ route('admin.evaluasi.index', ['year' => $annual->year, 'tab' => 'annual']) }}"
                                style="display:inline-block; background:#15803d; color:#ffffff; text-decoration:none; font-size:13px; font-weight:700; line-height:18px; padding:12px 24px; border-radius:6px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                                Buka & Sahkan Evaluasi Vendor
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

