<?php

declare(strict_types=1);

/**
 * Middleware registry and onion pipeline.
 *
 * Guards register under a name and receive (Request, callable $next[, string $arg]).
 * Route files reference them as 'name' or 'name:arg'.
 */
final class Middleware
{
    /** @var array<string, callable> */
    private static array $registry = [];

    public static function register(string $name, callable $middleware): void
    {
        self::$registry[$name] = $middleware;
    }

    /**
     * Wrap $destination with the given middleware names (outermost first).
     *
     * @param list<string> $names
     */
    public static function pipeline(array $names, callable $destination): callable
    {
        $pipeline = $destination;

        foreach (array_reverse($names) as $name) {
            $middleware = self::resolve($name);
            $next = $pipeline;
            $pipeline = static fn (Request $request): Response => $middleware($request, $next);
        }

        return $pipeline;
    }

    private static function resolve(string $name): callable
    {
        [$base, $arg] = array_pad(explode(':', $name, 2), 2, null);

        if (!isset(self::$registry[$base])) {
            throw new RuntimeException('Middleware tidak dikenal: ' . $base);
        }

        $middleware = self::$registry[$base];

        if ($arg === null) {
            return $middleware;
        }

        return static fn (Request $request, callable $next): Response => $middleware($request, $next, $arg);
    }
}
