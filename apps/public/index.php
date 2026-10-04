<?php

declare(strict_types=1);

/**
 * Front controller. Serves real static files through the built-in server and
 * dispatches every other request through the file-based router.
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($path !== '/' && $path !== '/index.php') {
    $public = realpath(__DIR__) ?: __DIR__;
    $target = realpath(__DIR__ . $path);

    if ($target !== false && is_file($target) && str_starts_with($target, $public . DIRECTORY_SEPARATOR)) {
        return false;
    }
}

$router = require __DIR__ . '/../app/bootstrap.php';

$response = $router->dispatch(request_method(), request_path());

http_response_code($response->status);

foreach ($response->headers as $name => $value) {
    header($name . ': ' . $value);
}

echo $response->body;
