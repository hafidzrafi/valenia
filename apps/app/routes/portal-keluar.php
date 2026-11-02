<?php

declare(strict_types=1);

return [
    'POST' => static function (Request $request): Response {
        lock_portal();

        return redirect('/form-riwayat');
    },
];
