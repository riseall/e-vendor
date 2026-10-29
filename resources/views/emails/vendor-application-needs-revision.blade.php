<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ __('email_revision_title') }}</title>
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
                        <td align="center" style="font-size:20px; padding-bottom:14px;">{{ __('email_application_needs_revision') }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:18px;">
                            {{ __('email_revision_notice', ['name' => optional($application->user)->name ?? 'Vendor', 'number' => $applicationNumber]) }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="font-size:14px; line-height:21px; padding:14px 18px; background:#fff8e6; border-left:4px solid #f59e0b;">
                            <strong>{{ __('email_revision_parts_required') }}:</strong>
                            <ul style="margin:10px 0 0; padding-left:20px;">
                                @forelse ((array) $application->revision_notes as $revision)
                                    @php
                                        $fieldKey = $revision['field'] ?? '';
                                        $rawLabel = $revision['label'] ?? ($fieldKey ?: __('email_vendor_data'));
                                        $translatedLabel = \App\Services\VendorRegistrationViewService::translateRevisionLabel($fieldKey, $rawLabel, $revision['item'] ?? null);
                                    @endphp
                                    <li style="margin-bottom:6px;">
                                        <strong>{{ $translatedLabel }}</strong>
                                        @if (!empty($revision['note']))
                                            : {{ $revision['note'] }}
                                        @endif
                                    </li>
                                @empty
                                    @php
                                        $defaultAdminNote = 'Terdapat data yang tidak disetujui oleh pengadaan.';
                                        $adminNoteDisplay = ($application->admin_note === $defaultAdminNote)
                                            ? __('default_revision_admin_note')
                                            : $application->admin_note;
                                    @endphp
                                    <li>{{ $adminNoteDisplay ?: __('email_check_revision_note') }}
                                    </li>
                                @endforelse
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 0 34px;">
                            <a href="{{ route('registrasi.index') }}"
                                style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                {{ __('email_fix_data_btn') }}
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
