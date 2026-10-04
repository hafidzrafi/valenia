<?php

declare(strict_types=1);

/** Immutable HTTP response value object plus a redirect helper. */

final class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string $body = '',
        public readonly int $status = 200,
        public readonly array $headers = [],
    ) {
    }

    public static function from(string|self $result): self
    {
        return is_string($result) ? new self($result) : $result;
    }
}

function redirect(string $to, int $status = 302): Response
{
    return new Response('', $status, ['Location' => $to]);
}
