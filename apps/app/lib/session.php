<?php

declare(strict_types=1);

/**
 * PostgreSQL-backed PHP sessions (FR-AUTH-02, NFR-SEC-04).
 *
 * Implements SessionHandlerInterface over the `sessions` table so `$_SESSION`
 * works normally while the data lives in PostgreSQL:
 *
 *   - the cookie holds a random 256-bit session id (raw token);
 *   - the database stores only its SHA-256 hash (`token_hash`);
 *   - absolute lifetime is 12 hours (`expires_at`), idle timeout 30 minutes
 *     (`last_seen_at`), both enforced by the auth guard;
 *   - authenticated and anonymous sessions are persisted. Anonymous rows have
 *     `user_id = NULL` and hold the public patient-flow draft/ticket/portal
 *     state until it expires.
 */
const SESSION_IDLE_SECONDS = 1800;
const SESSION_ABSOLUTE_SECONDS = 43200;
const SESSION_COOKIE = 'valenia_session';

final class DbSessionHandler implements SessionHandlerInterface
{
    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        $stmt = db()->prepare(
            'SELECT payload FROM sessions WHERE token_hash = :hash AND expires_at > now()'
        );
        $stmt->execute(['hash' => hash('sha256', $id)]);
        $payload = $stmt->fetchColumn();

        return is_string($payload) ? $payload : '';
    }

    public function write(string $id, string $data): bool
    {
        $rawUserId = $_SESSION['user_id'] ?? null;
        $userId = is_string($rawUserId) && '' !== $rawUserId ? $rawUserId : null;
        $hash = hash('sha256', $id);

        if (null === $userId) {
            $updated = db()->prepare(
                'UPDATE sessions SET payload = :payload, last_seen_at = now()
                 WHERE token_hash = :hash AND user_id IS NULL'
            );
            $updated->execute(['payload' => $data, 'hash' => $hash]);
        } else {
            $updated = db()->prepare(
                'UPDATE sessions SET payload = :payload, last_seen_at = now()
                 WHERE token_hash = :hash AND user_id = :user'
            );
            $updated->execute(['payload' => $data, 'hash' => $hash, 'user' => $userId]);
        }

        if (0 === $updated->rowCount()) {
            db()->prepare(
                'INSERT INTO sessions (user_id, token_hash, payload, ip_address, user_agent, expires_at)
                 VALUES (:user, :hash, :payload, :ip, :ua, now() + make_interval(secs => :lifetime))'
            )->execute([
                'user' => $userId,
                'hash' => $hash,
                'payload' => $data,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'lifetime' => SESSION_ABSOLUTE_SECONDS,
            ]);
        }

        return true;
    }

    public function destroy(string $id): bool
    {
        db()->prepare('DELETE FROM sessions WHERE token_hash = :hash')
            ->execute(['hash' => hash('sha256', $id)])
        ;

        return true;
    }

    public function gc(int $maxlifetime): int
    {
        $stmt = db()->prepare('DELETE FROM sessions WHERE expires_at <= now()');
        $stmt->execute();

        return $stmt->rowCount();
    }
}

/**
 * Start the application session exactly once per request.
 *
 * Cookie: HttpOnly, SameSite=Lax, Secure only in production (plain HTTP in
 * development would otherwise drop the cookie). Strict mode + cookies only.
 */
function start_app_session(): void
{
    if (PHP_SESSION_ACTIVE === session_status()) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    session_set_save_handler(new DbSessionHandler(), true);
    session_name(SESSION_COOKIE);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => 'production' === env('APP_ENV'),
    ]);
    session_start();
}
