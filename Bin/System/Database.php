<?php

namespace Bin\System;

use Bin\System\Database\Connection as Connection;
use PDO as PDO;
use PDOException as PDOException;

class Database extends Connection {
    private static $dbh;
    private static $stmt;
    public static $affectedRows = null;

    public function __construct() {
        parent::__construct();
        self::$dbh = parent::getDbh();
    }

    private static $fetchMode = PDO::FETCH_ASSOC;

    /**
     * @param int $fetchMode
     */
    public static function setFetchMode($fetchMode) {
        self::$fetchMode = $fetchMode;
    }

    private static $attribute = array();

    /**
     * $key You would most commonly use this to set the PDO::ATTR_CURSOR
     * $value value to PDO::CURSOR_SCROLL to request a scrollable cursor.
     * @param array $attribute
     * @return mixed
     */
    private static function setAttribute(array $attribute = null) {
        if (!empty($attribute)) {
            foreach ($attribute as $key => $value) {
                return self::$attribute[$key] = $value;
            }
        }
    }

    public static $lastInsertedId = null;

    /**
     * public string PDO::lastInsertId ([ string $name = NULL ] )
     * @param null $name Name of the sequence object from which the ID should be returned.
     * @return mixed
     */
    public static function setLastInsertedId($name = null) {
        return self::$dbh->lastInsertId($name);
    }

    private static function fetchAll($sql = null, array $params = null, array $options = null) {
        if (!empty($sql)) {
            try {
                $statement = self::prepare($sql, $params, $options);
                return $statement->fetchAll(self::$fetchMode);
            } catch (PDOException $e) {
                echo Connection::exceptionOutput($e, 'Something went wrong while fetching the record.');
            }
        }
        return false;
    }

    private static function fetch($sql = null, array $params = null, array $options = null) {
        if (!empty($sql)) {
            try {
                $statement = self::prepare($sql, $params, $options);
                return $statement->fetch(self::$fetchMode);
            } catch (PDOException $e) {
                echo Connection::exceptionOutput($e, 'Something went wrong while fetching the record.');
            }
        }
        return false;
    }

    /**
     * public PDOStatement PDO::prepare ( string $statement [, array $driver_options = array() ] )
     * @param null  $sql     This must be a valid SQL statement for the target database server.
     * @param array $options This array holds one or more key=>value pairs to set attribute values for the PDOStatement
     *                       object that this method returns
     * @param array $params
     */
    private static function prepare($sql = null, array $params = null, array $options = null) {
        if (!empty($sql)) {
            if (!is_object(self::$dbh)) {
                self::$dbh = Connection::getDbh();
            }
            self::setAttribute($options);
            self::$stmt = self::$dbh->prepare($sql, self::$attribute);
            self::bindParam($params);
            if (!self::$stmt) {
                $errorInfo = self::$dbh->errorInfo();
                throw new PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is
                {$errorInfo[1]} {$sql}");
            }
            if (!self::$stmt->execute() || self::$stmt->errorCode() != '00000') {
                $errorInfo = self::$stmt->errorInfo();
                throw new PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is
                {$errorInfo[1]}<br />SQL: {$sql}");
            }
            self::$affectedRows = self::$stmt->rowCount();
            return self::$stmt;
        }
    }

    private static function execute($sql = null, array $params = null, array $options = null) {
        if (!empty($sql)) {
            try {
                $statement = self::prepare($sql, $params, $options);
                return $statement;
            } catch (PDOException $e) {
                echo Connection::exceptionOutput($e, 'Something went wrong while inserting the record.');
            }
        }
        return false;
    }

    /**
     * public bool PDOStatement::bindParam ( mixed $parameter , mixed &$variable [, int $data_type = PDO::PARAM_STR [,
     * int $length [, mixed $driver_options ]]] )
     * |--parameter
     * Parameter identifier. For a prepared statement using named placeholders, this will be a parameter name of the
     * form :name. For a prepared statement using question mark placeholders, this will be the 1-indexed position of
     * the parameter.
     * |--variable
     * Name of the PHP variable to bind to the SQL statement parameter.
     * |--data_type
     * Explicit data type for the parameter using the PDO::PARAM_* constants. To return an INOUT parameter from a
     * stored procedure, use the bitwise OR operator to set the PDO::PARAM_INPUT_OUTPUT bits for the data_type
     * parameter.
     * |--length
     * Length of the data type. To indicate that a parameter is an OUT parameter from a stored procedure, you must
     * explicitly set the length.
     * |--driver_options
     * @param $params
     * @return bool
     */
    private static function bindParam(array $params = null, $type = null) {
        if (!empty($params)) {

            foreach ($params as $key => $value) {
                if (!is_array($value)) {
                    self::setBindParam($key, $value, $type);
                } else {
                    foreach ($value as $key_v => $value_v) {
                        self::setBindParam($key_v, $value_v, $type);
                    }
                }

            }
        }
        return false;
    }

    private static function setBindParam($key, $value, $type) {
        if ($type == null) {
            switch (true) {
                case is_integer($value):
                    $type = PDO::PARAM_INT;
                    self::$stmt->bindParam(":{$key}", $value, $type);
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    self::$stmt->bindParam(":{$key}", $value, $type);
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    self::$stmt->bindParam(":{$key}", $value, $type);
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    self::$stmt->bindParam(":{$key}", $value, $type);
            }
            return false;
        }
    }

    public static function query($sql = null) {
        if (!empty($sql)) {
            $fetchMode = self::$fetchMode;
            self::$stmt = self::$dbh->query($sql, $fetchMode);
            return self::$stmt->fetchAll();
        }
        return null;
    }

    /**
     * @param null  $table
     * @param array $values
     * @param array $params
     * @param array $options
     * @return bool
     */

    public static function row($table = null, array $values = null, array $params = null, array $options = null) {
        $fields = '*';
        $parameters = " ";
        if (!empty($values) && is_array($values)) {
            $fields = implode(", ", $values);
        }
        if (!empty($params) && is_array($params)) {
            $parameters = " WHERE ";
            foreach ($params as $key => $value) {
                $parameters .= "{$key}=:{$key}";
            }
        }

        $sql = "SELECT {$fields} FROM {$table}{$parameters}";
        return self::fetch($sql, $params, $options);
    }

    public static function select($table = null, array $values = null, array $params = null, array $options = null) {
        $fields = '*';
        $parameters = "";
        if (!empty($values) && is_array($values)) {
            $fields = implode(", ", $values);
        }
        if (!empty($params) && is_array($params)) {
            $parameters = " WHERE ";
            foreach ($params as $key => $value) {
                $parameters .= "{$key}=:{$key}";
            }
        }
        $sql = "SELECT {$fields} FROM {$table}{$parameters}";
        return self::fetchAll($sql, $params, $options);
    }

    public static function insert($table = null, array $params = null) {
        $data = self::insertArray($params);
        if (!empty($table) && !empty($data)) {
            $sql = "INSERT INTO {$table} (".implode(", ", $data[0]).") VALUES (".implode(", ", $data[1]).")";
            $return = self::execute($sql, $params);
            if ($return) {
                self::$lastInsertedId = self::setLastInsertedId();
                return true;
            }
            return false;
        }
        return false;
    }

    private function insertArray($data = null, $pre = null) {
        if (!empty($data) && is_array($data)) {
            $fields = array();
            $fieldsValue = array();
            foreach ($data as $key => $value) {
                $fields[] = !empty($pre) ? "{$pre}.{$key}" : "{$key}";
                $fieldsValue[] = ":{$key}";
            }
            return array($fields,
                         $fieldsValue);
        }
        return false;
    }

    public static function update($table = null, array $params = null) {
        $data = self::updateArray($params);
        $whereClause = "";
        if (!empty($params) && is_array($params)) {
            foreach ($params['Where'] as $key => $value) {
                $whereClause .= "{$key}=:{$key}";
            }
            $sql = "UPDATE {$table} SET ".implode(", ", $data[0])." WHERE {$whereClause}";
        }
        $return = self::execute($sql, $params);
        if ($return) {
            return true;
        }
        return false;
    }

    private static function updateArray(array $params = null, $pre = null) {
        if (!empty($params) && is_array($params)) {
            $fields = array();
            foreach ($params as $key => $value) {
                if (!is_array($value)) {
                    $fields[] = !empty($pre) ? "{$pre}.{$key}=:{$key}" : "{$key}=:{$key}";
                }
            }
            return array($fields);
        }
        return false;
    }

    public static function delete($table = null, array $params = null) {
        if (true) {
            $whereClause = "";
            if (!empty($params) && is_array($params)) {
                foreach ($params as $key => $value) {
                    $whereClause .= "{$key}=:{$key}";
                }
                $sql = "DELETE FROM {$table} WHERE {$whereClause}";
            }
            $return = self::execute($sql, $params);
            if ($return) {
                return true;
            }
        }
        return false;
    }

    public static function transaction() {
        self::$dbh->beginTransaction();
    }

    public static function commit() {
        self::$dbh->commit();
    }

    public static function rollback() {
        self::$dbh->rollBack();
    }
}