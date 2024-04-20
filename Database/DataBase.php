<?php

namespace Database;

use PDO;
use PDOException;

class DataBase
{
    private $connection;
    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    ];
    private $dbHost = DB_HOST;
    private $dbName = DB_NAME;
    private $dbUserName = DB_USERNAME;
    private $dbPassword = DB_PASSWORD;

    function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=" . $this->dbHost . ";dbname=" . $this->dbName, $this->dbUserName, $this->dbPassword, $this->options);

        } catch (PDOException $EROR) {
            die("Error : " . $EROR->getMessage());
        }
    }

    // CRUD opration
    public function select($query, $value = null)
    {
        try {
            $statement = $this->connection->prepare($query);

            if ($value == null) {
                $statement->execute();
            } else {
                $statement->execute($value);
            }

            return $statement;

        } catch (PDOException $EROR) {
            echo $EROR->getMessage();
            return false;
        }
    }

    public function insert($tableName, $fields, $values)
    {
        try {
            $statement = $this->connection->prepare("INSERT INTO " . $tableName . "(" . implode(', ', $fields) . ", created_at) VALUE (:" . implode(', :', $values) . ", NOW() );");
            $statement->execute(array_combine($fields, $values));
            return true;

        } catch (PDOException $EROR) {
            echo $EROR->getMessage();
            return false;
        }
    }

    public function update($tableName, $id, $fields, $values = NULL)
    {

        $query = "UPDATE " . $tableName . " SET";

        foreach (array_combine($fields, $values) as $fields => $values) {
            if ($values !== NULL) {
                $query .= " `" . $fields . "` = ? ,";
            } else {
                $query .= " `" . $fields . "` = NULL ,";
            }
        }

        $query .= " updated_at = NOW() WHERE id = ?";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute(array_merge(array_filter(array_values($values)), [$id]));
            return true;

        } catch (PDOException $EROR) {
            echo $EROR->getMessage();
            return false;
        }
    }

    public function delete($tableName, $id)
    {
        $query = "DELETE FROM " . $tableName . " WHERE id = ? ;";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([$id]);
            return true;

        } catch (PDOException $EROR) {
            echo $EROR->getMessage();
            return false;
        }
    }

    public function createTabel($query)
    {
        try {
            $this->connection->exec($query);
            return true;

        } catch(PDOException $EROR) {
            return false;
        }
    }
}