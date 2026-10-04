<?php

declare(strict_types=1);

/**
 * /sandbox - demonstrator: GET lists rows, POST inserts (parameterized).
 * CSRF is enforced globally by bootstrap.
 */
return (static function (): array {
    $list = static fn (): array => db()
        ->query('SELECT id, name, created_at FROM sandbox_items ORDER BY id DESC')
        ->fetchAll();

    return [
        'GET' => static fn (): string => view('sandbox', [
            'items' => $list(),
            'token' => csrf_token(),
        ]),

        'POST' => static function () use ($list): Response {
            $name = request_input('name');

            if ($name === null || $name === '') {
                return new Response(
                    view('sandbox', [
                        'items' => $list(),
                        'token' => csrf_token(),
                        'error' => 'Nama wajib diisi.',
                    ]),
                    422,
                );
            }

            $stmt = db()->prepare('INSERT INTO sandbox_items (name) VALUES (?)');
            $stmt->execute([$name]);

            return redirect('/sandbox');
        },
    ];
})();
