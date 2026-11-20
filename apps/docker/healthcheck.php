<?php

declare(strict_types=1);

/**
 * PHP-FPM health probe for the container HEALTHCHECK.
 *
 * Speaks FastCGI to the local pool and requests the ping endpoint configured in
 * docker/Dockerfile (ping.path = /ping, ping.response = pong). Written in plain
 * PHP so the runtime image needs no extra client package, and the probe never
 * touches the database: a slow query must not flap the container.
 *
 * Exit code 0 means the pool answered pong; anything else is unhealthy.
 */
const FCGI_VERSION_1 = 1;
const FCGI_BEGIN_REQUEST = 1;
const FCGI_END_REQUEST = 3;
const FCGI_PARAMS = 4;
const FCGI_STDIN = 5;
const FCGI_STDOUT = 6;
const FCGI_RESPONDER = 1;

/**
 * One FastCGI record: 8-byte header, content, then padding to 8 bytes.
 */
function fcgi_record(int $type, string $content, int $requestId = 1): string
{
    $length = strlen($content);
    $padding = (8 - ($length % 8)) % 8;

    return pack('CCnnCx', FCGI_VERSION_1, $type, $requestId, $length, $padding)
        .$content
        .str_repeat("\0", $padding);
}

/**
 * Encode a FastCGI name-value pair (lengths >= 128 use the 4-byte form).
 */
function fcgi_pair(string $name, string $value): string
{
    $encode = static fn (int $length): string => $length < 128
        ? chr($length)
        : pack('N', $length | 0x80000000);

    return $encode(strlen($name)).$encode(strlen($value)).$name.$value;
}

$host = (string) (getenv('FPM_HOST') ?: '127.0.0.1');
$port = (int) (getenv('FPM_PORT') ?: 9000);
$timeout = (float) (getenv('FPM_TIMEOUT') ?: 3);

$socket = @fsockopen($host, $port, $errorCode, $errorMessage, $timeout);

if (!is_resource($socket)) {
    fwrite(STDERR, "fpm healthcheck: cannot connect to {$host}:{$port} ({$errorMessage})\n");

    exit(1);
}

stream_set_timeout($socket, (int) ceil($timeout));

$params = [
    'SCRIPT_NAME' => '/ping',
    'SCRIPT_FILENAME' => '/ping',
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => '/ping',
    'QUERY_STRING' => '',
    'SERVER_PROTOCOL' => 'HTTP/1.1',
    'GATEWAY_INTERFACE' => 'CGI/1.1',
    'SERVER_SOFTWARE' => 'valenia-healthcheck',
    'SERVER_NAME' => 'localhost',
    'SERVER_PORT' => '80',
    'REMOTE_ADDR' => '127.0.0.1',
    'CONTENT_LENGTH' => '0',
];

$body = '';

foreach ($params as $name => $value) {
    $body .= fcgi_pair($name, $value);
}

$request = fcgi_record(FCGI_BEGIN_REQUEST, pack('nCxxxxx', FCGI_RESPONDER, 0))
    .fcgi_record(FCGI_PARAMS, $body)
    .fcgi_record(FCGI_PARAMS, '')   // empty PARAMS terminates the parameter list
    .fcgi_record(FCGI_STDIN, '');

fwrite($socket, $request);

$stdout = '';

while (!feof($socket)) {
    $header = fread($socket, 8);

    if (false === $header || strlen($header) < 8) {
        break;
    }

    $decoded = unpack('Cversion/Ctype/nrequestId/nlength/Cpadding/Creserved', $header);

    $contentLength = (int) $decoded['length'];
    $content = 0 < $contentLength ? (string) fread($socket, $contentLength) : '';

    if (0 < (int) $decoded['padding']) {
        fread($socket, (int) $decoded['padding']);
    }

    if (FCGI_STDOUT === (int) $decoded['type']) {
        $stdout .= $content;
    }

    if (FCGI_END_REQUEST === (int) $decoded['type']) {
        break;
    }
}

fclose($socket);

if (!str_contains($stdout, 'pong')) {
    fwrite(STDERR, "fpm healthcheck: pool did not answer pong\n");

    exit(1);
}

exit(0);
