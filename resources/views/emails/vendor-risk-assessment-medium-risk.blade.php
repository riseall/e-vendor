<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ __('email_risk_result_audit') }}</title>
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
                        <td align="center" style="font-size:20px; color:#f59e0b; padding-bottom:14px;">
                            {{ __('email_risk_result_audit') }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:20px;">
                            {{ __('email_risk_medium_body', [
                                'number' => $applicationNumber,
                                'company' => optional($application->general)->nama_perusahaan ?? '-'
                            ]) }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:14px; line-height:20px; color:#4b5563; padding-bottom:16px;">
                            {{ __('email_risk_medium_desc') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f3f4f6; border-radius:6px; padding:16px;">
                                <tr>
                                    <td style="font-size:13px; color:#374151;">
                                        <strong>{{ __('email_assessment_details') }}:</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px; color:#6b7280; padding-top:8px; line-height:20px;">
                                        {{ __('email_total_score') }}: <strong>{{ $qualification->total_score ?? '-' }}</strong><br />
                                        {{ __('email_risk_level') }}: <strong>MEDIUM</strong><br />
                                        {{ __('email_audit_type') }}: <strong>{{ __('audit_type_on_desk') }}</strong><br />
                                        {{ __('email_assessment_date') }}: <strong>{{ now()->format('d M Y') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            @php
                                $auditLink = optional($application->audits()->latest('id')->first())->id
                                    ? route(
                                        'vendor.audit.questionnaire',
                                        $application->audits()->latest('id')->first()->id,
                                    )
                                    : route('registrasi.tracking', $applicationNumber);
                            @endphp
                            <a href="{{ $auditLink }}"
                                style="display:inline-block; background:#f59e0b; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                {{ __('email_fill_questionnaire_btn') }}
                            </a>
                            <div style="font-size:12px; color:#9ca3af; padding-top:10px;">
                                <a href="{{ route('registrasi.tracking', $applicationNumber) }}"
                                    style="color:#f59e0b; text-decoration:underline;">
                                    {{ __('email_or_view_status_here') }}
                                </a>
                            </div>
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
