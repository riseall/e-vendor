<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Hasil Verifikasi Kuesioner Audit</title>
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
                    
                    @php
                        $isApproved = $audit->status === \App\Models\VendorAudit::STATUS_COMPLETED;
                        $isRejected = $audit->status === \App\Models\VendorAudit::STATUS_REJECTED;
                        $color = $isApproved ? '#15803d' : ($isRejected ? '#b91c1c' : '#b45309');
                        $statusText = $isApproved ? 'Disetujui' : ($isRejected ? 'Ditolak' : 'Perlu Revisi');
                    @endphp

                    <tr>
                        <td align="center" style="font-size:20px; color:{{ $color }}; padding-bottom:14px;">
                            Verifikasi Kuesioner - {{ $statusText }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:20px;">
                            Halo, <strong>{{ $application->general->nama_perusahaan ?? 'Vendor' }}</strong>.<br>
                            Berikut adalah hasil verifikasi kuesioner Anda untuk nomor permohonan <strong>{{ $applicationNumber }}</strong>.
                        </td>
                    </tr>
                    
                    @if($audit->summary || ($audit->questionnaire_revision_notes && is_array($audit->questionnaire_revision_notes)))
                    <tr>
                        <td style="padding-bottom:20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f3f4f6; border-radius:6px; padding:16px;">
                                <tr>
                                    <td style="font-size:13px; color:#374151;">
                                        <strong>Detail Verifikasi:</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px; color:#6b7280; padding-top:8px;">
                                        Status: <strong style="color:{{ $color }};">{{ $statusText }}</strong>
                                        
                                        @if($audit->summary)
                                            <br><br><strong>Catatan QA:</strong><br>
                                            {!! nl2br(e($audit->summary)) !!}
                                        @endif

                                        @if($audit->questionnaire_revision_notes && is_array($audit->questionnaire_revision_notes))
                                            <br><br><strong>Poin Revisi:</strong>
                                            <ul style="margin-top: 4px; padding-left: 20px;">
                                                @foreach($audit->questionnaire_revision_notes as $note)
                                                    <li>{{ $note }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td align="center"
                            style="font-size:14px; line-height:20px; color:#4b5563; padding-bottom:16px;">
                            Silakan login ke sistem e-Vendor untuk melihat detail lebih lanjut dan merespons.
                        </td>
                    </tr>
                    
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ url('/login') }}"
                                style="display:inline-block; background:#0284c7; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                LOGIN E-VENDOR
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
