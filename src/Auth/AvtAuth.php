<?php
namespace Avetify\Auth;

use Avetify\DB\DBConnection;
use Avetify\Routing\Routing;
use Throwable;

/**
 * Class AvtAuth
 *
 * This class handles user authentication and token management.
 *
 * Requirements:
 * The following database tables must exist:
 *
 * Table: users
 * - id INT(11) PRIMARY KEY
 * - username VARCHAR(255)
 * - password VARCHAR(255)
 *
 * Table: tokens
 * - id INT(11) PRIMARY KEY (Optional)
 * - user_id INT(11) (FOREIGN KEY -> users.id)
 * - token_hash VARCHAR(255)
 * - expires_at DATETIME
 *
 * Notes:
 * - Passwords should be stored using password_hash()
 * - token_hash should NOT store raw tokens
 */
class AvtAuth {
    public int $minUsernameLen = 4;
    public int $minPasswordLen = 6;
    public int $tokenLifetimeDays = 30;
    public string $usersUsernameCol = "username";
    public string $usersPasswordCol = "password";
    public string $tokensHashCol = "token_hash";
    public string $tokensExpiresAtCol = "expires_at";

    public function __construct(
        public string $appId,
        public string $usersTable,
        public string $usersPk,
        public string $tokensTable,
        public string $tokensUserFk
    ) {}

    public function startSession(): void {
        $isHttps = Routing::isHttpsRequest();

        // Avoid collisions with pre-existing Secure cookies when serving over HTTP.
        // Browsers may reject setting a non-Secure cookie if a Secure cookie with the
        // same name already exists for this site.
        if (!$isHttps) {
            session_name(strtoupper($this->appId) . '_SESSID_HTTP');
        }

        session_set_cookie_params([
            'lifetime' => 60 * 60 * 24 * $this->tokenLifetimeDays,
            'path' => '/',
            // IMPORTANT: must match the scheme consistently, otherwise browsers may reject
            // overwriting an existing Secure PHPSESSID cookie (causing login loops).
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    public function sessionKey(string $name): string {
        return $this->appId . '_' . $name;
    }

    public function isLoggedIn(): bool {
        return !empty($this->currentUserId());
    }

    function currentUserId(): string|null {
        return $_SESSION[$this->sessionKey('user_id')] ?? null;
    }

    public function currentUsername(): string {
        return (string)($_SESSION[$this->sessionKey('username')] ?? '');
    }

    public function requireLogin(DBConnection $conn, string $redirectTo = 'login.php'): void {
        $this->bootstrapRememberMe($conn);
        if (!$this->isLoggedIn()) {
            header("Location: {$redirectTo}");
            exit;
        }
    }

    public function login(DBConnection $conn, string $username, string $password): array {
        $username = trim($username);
        $password = (string)$password;
        if ($username === '' || $password === '') {
            return ['ok' => false, 'error' => 'missing_fields'];
        }

        $sql = "SELECT {$this->usersPk} AS id, {$this->usersPasswordCol} AS password FROM {$this->usersTable} WHERE {$this->usersUsernameCol} = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return ['ok' => false, 'error' => 'server_error'];
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result ? $result->fetch_assoc() : null;

        if (!$user || empty($user['id'])) {
            return ['ok' => false, 'error' => 'username_not_found'];
        }
        if (!password_verify($password, (string)($user['password'] ?? ''))) {
            return ['ok' => false, 'error' => 'wrong_password'];
        }

        $userId = $user['id'];
        $this->startAuthSession($userId, $username);
        $this->issueRememberMeToken($conn, $userId);
        session_write_close();

        return ['ok' => true, 'user' => ['id' => $userId, 'username' => $username]];
    }

    public function register(DBConnection $conn, string $username, string $password): array {
        $username = trim($username);
        $password = (string)$password;
        if ($username === '' || $password === '') {
            return ['ok' => false, 'error' => 'missing_fields'];
        }
        if (strlen($username) < $this->minUsernameLen) {
            return ['ok' => false, 'error' => 'username_too_short'];
        }
        if (strlen($password) < $this->minPasswordLen) {
            return ['ok' => false, 'error' => 'password_too_short'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO {$this->usersTable} ({$this->usersUsernameCol}, {$this->usersPasswordCol}) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return ['ok' => false, 'error' => 'server_error'];
        $stmt->bind_param("ss", $username, $hash);

        if (!$stmt->execute()) {
            return ['ok' => false, 'error' => 'username_exists'];
        }

        $userId = $stmt->insert_id;
        if ($userId <= 0) return ['ok' => false, 'error' => 'server_error'];

        $this->startAuthSession($userId, $username);
        $this->issueRememberMeToken($conn, $userId);
        session_write_close();

        return ['ok' => true, 'user' => ['id' => $userId, 'username' => $username]];
    }

    public function changePassword(DBConnection $conn, string $userId, string $currentPassword, string $newPassword): array {
        $currentPassword = trim((string)$currentPassword);
        $newPassword = trim((string)$newPassword);

        if ($userId <= 0) return ['ok' => false, 'error' => 'unauthorized'];
        if ($currentPassword === '' || $newPassword === '') return ['ok' => false, 'error' => 'missing_fields'];
        if (strlen($newPassword) < $this->minPasswordLen) return ['ok' => false, 'error' => 'password_too_short'];

        $sql = "SELECT {$this->usersPasswordCol} AS password FROM {$this->usersTable} WHERE {$this->usersPk} = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return ['ok' => false, 'error' => 'server_error'];
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        if (!$row || empty($row['password'])) return ['ok' => false, 'error' => 'user_not_found'];

        if (!password_verify($currentPassword, (string)$row['password'])) {
            return ['ok' => false, 'error' => 'wrong_password'];
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updSql = "UPDATE {$this->usersTable} SET {$this->usersPasswordCol} = ? WHERE {$this->usersPk} = ? LIMIT 1";
        $upd = $conn->prepare($updSql);
        if (!$upd) return ['ok' => false, 'error' => 'server_error'];
        $upd->bind_param("si", $hash, $userId);
        if (!$upd->execute()) return ['ok' => false, 'error' => 'server_error'];

        return ['ok' => true];
    }

    public function bootstrapRememberMe(DBConnection $conn): void {
        if ($this->isLoggedIn()) return;

        $cookieName = $this->rememberMeCookieName();
        if (empty($_COOKIE[$cookieName])) return;

        $token = trim((string)$_COOKIE[$cookieName]);
        if ($token === '') {
            $this->clearRememberMeCookie();
            return;
        }

        $tokenHash = hash('sha256', $token);

        try {
            $sql = "
                SELECT ut.{$this->tokensUserFk} AS user_id, u.{$this->usersUsernameCol} AS username
                FROM {$this->tokensTable} ut
                INNER JOIN {$this->usersTable} u ON u.{$this->usersPk} = ut.{$this->tokensUserFk}
                WHERE ut.{$this->tokensHashCol} = ?
                  AND ut.{$this->tokensExpiresAtCol} > NOW()
                LIMIT 1
            ";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $this->clearRememberMeCookie();
                return;
            }
            $stmt->bind_param("s", $tokenHash);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            if (!$row || empty($row['user_id'])) {
                $this->clearRememberMeCookie();
                return;
            }

            $userId = $row['user_id'];
            $username = (string)($row['username'] ?? '');

            $this->revokeRememberMeToken($conn, $token);

            $this->startAuthSession($userId, $username);
            $this->issueRememberMeToken($conn, $userId);
        } catch (Throwable $_) {
            $this->clearRememberMeCookie();
            return;
        }
    }

    public function logout(DBConnection $conn, string $redirectTo = 'login.php'): void {
        try {
            $rm = $_COOKIE['remember_me'] ?? ($_COOKIE['remember_me_http'] ?? null);
            if (!empty($rm)) {
                $this->revokeRememberMeToken($conn, (string)$rm);
            }
        } catch (Throwable $_) {
        }

        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_destroy();
        }

        $this->clearSessionCookies();
        $this->clearRememberMeCookie();

        header("Location: {$redirectTo}");
        exit;
    }

    private function startAuthSession(int $userId, string $username): void {
        session_regenerate_id(true);
        $_SESSION[$this->sessionKey('user_id')] = $userId;
        $_SESSION[$this->sessionKey('username')] = (string)$username;
    }

    private function rememberMeCookieName(): string {
        return Routing::isHttpsRequest() ? 'remember_me' : 'remember_me_http';
    }

    private function authCookieBaseParams(): array {
        $sessionParams = function_exists('session_get_cookie_params') ? session_get_cookie_params() : [];
        return [
            'path' => (string)($sessionParams['path'] ?? '/'),
            'httponly' => (bool)($sessionParams['httponly'] ?? true),
            'secure' => (bool)($sessionParams['secure'] ?? false),
            'samesite' => (string)($sessionParams['samesite'] ?? 'Lax'),
        ];
    }

    private function authCookieParams(): array {
        return [
            'expires' => time() + (60 * 60 * 24 * $this->tokenLifetimeDays),
            ...$this->authCookieBaseParams(),
        ];
    }

    private function setRememberMeCookie(string $token): void {
        setcookie($this->rememberMeCookieName(), $token, $this->authCookieParams());
    }

    private function clearRememberMeCookie(): void {
        $base = $this->authCookieBaseParams();
        $names = array_values(array_unique([$this->rememberMeCookieName(), 'remember_me', 'remember_me_http']));
        foreach ($names as $name) {
            setcookie($name, '', ['expires' => time() - 3600, ...$base]);
            setcookie($name, '', ['expires' => time() - 3600, ...$base, 'secure' => true]);
            setcookie($name, '', ['expires' => time() - 3600, ...$base, 'secure' => false]);
        }
    }

    private function issueRememberMeToken($conn, int $userId): ?string {
        if ($userId <= 0) return null;
        try {
            $token = bin2hex(random_bytes(32));
        } catch (Throwable $_) {
            return null;
        }
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + ($this->tokenLifetimeDays * 86400));

        $sql = "
            INSERT INTO {$this->tokensTable} ({$this->tokensUserFk}, {$this->tokensHashCol}, {$this->tokensExpiresAtCol})
            VALUES (?, ?, ?)
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param("iss", $userId, $tokenHash, $expiresAt);
        if (!$stmt->execute()) return null;

        $this->setRememberMeCookie($token);
        return $token;
    }

    private function revokeRememberMeToken($conn, string $rawToken): void {
        $rawToken = trim($rawToken);
        if ($rawToken === '') return;
        $tokenHash = hash('sha256', $rawToken);

        $sql = "DELETE FROM {$this->tokensTable} WHERE {$this->tokensHashCol} = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return;
        $stmt->bind_param("s", $tokenHash);
        $stmt->execute();
    }

    private function clearSessionCookies(): void {
        $cookieName = session_name();
        $cookieNames = array_values(array_unique([$cookieName, 'PHPSESSID', 'TEXTERSESSID_HTTP']));
        foreach ($cookieNames as $n) {
            setcookie($n, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure' => true,
            ]);
            setcookie($n, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure' => false,
            ]);
        }
    }
}

