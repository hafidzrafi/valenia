<?php

declare(strict_types=1);

return [
    // Landing page is a standalone full page (its own <html> document), so it is
    // rendered raw instead of through the shared layout.
    'GET' => static fn (Request $request): string => render(app_path('app/views/landing.php'), [
        'title' => 'VALENIA',
    ]),
];
