<?php

class DataBase
{
    private $conn;
    private $option = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    private $dbhost = DB_HOST;
    private $dbname = BD_NAME;
    private $dbusername = DB_USERNAME;
    private $dbpassword = DB_PASSWORD;

    public function __construct()
    {
        try {
            $this->conn = new PDO('mysql:host=' . $this->dbhost . ';dbname=' . $this->dbname, $this->dbusername, $this->dbpassword, $this->option);
            //             echo 'ok';
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function select($sql, $value = null)
    {
        try {
            $stmt = $this->conn->prepare($sql);
            if ($value == null) {
                $stmt->execute();
            } else {
                $stmt->execute([$value]);
            }
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function insert($tableName, $fields, $values)
    {
        try{
            // 'username' => 'hassank2', 'password' => '1234', 'age' => 30
            $stmt = $this->conn->prepare("INSERT INTO ".$tableName."(".implode(', ', $fields)." , created_at) VALUES ( :" . implode(', :', $fields) . " , now() );");
            $stmt->execute(array_combine($fields, $values));
            return true;
        }
        catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    public function update($tableName, $id, $fields, $values)
    {

        $sql = "UPDATE " . $tableName . " SET";
        foreach(array_combine($fields, $values) as $field => $value)
        {
            if($value)
            {
                $sql .= " `" . $field . "` = ? ,";
            }
            else{
                $sql .= " `" . $field . "` = NULL ,";

            }
        }

        $sql .= " updated_at = now()";
        $sql .= " WHERE id = ?";
        try{
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array_merge(array_filter(array_values($values)), [$id]));
            return true;
        }

        catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }


    }


    public function delete($tbname, $id)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM " . $tbname . " WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function new_select($fild, $tbname, $where, $value)
    {
        try {
            $stmt = $this->conn->prepare("SELECT  " . $fild . " FROM  " . $tbname . " WHERE  " . $where . "= ?");
            $stmt->execute([$value]);
            $stmt = $stmt->fetch();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    public function all($qury, array $value)
    {
        $stmt = $this->conn->prepare($qury);
        $stmt->execute($value);
        $result = $stmt->fetch();
        return $result;
    }

    public function getLastInsert($sTable)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM ".$sTable." ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            $stmt = $stmt->fetch();
            return $stmt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}
