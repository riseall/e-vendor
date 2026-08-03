@php
    $isRekualifikasi = $application->isRekualifikasi();
    $title = $isRekualifikasi ? 'Permohonan Rekualifikasi Vendor' : 'Permohonan Vendor Baru';
    $subtitle = $isRekualifikasi
        ? 'Tim Pengadaan & QA, terdapat permohonan pembaruan data profil vendor yang telah dikirim dan menunggu pemeriksaan.'
        : 'Tim Pengadaan, terdapat permohonan vendor baru yang telah dikirim dan menunggu pemeriksaan kelengkapan data.';
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
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;">
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
                        <td align="center" style="font-size:20px; font-weight:700; color:#1f2933; padding-bottom:14px;">
                            {{ $title }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center"
                            style="font-size:15px; line-height:22px; color:#5f4b4b; padding:0 12px 20px;">
                            {{ $subtitle }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <table role="presentation" cellpadding="0" cellspacing="0"
                                style="margin:0 auto; font-size:14px; color:#384150; text-align:left;">
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Tipe</td>
                                    <td style="padding:4px 10px;">
                                        @if ($isRekualifikasi)
                                            <span
                                                style="display:inline-block; background:#fff7ed; color:#c2410c; border:1px solid #ffedd5; font-weight:700; font-size:12px; padding:2px 8px; border-radius:4px;">Rekualifikasi</span>
                                        @else
                                            <span
                                                style="display:inline-block; background:#eff6ff; color:#1d4ed8; border:1px solid #dbeafe; font-weight:700; font-size:12px; padding:2px 8px; border-radius:4px;">Vendor
                                                Baru</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Nomor</td>
                                    <td style="padding:4px 10px; font-weight:700;">{{ $applicationNumber }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Perusahaan</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($application->general)->nama_perusahaan ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Email</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($application->user)->email ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Tanggal</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($application->submitted_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 10px; color:#6b7280;">Deadline</td>
                                    <td style="padding:4px 10px; font-weight:700;">
                                        {{ optional($deadline)->format('d/m/Y') ?? '-' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:4px 0 34px;">
                            <a href="{{ route('dashboard') }}"
                                style="display:inline-block; background:#5d9cec; color:#ffffff; text-decoration:none; font-size:13px; line-height:18px; padding:6px 14px; border-radius:3px;">
                                E-VENDOR
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:12px; color:#4b5563;">
                            E-Vendor &copy;{{ date('Y') }} PT. Phapros, Tbk. - All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
