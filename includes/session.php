<?php
// includes/session.php — session + auth helpers
require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    public static function attempt(string $email, string $password): bool {
        $user = Database::fetchOne(
            "SELECT * FROM users WHERE email = ? AND status = 'active'",
            [$email]
        );
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            return true;
        }
        return false;
    }

    public static function logout(): void {
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function role(): ?string {
        return $_SESSION['role'] ?? null;
    }

    public static function user(): ?array {
        if (!self::check()) return null;
        return [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'full_name' => $_SESSION['full_name'],
        ];
    }

    // Call at the top of any protected page. $allowedRoles = ['admin'] or ['admin','editor'] etc.
    public static function require(array $allowedRoles, string $loginPath = 'login.php'): void {
        if (!self::check() || !in_array(self::role(), $allowedRoles, true)) {
            header('Location: ' . $loginPath);
            exit;
        }
    }

    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfCheck(?string $token): bool {
        return $token !== null && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
