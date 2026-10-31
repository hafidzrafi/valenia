<?php

declare(strict_types=1);

return [
    'GET' => static fn (Request $request): string => view('muncul-qr', [
        'title' => 'Ambil scan QR',
    ]),
    'POST' => static function (Request $request): Response {
        return redirect('/muncul-qr');
    },
];
