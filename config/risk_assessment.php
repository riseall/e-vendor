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
];
