<?php
declare(strict_types=1);

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';

    protected function db(): PDO
    {
        return Database::pdo();
    }

    public function find(int|string $id): ?array
    {
        $st = $this->db()->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1"
        );
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public function all(int $limit = 100, int $offset = 0): array
    {
        $st = $this->db()->prepare(
            "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC LIMIT :l OFFSET :o"
        );
        $st->bindValue(':l', $limit, PDO::PARAM_INT);
        $st->bindValue(':o', $offset, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll();
    }

    public function delete(int|string $id): bool
    {
        $st = $this->db()->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id"
        );
        return $st->execute(['id' => $id]);
    }
}
