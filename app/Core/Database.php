<?php
namespace App\Core;

use PDO;

class Database
{
    private $config;
    private $pdo;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function pdo()
    {
        if ($this->pdo instanceof PDO) return $this->pdo;

        $host = isset($this->config['host']) ? $this->config['host'] : 'localhost';
        $name = isset($this->config['name']) ? $this->config['name'] : '';
        $charset = isset($this->config['charset']) ? $this->config['charset'] : 'utf8mb4';
        $dsn = 'mysql:host=' . $host . ';dbname=' . $name . ';charset=' . $charset;

        $this->pdo = new PDO(
            $dsn,
            isset($this->config['username']) ? $this->config['username'] : '',
            isset($this->config['password']) ? $this->config['password'] : '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );
        return $this->pdo;
    }

    public function statement($sql, array $bindings = [])
    {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute(array_values($bindings));
        return $stmt;
    }

    public function fetch($sql, array $bindings = [])
    {
        $row = $this->statement($sql, $bindings)->fetch();
        return $row === false ? null : $row;
    }

    public function fetchAll($sql, array $bindings = [])
    {
        return $this->statement($sql, $bindings)->fetchAll();
    }

    public function value($sql, array $bindings = [])
    {
        return $this->statement($sql, $bindings)->fetchColumn();
    }

    public function insert($table, array $data, $timestamps = true)
    {
        $this->assertIdentifier($table);
        if (empty($data)) throw new \InvalidArgumentException('Insert data cannot be empty.');

        $fields = array_keys($data);
        foreach ($fields as $field) $this->assertIdentifier($field);

        $columns = $fields;
        $placeholders = array_fill(0, count($fields), '?');
        if ($timestamps) {
            $columns[] = 'created_at';
            $placeholders[] = 'NOW()';
        }

        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $this->statement($sql, array_values($data));
        return $this->pdo()->lastInsertId();
    }

    public function updateById($table, $id, array $data, $touch = true)
    {
        $this->assertIdentifier($table);
        if (empty($data)) return true;

        $parts = [];
        foreach ($data as $field => $value) {
            $this->assertIdentifier($field);
            $parts[] = $field . ' = ?';
        }
        if ($touch) $parts[] = 'updated_at = NOW()';

        $bindings = array_values($data);
        $bindings[] = $id;
        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $parts) . ' WHERE id = ?';
        $this->statement($sql, $bindings);
        return true;
    }

    public function deleteById($table, $id)
    {
        $this->assertIdentifier($table);
        return $this->statement('DELETE FROM ' . $table . ' WHERE id = ?', [$id])->rowCount() > 0;
    }

    public function find($table, $id)
    {
        $this->assertIdentifier($table);
        return $this->fetch('SELECT * FROM ' . $table . ' WHERE id = ? LIMIT 1', [$id]);
    }

    public function last($table)
    {
        $this->assertIdentifier($table);
        return $this->fetch('SELECT * FROM ' . $table . ' ORDER BY id DESC LIMIT 1');
    }

    public function count($table, $whereSql = '', array $bindings = [])
    {
        $this->assertIdentifier($table);
        $sql = 'SELECT COUNT(*) FROM ' . $table;
        if ($whereSql !== '') $sql .= ' WHERE ' . $whereSql;
        return (int) $this->value($sql, $bindings);
    }

    public function transaction(callable $callback)
    {
        $pdo = $this->pdo();
        $pdo->beginTransaction();
        try {
            $result = call_user_func($callback, $this);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    private function assertIdentifier($identifier)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $identifier)) {
            throw new \InvalidArgumentException('Invalid SQL identifier.');
        }
    }
}
