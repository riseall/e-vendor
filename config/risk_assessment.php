<?php

return [
    'score_min' => env('RISK_ASSESSMENT_SCORE_MIN', 1),
    'score_max' => env('RISK_ASSESSMENT_SCORE_MAX', 10),

    // Physical supplier risk assessment form:
    // (Safety Efficacy + Availability) x (Detectability + Probability).
    // Low: 12-88, Medium: 89-164, High: 165-240.
    'low_threshold' => env('RISK_ASSESSMENT_LOW_THRESHOLD', 88),
    'high_threshold' => env('RISK_ASSESSMENT_HIGH_THRESHOLD', 164),

    // Label fungsi bahan (Section D / Probability). Key = skor angka,
    // value = label yang disimpan ke DB dan dipakai untuk branching form vendor.
    'material_functions' => [
        1 => 'Non Kontak Produk',
        2 => 'Secondary Packaging',
        3 => 'Eksipien / Primary Packaging',
        4 => 'API',
    ],

    // Konfigurasi Risk Assessment Vendor Eksisting (Kategori 1: Bahan Baku & Bahan Kemas)
    // Formula: (A + B) * (C + D)
    'existing' => [
        'low_threshold'       => (int) env('RISK_ASSESSMENT_EXISTING_LOW', 84),
        'medium_threshold'    => (int) env('RISK_ASSESSMENT_EXISTING_MEDIUM', 168),
        'high_threshold'      => (int) env('RISK_ASSESSMENT_EXISTING_HIGH', 336),
        'very_high_threshold' => (int) env('RISK_ASSESSMENT_EXISTING_VERY_HIGH', 672),
        'levels' => [
            'low' => [
                'min' => 42,
                'max' => 84,
                'period_years' => 7,
                'action_label' => 'Audit by Questionnaire',
                'audit_type'   => 'on_desk',
                'quality_agreement' => false,
            ],
            'medium' => [
                'min' => 85,
                'max' => 168,
                'period_years' => 5,
                'action_label' => 'Audit by Questionnaire',
                'audit_type'   => 'on_desk',
                'quality_agreement' => false,
            ],
            'high' => [
                'min' => 169,
                'max' => 336,
                'period_years' => 3,
                'action_label' => 'Audit On Site',
                'audit_type'   => 'on_site',
                'quality_agreement' => false,
            ],
            'very_high' => [
                'min' => 337,
                'max' => 672,
                'period_years' => 2,
                'action_label' => 'Audit On Site, Quality Agreement',
                'audit_type'   => 'on_site',
                'quality_agreement' => true,
            ],
        ],
    ],

    // Skema Form Penilaian Vendor Eksisting (14 Parameter dalam 4 Bagian: A, B, C, D)
    'existing_sections' => [
        'a' => [
            'letter' => 'A',
            'title'  => 'Safety Efficacy',
            'sub'    => 'Severity — Keamanan, keluhan efek samping & critical attribute',
            'color'  => '#005db6',
            'fields' => [
                [
                    'name'    => 'score_safety_efficacy_doc',
                    'label'   => 'A1. Kelengkapan Dokumen',
                    'options' => [
                        ['val' => '1', 'label' => 'Dokumen Lengkap', 'desc' => 'Ada semua (CDOB/GMP, PBF, Halal, ISO, NIB, NPWP)', 'risk_class' => 'selected-safe'],
                        ['val' => '3', 'label' => 'Dokumen Kurang Lengkap', 'desc' => 'Sebagian dokumen terpenuhi', 'risk_class' => 'selected-neutral'],
                        ['val' => '4', 'label' => 'Dokumen Tidak Lengkap / N/A', 'desc' => 'Tidak ada atau tidak berlaku', 'risk_class' => 'selected-risk'],
                    ],
                ],
                [
                    'name'    => 'score_complaint_side_effect',
                    'label'   => 'A2. Berita Keluhan Efek Samping dan/atau Pasca Pemasaran',
                    'options' => [
                        ['val' => '4', 'label' => 'Critical', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => 'Major', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => 'Minor', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'Tidak Ada / N/A', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_safety_efficacy_attr',
                    'label'   => 'A3. Bahan yang di-supply memiliki Critical Attribute',
                    'options' => [
                        ['val' => '8', 'label' => 'Memiliki Critical Attribute', 'desc' => 'Wajib lapor BPOM, steril, cemaran EG DEG, uji BE', 'risk_class' => 'selected-risk'],
                        ['val' => '1', 'label' => 'Tidak Memiliki Critical Attribute', 'desc' => 'Non-critical attribute', 'risk_class' => 'selected-safe'],
                    ],
                ],
            ],
        ],
        'b' => [
            'letter' => 'B',
            'title'  => 'Availability',
            'sub'    => 'Severity — Obat pareto, volume pembelian, jenis pemasok & status NIE',
            'color'  => '#0f766e',
            'fields' => [
                [
                    'name'    => 'score_pareto',
                    'label'   => 'B1. Produsen / Pemasok Obat-obat Pareto',
                    'options' => [
                        ['val' => '4', 'label' => 'Pareto A', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => 'Pareto B', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => 'Pareto C', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'Pareto D / Non Pareto', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_purchase_volume',
                    'label'   => 'B2. Volume Pembelian terhadap Pemasok',
                    'options' => [
                        ['val' => '4', 'label' => '> 1000 kg', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => '500 – 1000 kg', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => '100 – 500 kg', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => '< 100 kg dan N/A', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_availability_type',
                    'label'   => 'B3. Jenis Pemasok',
                    'options' => [
                        ['val' => '1', 'label' => 'Manufaktur', 'risk_class' => 'selected-safe'],
                        ['val' => '2', 'label' => 'Distributor Pertama / Kedua', 'risk_class' => 'selected-safe'],
                        ['val' => '3', 'label' => 'Repacker', 'risk_class' => 'selected-neutral'],
                        ['val' => '4', 'label' => 'Trader', 'risk_class' => 'selected-risk'],
                    ],
                ],
                [
                    'name'    => 'score_nie_status',
                    'label'   => 'B4. Status NIE (Nomor Izin Edar)',
                    'options' => [
                        ['val' => '3', 'label' => 'Bahan Belum Terdaftar pada NIE', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => 'Bahan dalam Proses Pendaftaran NIE', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'Bahan Sudah Terdaftar pada NIE', 'risk_class' => 'selected-safe'],
                    ],
                ],
            ],
        ],
        'c' => [
            'letter' => 'C',
            'title'  => 'Detectability',
            'sub'    => 'Riwayat tahun audit, hasil temuan, respon & jumlah komplain',
            'color'  => '#7c3aed',
            'fields' => [
                [
                    'name'    => 'score_audit_year',
                    'label'   => 'C1. Tahun Terakhir Audit',
                    'options' => [
                        ['val' => '4', 'label' => '> 4 tahun yang lalu', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => '3 – 4 tahun yang lalu', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => '2 – 3 tahun yang lalu', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => '< 2 tahun yang lalu / N/A', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_audit_result',
                    'label'   => 'C2. Hasil Audit',
                    'options' => [
                        ['val' => '4', 'label' => 'Belum Ada / NA (*D)', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => 'Critical (*C)', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => 'Major (*B)', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'Minor (*A)', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_audit_response',
                    'label'   => 'C3. Respon terhadap Hasil Audit',
                    'options' => [
                        ['val' => '4', 'label' => 'Tidak Respon', 'risk_class' => 'selected-risk'],
                        ['val' => '1', 'label' => 'Respon Baik / Tepat Waktu', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_complaint_count',
                    'label'   => 'C4. Jumlah Complain (Internal, OOS, & External)',
                    'options' => [
                        ['val' => '4', 'label' => '> 6 Komplain', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => '4 – 6 Komplain', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => '1 – 3 Komplain', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'N/A / 0 Komplain', 'risk_class' => 'selected-safe'],
                    ],
                ],
            ],
        ],
        'd' => [
            'letter' => 'D',
            'title'  => 'Probability',
            'sub'    => 'Grade bahan baku, variasi produk fasilitas & peruntukan',
            'color'  => '#b45309',
            'fields' => [
                [
                    'name'    => 'score_material_grade',
                    'label'   => 'D1. Grade Bahan Baku',
                    'options' => [
                        ['val' => '4', 'label' => 'Reagent Grade', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => 'Foodgrade', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => 'Chemical Grade', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'Pharmagrade', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_facility_variation',
                    'label'   => 'D2. Variasi Produk pada Fasilitas (Pharmaceutical, F&B, Veterinary, dll)',
                    'options' => [
                        ['val' => '4', 'label' => 'Pharmagrade', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => 'Pharmagrade & 1 variasi', 'risk_class' => 'selected-neutral'],
                        ['val' => '2', 'label' => 'Pharmagrade & 2 variasi', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'Pharmagrade & 3 variasi', 'risk_class' => 'selected-safe'],
                    ],
                ],
                [
                    'name'    => 'score_material_purpose',
                    'label'   => 'D3. Peruntukan Bahan Baku',
                    'options' => [
                        ['val' => '4', 'label' => 'Steril Betalaktam / Cephalosporin', 'risk_class' => 'selected-risk'],
                        ['val' => '3', 'label' => 'Steril Non Betalaktam', 'risk_class' => 'selected-risk'],
                        ['val' => '2', 'label' => 'Cair Oral & Topikal', 'risk_class' => 'selected-neutral'],
                        ['val' => '1', 'label' => 'TTSK (Tablet, Tetes, Salep, Kapsul)', 'risk_class' => 'selected-safe'],
                    ],
                ],
            ],
        ],
    ],
];

