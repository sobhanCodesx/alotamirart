<?php

class DataBase
{
    private $conn;
    private $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    );
    private static $sharedConn;

    public function __construct()
    {
        if (self::$sharedConn instanceof PDO) {
            $this->conn = self::$sharedConn;
            return;
        }

        $config = require __DIR__ . '/../config/database.php';
        $database = $config['primary'];

        try {
            $this->conn = new PDO(
                'mysql:host=' . $database['host'] . ';dbname=' . $database['name'],
                $database['username'],
                $database['password'],
                $this->option
            );
            self::$sharedConn = $this->conn;
        } catch (PDOException $e) {
            die("❌ خطا در اتصال به دیتابیس: " . $e->getMessage());
        }
    }

    public function select($sql, $values = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            if (!is_array($values)) $values = [$values];
            $stmt->execute($values);
            return $stmt;
        } catch (PDOException $e) {
            echo "❌ خطا در SELECT: " . $e->getMessage();
            return false;
        }
    }

    // ===== ✅ متد selectOne (جدید) =====
    public function selectOne($sql, $values = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            if (!is_array($values)) $values = [$values];
            $stmt->execute($values);
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo "❌ خطا در SELECT ONE: " . $e->getMessage();
            return false;
        }
    }

    public function selectAll($sql, $values = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            if (!is_array($values)) $values = [$values];
            $stmt->execute($values);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "❌ خطا در SELECT ALL: " . $e->getMessage();
            return false;
        }
    }

    public function new_select($fields, $tableName, $where, $value)
    {
        try {
            $stmt = $this->conn->prepare("SELECT {$fields} FROM {$tableName} WHERE {$where} = ?");
            $stmt->execute([$value]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo "❌ خطا در NEW_SELECT: " . $e->getMessage();
            return false;
        }
    }

    public function all($sql, $values = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "❌ خطا در ALL: " . $e->getMessage();
            return false;
        }
    }

    public function insert($tableName, $fields, $values)
    {
        try {
            $placeholders = ':' . implode(', :', $fields);
            $sql = "INSERT INTO " . $tableName . " (" . implode(', ', $fields) . ", created_at) 
                    VALUES (" . $placeholders . ", now())";
            $stmt = $this->conn->prepare($sql);
            $data = array_combine($fields, $values);
            $stmt->execute($data);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            echo "❌ خطا در INSERT: " . $e->getMessage();
            return false;
        }
    }

    public function update($tableName, $id, $fields, $values)
    {
        try {
            $sql = "UPDATE " . $tableName . " SET ";
            $setParts = [];
            $data = [];
            foreach ($fields as $index => $field) {
                $setParts[] = "`$field` = ?";
                $data[] = $values[$index];
            }
            $sql .= implode(', ', $setParts);
            $sql .= ", updated_at = now() WHERE id = ?";
            $data[] = $id;
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "❌ خطا در UPDATE: " . $e->getMessage();
            return false;
        }
    }

    public function delete($tableName, $id)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM " . $tableName . " WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            echo "❌ خطا در DELETE: " . $e->getMessage();
            return false;
        }
    }

    public function query($sql, $values = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            if (!is_array($values)) $values = [$values];
            $stmt->execute($values);
            return $stmt;
        } catch (PDOException $e) {
            echo "❌ خطا در QUERY: " . $e->getMessage();
            return false;
        }
    }

    public function getLastInsert($tableName)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM " . $tableName . " ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo "❌ خطا در GET LAST INSERT: " . $e->getMessage();
            return false;
        }
    }

    public function count($tableName, $where = '', $value = null)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . $tableName;
            if ($where) {
                $sql .= " WHERE " . $where . " = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$value]);
            } else {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
            }
            $result = $stmt->fetch();
            return isset($result['total']) ? $result['total'] : 0;
        } catch (PDOException $e) {
            echo "❌ خطا در COUNT: " . $e->getMessage();
            return 0;
        }
    }
}

$db = new DataBase();
?>