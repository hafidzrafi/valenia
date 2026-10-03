<?php

declare(strict_types=1);

/**
 * Read-only access to the current HTTP request, plus the Request object that
 * flows through the middleware pipeline.
 */

final class Request
{
    /**
     * @param array<string, string> $params
     * @param array<string, mixed>  $attributes
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $params = [],
        public array $attributes = [],
    ) {
    }

    public function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function request_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    return rawurldecode($path);
}

function request_input(string $key, ?string $default = null): ?string
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;

    return is_string($value) ? trim($value) : $default;
}
