<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Pemberitahuan Audit - {{ optional($application)->application_number }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #222;
            max-width: 800px;
            margin: 30px auto;
            padding: 0 24px;
        }

        h1 {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 4px;
        }

        .meta {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 24px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        th,
        td {
            text-align: left;
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11pt;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
            width: 30%;
        }

        .footer {
            margin-top: 48px;
        }

        .sign {
            margin-top: 64px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <h1>SURAT PEMBERITAHUAN AUDIT VENDOR</h1>
    <div class="meta">PT Phapros Tbk — Sistem E-Vendor</div>

    <p>Kepada Yth. <strong>{{ optional(optional($application)->general)->nama_perusahaan ?? '—' }}</strong></p>

    <p>Dengan hormat,</p>
    <p>Sehubungan dengan proses kualifikasi vendor PT Phapros Tbk, dengan ini kami sampaikan rencana audit
        on-site untuk permohonan nomor <strong>{{ optional($application)->application_number }}</strong>.</p>

    <table>
        <tr>
            <th>No Permohonan</th>
            <td>{{ optional($application)->application_number }}</td>
        </tr>
        <tr>
            <th>Nama Vendor</th>
            <td>{{ optional(optional($application)->general)->nama_perusahaan ?? '—' }}</td>
        </tr>
        <tr>
            <th>Risk Level</th>
            <td>{{ strtoupper(optional($application)->risk_level ?? '—') }}</td>
        </tr>
        <tr>
            <th>Tanggal Audit</th>
            <td>{{ optional($audit->confirmed_schedule_at)->format('d F Y H:i') ?? 'Akan dikonfirmasi' }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>{{ $audit->audit_location ?? '—' }}</td>
        </tr>
        <tr>
            <th>Agenda</th>
            <td>{{ $audit->audit_agenda ?? '—' }}</td>
        </tr>
        <tr>
            <th>Tim Auditor</th>
            <td>
                @if ($audit->auditor_team)
                    <ul style="margin:0;padding-left:18px;">
                        @foreach ($audit->auditor_team as $m)
                            <li>{{ $m['name'] ?? '—' }} ({{ $m['role'] ?? '—' }})</li>
                        @endforeach
                    </ul>
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <th>QA Lead</th>
            <td>{{ optional($audit->qaLead)->name ?? '—' }}</td>
        </tr>
    </table>

    <p>Dimohon kepada vendor untuk menyiapkan akses lokasi, dokumen mutu, dan PIC yang diperlukan selama audit.
        Apabila terdapat perubahan jadwal, mohon untuk segera menginformasikan kepada tim QA Phapros.</p>

    <div class="footer">
        <p>Surat ini di-generate otomatis oleh sistem E-Vendor pada
            {{ optional($audit->audit_letter_sent_at)->format('d F Y H:i') ?? now()->format('d F Y H:i') }}.</p>

        <div class="sign">
            <p>Hormat kami,</p>
            <br><br><br>
            <p><strong>Tim Quality Assurance</strong><br>PT Phapros Tbk</p>
        </div>
    </div>
</body>

</html>
