<?php

declare(strict_types=1);

/*
 * Point d'entrée commun à toutes les pages : configuration, session,
 * en-têtes de sécurité et chargement des classes.
 */

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', getenv('DATA_DIR') ?: ROOT_DIR . '/data');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('DEMO_MODE', filter_var(getenv('DEMO_MODE') ?: 'true', FILTER_VALIDATE_BOOL));

spl_autoload_register(static function (string $class): void {
    $prefix = 'PoleIt\\';
    if (str_starts_with($class, $prefix)) {
        $file = __DIR__ . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

require __DIR__ . '/helpers.php';

if (PHP_SAPI !== 'cli') {
    $https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

    session_name('poleit_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; media-src 'self'; "
        . "style-src 'self' https://fonts.googleapis.com; "
        . "font-src 'self' https://fonts.gstatic.com; script-src 'self'; "
        . "form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
