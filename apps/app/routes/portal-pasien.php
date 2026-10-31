<?php
declare(strict_types=1);

return [
    'GET' => static fn (Request $request): string => view('portal-pasien', [
        'title' => 'Portal Pasien',
    ]),
    'POST' => static function (Request $request): Response {
        
       
        return redirect('/portal-pasien'); 
    },
];