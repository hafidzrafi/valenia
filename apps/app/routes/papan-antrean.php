<?php

declare(strict_types=1);

/*
 * Live board data for the landing page, consumed by the rotating
 * "Nomor Dilayani Saat Ini" card.
 *
 * Returns JSON (not markup) so the client can refresh the data without
 * replacing the DOM, which would reset the carousel position every poll.
 */

return [
    'GET' => static function (): Response {
        $cards = array_map(
            static fn (array $board): array => [
                'code' => null !== $board['current_queue_code'] ? (string) $board['current_queue_code'] : '—',
                'service' => (string) $board['service_name'],
            ],
            queue_boards()
        );

        return new Response(
            (string) json_encode(['cards' => $cards]),
            200,
            ['Content-Type' => 'application/json', 'Cache-Control' => 'no-store']
        );
    },
];
