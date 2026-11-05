<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ __('email_subject_requalification_triggered') }}</title>
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
                            {{ __('email_requalification_header') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:18px;">
                            {{ __('email_greeting', ['name' => optional($application->general)->nama_perusahaan ?: (optional($application->user)->name ?: 'Vendor')]) }},<br><br>
                            @if ($application->requalification_reason === \App\Models\VendorApplication::REASON_QA_TRIGGER)
                                {{ __('email_requalification_notice', ['number' => $applicationNumber]) }}
                            @else
                                {{ __('email_requalification_notice_auto', ['number' => $applicationNumber]) }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; line-height:21px; padding:16px 20px; background:#fff8e6; border-left:4px solid #f59e0b; border-radius:4px;">
                            <strong style="color:#92400e;">{{ __('email_requalification_reason_label') }}:</strong>
                            <div style="font-size:15px; font-weight:600; color:#78350f; margin-top:6px;">
                                {{ $reasonLabel }}
                            </div>
                            @if (!empty($application->admin_note) && $application->requalification_reason === \App\Models\VendorApplication::REASON_QA_TRIGGER)
                                <div style="margin-top:12px; padding-top:12px; border-top:1px dashed #fcd34d;">
                                    <strong style="color:#92400e; font-size:13px;">{{ __('email_requalification_additional_notes') }}:</strong>
                                    <div style="font-size:14px; color:#451a03; margin-top:4px; line-height:20px; font-style:italic;">
                                        &ldquo;{{ $application->admin_note }}&rdquo;
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:14px; line-height:22px; color:#4b5563; padding-top:20px;">
                            {{ __('email_requalification_prompt') }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 0 34px;">
                            <a href="{{ route('registrasi.index') }}"
                                style="display:inline-block; background:#b45309; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 24px; border-radius:6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                {{ __('email_start_requalification_btn') }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#6b7280; border-top:1px solid #e5e7eb; padding-top:18px;">
                            {{ __('email_do_not_reply') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
