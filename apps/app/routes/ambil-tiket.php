<?php
declare(strict_types=1);

return [
    'GET' => static fn (Request $request): string => view('ambil-tiket', [
        'title' => 'Ambil Tiket',
    ]),
    'POST' => static function (Request $request): Response {
        
       
        return redirect('/ambil-tiket'); 
    },
];