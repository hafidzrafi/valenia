<?php

declare(strict_types=1);

/** Minimal server-side view renderer with HTML escaping. */

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Render a page inside the shared layout.
 *
 * @param array<string, mixed> $data
 */
function view(string $name, array $data = []): string
{
    $content = render(app_path('app/views/' . $name . '.php'), $data);

    return render(app_path('app/views/layout.php'), $data + [
        'title' => $data['title'] ?? 'VALENIA',
        'content' => $content,
    ]);
}

/**
 * @param array<string, mixed> $data
 */
function render(string $file, array $data): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require $file;

    return (string) ob_get_clean();
}
