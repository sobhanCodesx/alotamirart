<?php
/**
 * Backward-compatible database facade.
 *
 * New code uses App\Core\Database directly. This class remains so old views or
 * legacy files cannot accidentally create a second database configuration path.
 */
class DataBase
{
    private $db;

    public function __construct()
    {
        if (!class_exists(\App\Core\Database::class)) {
            throw new RuntimeException('Application bootstrap must be loaded before DataBase.');
        }

        $this->db = new \App\Core\Database(\App\Core\Config::load('database'));
    }

    public function select($sql, $values = [])
    {
        return $this->db->statement($sql, $this->values($values));
    }

    public function selectOne($sql, $values = [])
    {
        return $this->db->fetch($sql, $this->values($values));
    }

    public function selectAll($sql, $values = [])
    {
        return $this->db->fetchAll($sql, $this->values($values));
    }

    public function new_select($fields, $tableName, $where, $value)
    {
        $this->identifier($tableName);
        $this->identifier($where);
        return $this->db->fetch(
            'SELECT ' . $fields . ' FROM ' . $tableName . ' WHERE ' . $where . ' = ? LIMIT 1',
            [$value]
        );
    }

    public function all($sql, $values = [])
    {
        return $this->db->fetchAll($sql, $this->values($values));
    }

    public function insert($tableName, $fields, $values)
    {
        $data = [];
        $values = array_values($values);
        foreach (array_values($fields) as $index => $field) {
            $data[$field] = array_key_exists($index, $values) ? $values[$index] : null;
        }
        return $this->db->insert($tableName, $data);
    }

    public function update($tableName, $id, $fields, $values)
    {
        $data = [];
        $values = array_values($values);
        foreach (array_values($fields) as $index => $field) {
            $data[$field] = array_key_exists($index, $values) ? $values[$index] : null;
        }
        return $this->db->updateById($tableName, $id, $data);
    }

    public function delete($tableName, $id)
    {
        return $this->db->deleteById($tableName, $id);
    }

    public function query($sql, $values = [])
    {
        return $this->db->statement($sql, $this->values($values));
    }

    public function getLastInsert($tableName)
    {
        return $this->db->last($tableName);
    }

    public function count($tableName, $where = '', $value = null)
    {
        return $where === ''
            ? $this->db->count($tableName)
            : $this->db->count($tableName, $where . ' = ?', [$value]);
    }

    private function values($values)
    {
        if ($values === null || $values === '') return [];
        return is_array($values) ? array_values($values) : [$values];
    }

    private function identifier($value)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $value)) {
            throw new InvalidArgumentException('Invalid SQL identifier.');
        }
    }
}
