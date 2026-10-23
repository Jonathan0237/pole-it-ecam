<?php

declare(strict_types=1);

namespace PoleIt;

use InvalidArgumentException;
use PDO;

/**
 * Accès aux trois tables de demandes (messages, admissions, stages).
 * Toutes les requêtes sont préparées ; les noms de tables et de colonnes
 * proviennent exclusivement de la liste blanche ci-dessous.
 */
final class Repository
{
    public const TABLES = [
        'messages'   => ['nom', 'email', 'sujet', 'message'],
        'admissions' => ['prenom', 'nom', 'email', 'telephone', 'adresse', 'code_postal', 'ville', 'niveau', 'programme', 'motivation'],
        'stages'     => ['prenom', 'nom', 'age', 'email', 'telephone', 'ville', 'pays', 'domaine', 'date_debut', 'duree', 'reference'],
    ];

    private readonly string $table;

    public function __construct(string $table, private readonly ?PDO $pdo = null)
    {
        if (!isset(self::TABLES[$table])) {
            throw new InvalidArgumentException("Table inconnue : {$table}");
        }
        $this->table = $table;
    }

    public function insert(array $data): int
    {
        $columns = self::TABLES[$this->table];
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', array_map(static fn (string $c): string => ':' . $c, $columns))
        );
        $stmt = $this->db()->prepare($sql);
        foreach ($columns as $column) {
            $stmt->bindValue(':' . $column, $data[$column] ?? '');
        }
        $stmt->execute();
        return (int) $this->db()->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBy(string $column, string $value): ?array
    {
        if (!in_array($column, self::TABLES[$this->table], true)) {
            throw new InvalidArgumentException("Colonne inconnue : {$column}");
        }
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetch() ?: null;
    }

    /** Liste paginée, filtrable par mot-clé sur nom / prénom / e-mail. */
    public function list(string $search = '', int $limit = 20, int $offset = 0): array
    {
        [$where, $params] = $this->searchClause($search);
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} {$where} ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?");
        foreach ($params as $i => $param) {
            $stmt->bindValue($i + 1, $param);
        }
        $stmt->bindValue(count($params) + 1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int
    {
        [$where, $params] = $this->searchClause($search);
        $stmt = $this->db()->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function countUnread(): int
    {
        return (int) $this->db()->query("SELECT COUNT(*) FROM {$this->table} WHERE lu = 0")->fetchColumn();
    }

    public function markRead(int $id): void
    {
        $this->db()->prepare("UPDATE {$this->table} SET lu = 1 WHERE id = ?")->execute([$id]);
    }

    public function delete(int $id): void
    {
        $this->db()->prepare("DELETE FROM {$this->table} WHERE id = ?")->execute([$id]);
    }

    /** @return list<array> toutes les lignes, pour l'export CSV */
    public function all(): array
    {
        return $this->db()->query("SELECT * FROM {$this->table} ORDER BY id")->fetchAll();
    }

    private function searchClause(string $search): array
    {
        $search = trim($search);
        if ($search === '') {
            return ['', []];
        }
        $columns = array_values(array_intersect(['nom', 'prenom', 'email'], self::TABLES[$this->table]));
        $like = '%' . addcslashes($search, '%_\\') . '%';
        $conditions = array_map(static fn (string $c): string => "{$c} LIKE ? ESCAPE '\\'", $columns);
        return ['WHERE ' . implode(' OR ', $conditions), array_fill(0, count($columns), $like)];
    }

    private function db(): PDO
    {
        return $this->pdo ?? Database::connection();
    }
}
