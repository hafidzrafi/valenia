<?php

declare(strict_types=1);

return [
    'GET' => static fn (Request $request): string => view('home'),
];
