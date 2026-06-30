<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Revisi Permohonan Vendor</title>
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
                        <td align="center" style="font-size:20px; padding-bottom:14px;">Permohonan Perlu Direvisi</td>
                    </tr>
                    <tr>
                        <td style="font-size:15px; line-height:22px; color:#4b5563; padding-bottom:18px;">
                            Yth. {{ optional($application->user)->name ?? 'Vendor' }}, tim pengadaan meminta
                            perbaikan pada permohonan <strong>{{ $applicationNumber }}</strong>.
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="font-size:14px; line-height:21px; padding:14px 18px; background:#fff8e6; border-left:4px solid #f59e0b;">
                            <strong>Bagian yang perlu direvisi:</strong>
                            <ul style="margin:10px 0 0; padding-left:20px;">
                                @forelse ((array) $application->revision_notes as $revision)
                                    <li style="margin-bottom:6px;">
                                        <strong>{{ $revision['label'] ?? ($revision['field'] ?? 'Data vendor') }}</strong>
                                        @if (!empty($revision['note']))
                                            : {{ $revision['note'] }}
                                        @endif
                                    </li>
                                @empty
                                    <li>{{ $application->admin_note ?: 'Silakan periksa catatan revisi pada aplikasi.' }}
                                    </li>
                                @endforelse
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 0 34px;">
                            <a href="{{ route('registrasi.index') }}"
                                style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; font-size:13px; padding:9px 18px; border-radius:3px;">
                                PERBAIKI DATA
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
