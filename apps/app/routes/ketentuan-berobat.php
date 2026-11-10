<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): string {
        return view('ketentuan-berobat', [
            'title' => 'Ketentuan Berobat',
        ]);
    },
];
