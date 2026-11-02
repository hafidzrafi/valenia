<?php

declare(strict_types=1);

return [
    'GET' => static function (): Response|string {
        if (null === portal_patient()) {
            return new Response('', 403);
        }

        return render(app_path('app/views/partials/served-boards.php'), [
            'boards' => queue_boards(),
        ]);
    },
];
