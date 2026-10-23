<?php

declare(strict_types=1);

/*
 * Mini lanceur de tests sans dépendance : php tests/run.php
 * Chaque fichier tests/*Test.php appelle test('nom', fn () => ...).
 */

putenv('DB_PATH=:memory:');
require dirname(__DIR__) . '/src/bootstrap.php';

$tests = [];
$failures = [];

function test(string $name, callable $fn): void
{
    global $tests;
    $tests[$name] = $fn;
}

function assert_true(bool $condition, string $message = 'assertion échouée'): void
{
    if (!$condition) {
        throw new AssertionError($message);
    }
}

function assert_same(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        throw new AssertionError(trim($message . ' attendu ' . var_export($expected, true) . ', obtenu ' . var_export($actual, true)));
    }
}

foreach (glob(__DIR__ . '/*Test.php') as $file) {
    require $file;
}

foreach ($tests as $name => $fn) {
    $_SESSION = [];
    \PoleIt\Database::use(\PoleIt\Database::open(':memory:'));
    try {
        $fn();
        echo "\033[32m✔\033[0m {$name}\n";
    } catch (Throwable $e) {
        $failures[] = $name;
        echo "\033[31m✘ {$name}\033[0m\n    " . $e->getMessage() . "\n";
    }
}

printf("\n%d test(s), %d échec(s)\n", count($tests), count($failures));
exit($failures ? 1 : 0);
