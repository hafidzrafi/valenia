<?php

declare(strict_types=1);

return [
    'identity_patterns' => [
        'NIM' => '/^\d{12}$/',
        'NIP' => '/^\d{18}$/',
    ],
    'services' => [
        'POLI UMUM' => 'Poli Umum',
        'POLI GIGI' => 'Poli Gigi',
        'KONSELING' => 'Konseling/Psikologi',
    ],
    'portal_ttl_seconds' => 900,
];
