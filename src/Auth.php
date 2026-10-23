<?php

declare(strict_types=1);

namespace PoleIt;

/**
 * Authentification de l'espace admin.
 * Identifiants lus dans les variables d'environnement ADMIN_USER et
 * ADMIN_PASSWORD_HASH (généré avec password_hash). En mode démo, et
 * seulement si aucun hash n'est fourni, le compte demo / demo est accepté.
 */
final class Auth
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 300;

    public static function attempt(string $user, string $password): bool
    {
        if (self::isLocked()) {
            return false;
        }

        $expectedUser = getenv('ADMIN_USER') ?: 'demo';
        $hash = getenv('ADMIN_PASSWORD_HASH') ?: (DEMO_MODE ? password_hash('demo', PASSWORD_DEFAULT) : '');

        $ok = $hash !== '' && hash_equals($expectedUser, $user) && password_verify($password, $hash);

        if ($ok) {
            session_regenerate_id(true);
            $_SESSION['admin'] = $user;
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
            return true;
        }

        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= self::MAX_ATTEMPTS) {
            $_SESSION['login_locked_until'] = time() + self::LOCK_SECONDS;
            $_SESSION['login_attempts'] = 0;
        }
        return false;
    }

    public static function isLocked(): bool
    {
        return ($_SESSION['login_locked_until'] ?? 0) > time();
    }

    public static function check(): bool
    {
        return !empty($_SESSION['admin']);
    }

    public static function require(): void
    {
        if (!self::check()) {
            redirect('/admin/login.php');
        }
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
