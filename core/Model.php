<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

abstract class Model
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::get();
    }

    protected function run(string $sql, array $params = []): \PDOStatement
    {
        $stm = $this->db->prepare($sql);
        $stm->execute($params);
        return $stm;
    }

    protected function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    protected function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    protected function lastId(): string
    {
        return $this->db->lastInsertId();
    }
}
