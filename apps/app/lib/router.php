<?php

declare(strict_types=1);

/**
 * File-based router.
 *
 * A route file in $dir becomes a URL pattern:
 *   _index.php   -> /
 *   about.php    -> /about
 *   room.$id.php -> /room/{id}
 *
 * Each route file returns an array keyed by HTTP method, with an optional
 * '_middleware' list. Handlers receive a Request and return a Response/string.
 *
 * Safety: only files discovered by glob() are ever required, so a user-supplied
 * path can never reach the filesystem.
 */
final class Router
{
    /** @var array<string, array<string, array{handler: callable, middleware: list<string>}>> */
    private array $routes = [];

    /**
     * @param list<string> $globalMiddleware
     */
    public function __construct(private string $dir, private array $globalMiddleware = [])
    {
        $this->load();
    }

    private function load(): void
    {
        foreach (glob($this->dir . '/*.php') ?: [] as $file) {
            $pattern = self::toPattern(basename($file, '.php'));
            $definition = require $file;

            if (is_callable($definition)) {
                $definition = ['GET' => $definition];
            }

            $middleware = $definition['_middleware'] ?? [];
            unset($definition['_middleware']);

            foreach ($definition as $method => $handler) {
                $this->routes[strtoupper((string) $method)][$pattern] = [
                    'handler' => $handler,
                    'middleware' => array_values((array) $middleware),
                ];
            }
        }
    }

    private static function toPattern(string $name): string
    {
        $segments = array_filter(
            explode('.', $name),
            static fn (string $segment): bool => $segment !== '_index',
        );

        if ($segments === []) {
            return '';
        }

        return implode('/', array_map(
            static fn (string $segment): string => str_starts_with($segment, '$')
                ? '(?P<' . substr($segment, 1) . '>[^/]+)'
                : preg_quote($segment, '#'),
            $segments,
        ));
    }

    public function dispatch(string $method, string $path): Response
    {
        $path = trim($path, '/');

        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            if (preg_match('#^' . $pattern . '$#', $path, $matches) === 1) {
                return $this->run($route, $method, $path, $matches);
            }
        }

        return $this->pathExists($path)
            ? new Response('405 Method Not Allowed', 405)
            : new Response('404 Not Found', 404);
    }

    /**
     * @param array{handler: callable, middleware: list<string>} $route
     * @param array<int|string, string> $matches
     */
    private function run(array $route, string $method, string $path, array $matches): Response
    {
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        $names = array_merge($this->globalMiddleware, $route['middleware']);

        $pipeline = Middleware::pipeline(
            $names,
            static fn (Request $request): Response => Response::from(($route['handler'])($request)),
        );

        return $pipeline(new Request($method, $path, $params));
    }

    private function pathExists(string $path): bool
    {
        foreach ($this->routes as $patterns) {
            foreach (array_keys($patterns) as $pattern) {
                if (preg_match('#^' . $pattern . '$#', $path) === 1) {
                    return true;
                }
            }
        }

        return false;
    }
}
