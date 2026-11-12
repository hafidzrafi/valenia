<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): string {
        return view('tentang-poliklinik', [
            'title' => 'Tentang Poliklinik',
        ]);
    },
];
