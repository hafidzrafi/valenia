<?php

declare(strict_types=1);

return [
    'GET' => static fn (Request $request): string => view('ambil-tiket', [
        'title' => 'Ambil Tiket',
    ]),
    'POST' => static function (Request $request): Response {
        // Proses penyimpanan data form di sini

        // Kembalikan Response redirect
        return redirect('/ambil-tiket');
    },
];
