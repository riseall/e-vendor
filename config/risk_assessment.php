<?php

return [
    'score_min' => env('RISK_ASSESSMENT_SCORE_MIN', 1),
    'score_max' => env('RISK_ASSESSMENT_SCORE_MAX', 10),

    // Physical supplier risk assessment form:
    // (Safety Efficacy + Availability) x (Detectability + Probability).
    // Low: 12-88, Medium: 89-164, High: 165-240.
    'low_threshold' => env('RISK_ASSESSMENT_LOW_THRESHOLD', 88),
    'high_threshold' => env('RISK_ASSESSMENT_HIGH_THRESHOLD', 164),
];
