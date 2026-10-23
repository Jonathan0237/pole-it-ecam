<?php

declare(strict_types=1);

namespace PoleIt;

use PDO;

/** Connexion PDO SQLite unique, schéma créé automatiquement au premier accès. */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::open(getenv('DB_PATH') ?: DATA_DIR . '/pole-it.sqlite');
        }
        return self::$pdo;
    }

    /** Remplace la connexion (utilisé par les tests avec ':memory:'). */
    public static function use(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public static function open(string $path): PDO
    {
        if ($path !== ':memory:' && !is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }
        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA journal_mode = WAL; PRAGMA foreign_keys = ON;');
        self::migrate($pdo);
        return $pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS messages (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                nom         TEXT NOT NULL,
                email       TEXT NOT NULL,
                sujet       TEXT NOT NULL,
                message     TEXT NOT NULL,
                lu          INTEGER NOT NULL DEFAULT 0,
                created_at  TEXT NOT NULL DEFAULT (datetime('now'))
            );
            CREATE TABLE IF NOT EXISTS admissions (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                prenom      TEXT NOT NULL,
                nom         TEXT NOT NULL,
                email       TEXT NOT NULL,
                telephone   TEXT NOT NULL,
                adresse     TEXT NOT NULL,
                code_postal TEXT NOT NULL,
                ville       TEXT NOT NULL,
                niveau      TEXT NOT NULL,
                programme   TEXT NOT NULL,
                motivation  TEXT NOT NULL,
                lu          INTEGER NOT NULL DEFAULT 0,
                created_at  TEXT NOT NULL DEFAULT (datetime('now'))
            );
            CREATE TABLE IF NOT EXISTS stages (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                prenom      TEXT NOT NULL,
                nom         TEXT NOT NULL,
                age         INTEGER NOT NULL,
                email       TEXT NOT NULL,
                telephone   TEXT NOT NULL,
                ville       TEXT NOT NULL,
                pays        TEXT NOT NULL,
                domaine     TEXT NOT NULL,
                date_debut  TEXT NOT NULL,
                duree       INTEGER NOT NULL,
                reference   TEXT NOT NULL UNIQUE,
                lu          INTEGER NOT NULL DEFAULT 0,
                created_at  TEXT NOT NULL DEFAULT (datetime('now'))
            );
        SQL);
    }
}
