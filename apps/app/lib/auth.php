<?php

declare(strict_types=1);

/**
 * Authentication services (FR-AUTH-01…08, NFR-SEC-01/04/05/06).
 *
 * Shared logic lives here per the route-centric convention; route files only
 * call these helpers. Passwords are verified with password_verify() and never
 * logged. Authentication outcomes are recorded in `auth_events` (login success,
 * login failure, lockout, logout, password change/reset), while `audit_logs`
 * keeps the row-diff trail:
 *   - successful login  -> INSERT on sessions
 *   - failed attempt    -> UPDATE on credentials (failed_attempts counter)
 *   - logout / revoke   -> DELETE on sessions
 */

const AUTH_MAX_FAILED_ATTEMPTS = 5;
const AUTH_LOCK_MINUTES = 15;
const AUTH_PASSWORD_MIN_LENGTH = 8;

/**
 * The authenticated user resolved for the current request (null when anonymous).
 * Set by the `auth` guard and read by the layout/middleware.
 */
final class Auth
{
    public static ?array $user = null;
}

function current_user(): ?array
{
    return Auth::$user;
}

/**
 * Anonymous-reachable paths (PRD: landing, login, logout, and the future patient
 * history lookup are public). Paths are the trimmed routing values: '' is root.
 * Keep in sync when adding public routes.
 */
function is_public_path(string $path): bool
{
    return in_array($path, ['', 'login', 'logout', 'greeting'], true);
}

/**
 * Insert one audit trail row. $oldJson/$newJson are JSON strings or null.
 */
function audit_log(
    string $table,
    string $rowId,
    string $action,
    ?string $actorId = null,
    ?string $oldJson = null,
    ?string $newJson = null,
): void {
    $stmt = db()->prepare(
        'INSERT INTO audit_logs (table_name, row_id, action, actor_id, old_data, new_data)
         VALUES (:table, :row, :action, :actor, :old, :new)'
    );
    $stmt->bindValue(':table', $table);
    $stmt->bindValue(':row', $rowId);
    $stmt->bindValue(':action', $action);
    $stmt->bindValue(':actor', $actorId, $actorId === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':old', $oldJson, $oldJson === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(':new', $newJson, $newJson === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->execute();
}

/**
 * Append one authentication event (FR-AUTH-01/07, NFR-SEC-06).
 *
 * Wrapped so a trail write can never block authentication itself.
 */
function record_auth_event(string $event, ?string $username, ?string $userId = null): void
{
    try {
        db()->prepare(
            'INSERT INTO auth_events (event, username, user_id, ip_address, user_agent)
             VALUES (:event, :username, :user, :ip, :ua)'
        )->execute([
            'event' => $event,
            'username' => $username,
            'user' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);
    } catch (Throwable $exception) {
        app_log('error', 'Gagal menulis auth_events: ' . $exception->getMessage());
    }
}

/** Hash a password per NFR-SEC-01 (Argon2id when available, else bcrypt). */
function hash_password(string $password): string
{
    $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;

    return password_hash($password, $algo);
}

/**
 * Verify credentials against users + credentials.
 *
 * Generic on purpose: callers must not distinguish an unknown username from a
 * wrong password (FR-AUTH-01). Returns:
 *   ['ok' => bool, 'user' => ?array, 'locked' => bool, 'must_change' => bool]
 */
function authenticate(string $username, string $password): array
{
    $result = ['ok' => false, 'user' => null, 'locked' => false, 'must_change' => false];

    $stmt = db()->prepare(
        'SELECT u.id, u.username, u.full_name, u.role, u.is_active,
                c.id AS credential_id, c.password_hash, c.must_change_password,
                c.failed_attempts, c.locked_until
         FROM users u
         JOIN credentials c ON c.user_id = u.id AND c.provider = \'credential\'
         WHERE u.username = :username'
    );
    $stmt->execute(['username' => $username]);
    $row = $stmt->fetch();

    if (!is_array($row) || !$row['is_active']) {
        record_auth_event('login_failure', $username);
        app_log('warning', 'Login gagal (akun tidak dikenal/nonaktif): ' . log_safe($username));

        return $result;
    }

    if ($row['locked_until'] !== null && strtotime((string) $row['locked_until']) > time()) {
        record_auth_event('locked', $username, (string) $row['id']);
        app_log('warning', 'Login ditolak (akun terkunci): ' . log_safe($username));

        $result['locked'] = true;

        return $result;
    }

    if (!password_verify($password, (string) $row['password_hash'])) {
        $failed = (int) $row['failed_attempts'] + 1;
        $pdo = db();
        $pdo->beginTransaction();

        try {
            if ($failed >= AUTH_MAX_FAILED_ATTEMPTS) {
                $pdo->prepare(
                    'UPDATE credentials
                     SET failed_attempts = :n, locked_until = now() + make_interval(mins => :m), updated_at = now()
                     WHERE id = :id'
                )->execute(['n' => $failed, 'm' => AUTH_LOCK_MINUTES, 'id' => $row['credential_id']]);
            } else {
                $pdo->prepare(
                    'UPDATE credentials SET failed_attempts = :n, updated_at = now() WHERE id = :id'
                )->execute(['n' => $failed, 'id' => $row['credential_id']]);
            }
            audit_log(
                'credentials',
                (string) $row['credential_id'],
                'UPDATE',
                (string) $row['id'],
                json_encode(['failed_attempts' => (int) $row['failed_attempts']]) ?: null,
                json_encode(['failed_attempts' => $failed]) ?: null,
            );
            record_auth_event('login_failure', $username, (string) $row['id']);
            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        app_log('warning', 'Login gagal (kata sandi salah): ' . log_safe($username));

        if ($failed >= AUTH_MAX_FAILED_ATTEMPTS) {
            $result['locked'] = true;
        }

        return $result;
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $params = ['id' => $row['credential_id']];
        $rehash = '';
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;

        if (password_needs_rehash((string) $row['password_hash'], $algo)) {
            $params['hash'] = password_hash($password, $algo);
            $rehash = ', password_hash = :hash';
        }

        $pdo->prepare(
            'UPDATE credentials
             SET failed_attempts = 0, locked_until = NULL, last_login_at = now(), updated_at = now()'
            . $rehash . ' WHERE id = :id'
        )->execute($params);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }

    $result['ok'] = true;
    record_auth_event('login_success', (string) $row['username'], (string) $row['id']);
    $result['user'] = [
        'id' => (string) $row['id'],
        'username' => (string) $row['username'],
        'full_name' => (string) $row['full_name'],
        'role' => (string) $row['role'],
    ];
    $result['must_change'] = (bool) $row['must_change_password'];

    return $result;
}

/**
 * Create the session for an authenticated user and audit it.
 * Returns the sessions-row id. Regenerates the token to prevent fixation.
 */
function login_user(array $user): string
{
    start_app_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];

    $stmt = db()->prepare(
        'INSERT INTO sessions (user_id, token_hash, ip_address, user_agent, expires_at)
         VALUES (:user, :hash, :ip, :ua, now() + make_interval(secs => :lifetime))
         RETURNING id'
    );
    $stmt->execute([
        'user' => $user['id'],
        'hash' => hash('sha256', session_id()),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'ua' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'lifetime' => SESSION_ABSOLUTE_SECONDS,
    ]);
    $sessionRowId = (string) $stmt->fetchColumn();

    audit_log('sessions', $sessionRowId, 'INSERT', $user['id']);

    return $sessionRowId;
}

/** Destroy the current session (row + cookie + data) and audit the logout. */
function logout_user(): void
{
    start_app_session();

    $sessionId = session_id();
    $rowId = null;
    $actorId = null;

    if ($sessionId !== '') {
        $sel = db()->prepare('SELECT id, user_id FROM sessions WHERE token_hash = :hash');
        $sel->execute(['hash' => hash('sha256', $sessionId)]);
        $found = $sel->fetch();

        if (is_array($found)) {
            $rowId = (string) $found['id'];
            $actorId = (string) $found['user_id'];
        }
    }

    if ($actorId !== null) {
        record_auth_event('logout', null, $actorId);
    }

    $_SESSION = [];

    if ($sessionId !== '') {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => 1,
            'path' => is_string($params['path'] ?? null) ? $params['path'] : '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => env('APP_ENV') === 'production',
        ]);
        session_destroy();
    }

    if ($rowId !== null) {
        audit_log('sessions', $rowId, 'DELETE', $actorId);
    }
}

/**
 * Resolve the logged-in user for the current request, or null.
 *
 * Validates the session row (exists, not expired, not idle), the account still
 * being active, refreshes last_seen_at, and publishes the actor for the audit
 * trail / future RLS (app.actor_id, app.actor_role) on this connection.
 */
function load_session_user(string $userId): ?array
{
    $sessionId = session_id();

    if ($sessionId === '') {
        return null;
    }

    $stmt = db()->prepare(
        'SELECT s.id AS session_row_id, s.last_seen_at, s.expires_at,
                u.id, u.username, u.full_name, u.role, u.is_active,
                c.must_change_password
         FROM sessions s
         JOIN users u ON u.id = s.user_id
         JOIN credentials c ON c.user_id = u.id AND c.provider = \'credential\'
         WHERE s.token_hash = :hash AND s.user_id = :user'
    );
    $stmt->execute(['hash' => hash('sha256', $sessionId), 'user' => $userId]);
    $row = $stmt->fetch();

    if (!is_array($row)) {
        clear_session_data();

        return null;
    }

    $expired = strtotime((string) $row['expires_at']) <= time();
    $idle = strtotime((string) $row['last_seen_at']) < time() - SESSION_IDLE_SECONDS;

    if ($expired || $idle || !$row['is_active']) {
        db()->prepare('DELETE FROM sessions WHERE id = :id')->execute(['id' => $row['session_row_id']]);
        clear_session_data();

        return null;
    }

    db()->prepare('UPDATE sessions SET last_seen_at = now() WHERE id = :id')
        ->execute(['id' => $row['session_row_id']]);

    $actor = db()->prepare("SELECT set_config('app.actor_id', :id, false), set_config('app.actor_role', :role, false)");
    $actor->execute(['id' => (string) $row['id'], 'role' => (string) $row['role']]);
    $actor->fetchColumn();

    return [
        'id' => (string) $row['id'],
        'username' => (string) $row['username'],
        'full_name' => (string) $row['full_name'],
        'role' => (string) $row['role'],
        'must_change' => (bool) $row['must_change_password'],
    ];
}

/** Drop in-memory session data and the cookie without touching audit. */
function clear_session_data(): void
{
    $_SESSION = [];

    if (session_id() !== '') {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => 1,
            'path' => is_string($params['path'] ?? null) ? $params['path'] : '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => env('APP_ENV') === 'production',
        ]);
        session_destroy();
    }
}

/** Where a role lands after login: admins to /admin, everyone else to /. */
function target_for_role(string $role): string
{
    return $role === 'admin' ? '/admin' : '/';
}

/**
 * Change a user's password. Returns ['ok' => bool, 'error' => ?string].
 * Also clears lockout state and revokes every *other* session.
 */
function change_password(string $userId, string $current, string $new): array
{
    $stmt = db()->prepare(
        'SELECT id, password_hash FROM credentials WHERE user_id = :user AND provider = \'credential\''
    );
    $stmt->execute(['user' => $userId]);
    $cred = $stmt->fetch();

    if (!is_array($cred) || !password_verify($current, (string) $cred['password_hash'])) {
        return ['ok' => false, 'error' => 'Kata sandi saat ini salah.'];
    }

    if (strlen($new) < AUTH_PASSWORD_MIN_LENGTH) {
        return ['ok' => false, 'error' => 'Kata sandi baru minimal 8 karakter.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $pdo->prepare(
            'UPDATE credentials
             SET password_hash = :hash, must_change_password = FALSE,
                 failed_attempts = 0, locked_until = NULL, updated_at = now()
             WHERE id = :id'
        )->execute(['hash' => hash_password($new), 'id' => $cred['id']]);
        audit_log(
            'credentials',
            (string) $cred['id'],
            'UPDATE',
            $userId,
            json_encode(['must_change_password' => true]) ?: null,
            json_encode(['must_change_password' => false]) ?: null,
        );
        record_auth_event('password_change', null, $userId);

        $currentRow = null;

        if (session_id() !== '') {
            $sel = $pdo->prepare('SELECT id FROM sessions WHERE token_hash = :hash');
            $sel->execute(['hash' => hash('sha256', session_id())]);
            $found = $sel->fetch();
            $currentRow = is_array($found) ? (string) $found['id'] : null;
        }

        if ($currentRow === null) {
            $pdo->prepare('DELETE FROM sessions WHERE user_id = :user')->execute(['user' => $userId]);
        } else {
            $pdo->prepare('DELETE FROM sessions WHERE user_id = :user AND id != :keep')
                ->execute(['user' => $userId, 'keep' => $currentRow]);
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}

/** All accounts, newest-relevant fields for the admin overview. */
function list_users(): array
{
    return db()->query(
        'SELECT u.id, u.username, u.full_name, u.role, u.is_active, u.created_at,
                c.must_change_password, c.failed_attempts,
                (c.locked_until IS NOT NULL AND c.locked_until > now()) AS locked
         FROM users u
         JOIN credentials c ON c.user_id = u.id AND c.provider = \'credential\'
         ORDER BY u.created_at'
    )->fetchAll();
}

/**
 * Create an account (admin only). Returns ['ok' => bool, 'error' => ?string].
 * New accounts must change their password on first login.
 */
function create_user(string $username, string $fullName, string $role, string $password, string $actorId): array
{
    $username = trim($username);
    $fullName = trim($fullName);

    if ($username === '' || $fullName === '') {
        return ['ok' => false, 'error' => 'Nama pengguna dan nama lengkap wajib diisi.'];
    }

    if ($role !== 'admin' && $role !== 'perawat') {
        return ['ok' => false, 'error' => 'Peran tidak dikenal.'];
    }

    if (strlen($password) < AUTH_PASSWORD_MIN_LENGTH) {
        return ['ok' => false, 'error' => 'Kata sandi minimal 8 karakter.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $ins = $pdo->prepare(
            'INSERT INTO users (username, full_name, role) VALUES (:u, :n, :r) RETURNING id'
        );
        $ins->execute(['u' => $username, 'n' => $fullName, 'r' => $role]);
        $userId = (string) $ins->fetchColumn();

        $pdo->prepare(
            'INSERT INTO credentials (user_id, password_hash, must_change_password)
             VALUES (:user, :hash, TRUE)'
        )->execute(['user' => $userId, 'hash' => hash_password($password)]);

        audit_log('users', $userId, 'INSERT', $actorId, null, json_encode(['username' => $username, 'role' => $role]) ?: null);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();

        if ($exception instanceof PDOException && $exception->getCode() === '23505') {
            return ['ok' => false, 'error' => 'Nama pengguna sudah dipakai.'];
        }

        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}

/** Enable/disable an account. Returns false when nothing changed. */
function set_user_active(string $userId, bool $active, string $actorId): bool
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $sel = $pdo->prepare('SELECT is_active FROM users WHERE id = :id');
        $sel->execute(['id' => $userId]);
        $old = $sel->fetchColumn();

        if ($old === false) {
            $pdo->rollBack();

            return false;
        }

        $pdo->prepare('UPDATE users SET is_active = :active WHERE id = :id')
            ->execute(['active' => $active, 'id' => $userId]);
        audit_log(
            'users',
            $userId,
            'UPDATE',
            $actorId,
            json_encode(['is_active' => (bool) $old]) ?: null,
            json_encode(['is_active' => $active]) ?: null,
        );
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }

    return true;
}

/**
 * Reset a password to one supplied by the admin. Returns ['ok' => bool, 'error' => ?string].
 * The account must change the password on the next login.
 */
function reset_user_password(string $userId, string $newPassword, string $actorId): array
{
    if (strlen($newPassword) < AUTH_PASSWORD_MIN_LENGTH) {
        return ['ok' => false, 'error' => 'Kata sandi minimal 8 karakter.'];
    }

    $pdo = db();
    $pdo->beginTransaction();

    try {
        $sel = $pdo->prepare('SELECT id FROM credentials WHERE user_id = :user AND provider = \'credential\'');
        $sel->execute(['user' => $userId]);
        $cred = $sel->fetch();

        if (!is_array($cred)) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'Kredensial tidak ditemukan.'];
        }

        $pdo->prepare(
            'UPDATE credentials
             SET password_hash = :hash, must_change_password = TRUE,
                 failed_attempts = 0, locked_until = NULL, updated_at = now()
             WHERE id = :id'
        )->execute(['hash' => hash_password($newPassword), 'id' => $cred['id']]);
        audit_log('credentials', (string) $cred['id'], 'UPDATE', $actorId);
        record_auth_event('password_reset', null, $userId);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }

    return ['ok' => true, 'error' => null];
}

/** Active sessions (non-expired), newest activity first. */
function list_sessions(): array
{
    return db()->query(
        'SELECT s.id, s.user_id, u.username, u.role, s.ip_address, s.user_agent,
                s.created_at, s.last_seen_at, s.expires_at
         FROM sessions s
         JOIN users u ON u.id = s.user_id
         WHERE s.expires_at > now()
         ORDER BY s.last_seen_at DESC'
    )->fetchAll();
}

/** Revoke one session by row id. Returns false when it did not exist. */
function revoke_session(string $sessionRowId, string $actorId): bool
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $sel = $pdo->prepare('SELECT user_id FROM sessions WHERE id = :id');
        $sel->execute(['id' => $sessionRowId]);
        $found = $sel->fetch();

        if (!is_array($found)) {
            $pdo->rollBack();

            return false;
        }

        $pdo->prepare('DELETE FROM sessions WHERE id = :id')->execute(['id' => $sessionRowId]);
        audit_log('sessions', $sessionRowId, 'DELETE', $actorId);
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }

    return true;
}
