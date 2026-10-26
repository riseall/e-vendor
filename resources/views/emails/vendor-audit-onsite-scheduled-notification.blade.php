<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ __('email_subject_audit_onsite_scheduled') }}</title>
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
                            {{ __('email_audit_onsite_title') }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:20px;">
                            {{ __('email_audit_onsite_notice', [
                                'number' => $applicationNumber,
                                'company' => optional($application->general)->nama_perusahaan ?? '-'
                            ]) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f3f4f6; border-radius:6px; padding:16px;">
                                <tr>
                                    <td style="font-size:13px; color:#374151;">
                                        <strong>{{ __('email_audit_schedule_and_location') }}:</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:12px; color:#6b7280; padding-top:8px; line-height:20px;">
                                        {{ __('email_audit_date') }}: <strong>{{ \Carbon\Carbon::parse($audit->confirmed_schedule_at)->translatedFormat('d F Y H:i') }} WIB</strong><br />
                                        {{ __('email_audit_location') }}: <strong>{{ $audit->audit_location }}</strong><br />
                                        {{ __('email_audit_agenda') }}: <strong>{{ $audit->audit_agenda }}</strong><br />
                                        @if(!empty($audit->auditor_team))
                                            <br /><strong>{{ __('email_auditor_team') }}:</strong>
                                            <ul style="margin-top:4px; padding-left:20px;">
                                                @foreach((array)$audit->auditor_team as $member)
                                                    <li>{{ $member['name'] ?? '' }} ({{ $member['role'] ?? '' }})</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ url('/login') }}"
                                style="display:inline-block; background:#0284c7; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                {{ __('email_open_evendor_btn') }}
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
