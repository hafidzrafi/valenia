<?php

declare(strict_types=1);

/**
 * /greeting - htmx fragment endpoint. Returns a partial with no layout.
 */
return [
    'GET' => static fn (): string =>
        '<span class="inline-flex items-center gap-1 text-emerald-600">'
        . '<i class="ph ph-check-circle"></i>Halo dari server! - ' . date('H:i:s') . '</span>',
];
