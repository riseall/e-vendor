@php
    $isRekualifikasi = $application->isRekualifikasi();
    $title = __('email_awaiting_manager_title');
    $subtitle = __('email_awaiting_manager_subtitle');
@endphp
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
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
                        <td align="center" style="font-size:26px; font-weight:700; color:#1f2933; padding-bottom:28px;">
                            E-VENDOR
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:20px; font-weight:700; color:#005db6; padding-bottom:14px;">
                            {{ $title }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding:0 12px 20px;">
                            {{ $subtitle }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"
                                style="margin:0 auto; font-size:14px; color:#384150; text-align:left; width:100%; max-width:480px; border:1px solid #e5e7eb; border-radius:6px; background:#f9fafb;">
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb; width:40%;">Tipe Permohonan</td>
                                    <td style="padding:10px 14px; border-bottom:1px solid #e5e7eb;">
                                        @if ($isRekualifikasi)
                                            <span
                                                style="display:inline-block; background:#fff7ed; color:#c2410c; border:1px solid #ffedd5; font-weight:700; font-size:12px; padding:2px 8px; border-radius:4px;">Rekualifikasi</span>
                                        @else
                                            <span
                                                style="display:inline-block; background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe; font-weight:700; font-size:12px; padding:2px 8px; border-radius:4px;">Vendor Baru</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Nomor Permohonan</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">{{ $applicationNumber }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Nama Perusahaan</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">
                                        {{ optional($application->general)->nama_perusahaan ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Email Vendor</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">
                                        {{ optional($application->user)->email ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">{{ __('email_verified_by_admin') }}</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">
                                        {{ $verifierName ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280; border-bottom:1px solid #e5e7eb;">Tanggal Verifikasi Admin</td>
                                    <td style="padding:10px 14px; font-weight:700; border-bottom:1px solid #e5e7eb;">
                                        {{ optional($application->verified_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px; color:#6b7280;">Batas Waktu (Deadline)</td>
                                    <td style="padding:10px 14px; font-weight:700; color:#dc2626;">
                                        {{ optional($deadline)->format('d/m/Y') ?? '-' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ route('verifikasi.show', $application->id) }}"
                                style="display:inline-block; background:#005db6; color:#ffffff; text-decoration:none; font-size:13px; font-weight:700; line-height:18px; padding:10px 22px; border-radius:4px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                                {{ __('email_btn_review_and_approve') }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#6b7280;">
                            {{ __('email_footer_rights', ['year' => date('Y')]) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>

