<?php

declare(strict_types=1);

return [
    'GET' => static function (Request $request): string {
        return view('kontak', [
            'title' => 'Kontak Poliklinik',
            'hero' => [
                'title' => 'Kontak Poliklinik',
                'subtitle' => 'Layanan Poliklinik Politeknik Negeri Malang',
            ],
            'location' => [
                'name' => 'Poliklinik POLINEMA',
                'maps_url' => 'https://maps.google.com/?q=Poliklinik+POLINEMA+Malang',
                'embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1975.752780399872!2d112.61486329625531!3d-7.946592287535437!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e788274114f1135%3A0x2bff80027f459b8b!2sPoliklinik%20POLINEMA!5e0!3m2!1sid!2sid!4v1793602467828!5m2!1sid!2sid',
            ],
            'call_centers' => [
                ['label' => 'Call Center', 'number' => 'xxxx - xxxx - xxxx'],
                ['label' => 'Call Center', 'number' => 'xxxx - xxxx - xxxx'],
                ['label' => 'Call Center', 'number' => 'xxxx - xxxx - xxxx'],
            ],
        ]);
    },
];
