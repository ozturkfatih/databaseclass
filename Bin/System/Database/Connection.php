<?php

namespace Bin\System\Database;

use Bin\Helper\Arr as ArrHelper;
use Bin\System\Config as Config;
use PDO as PDO;
use PDOException as PDOException;

class Connection {

    private static $config_array;
    private static $connectionString;
    private static $driver;
    private static $driverOptions = array();
    private static $dbh = null;

    function __construct() {
        self::getConfigArray();
        self::open();
    }

    private static function getConfigArray() {
        Config::setLoadConfigFile("Database");
        self::$config_array = Config::getLoadConfigFile();
        return self::$config_array;
    }

    private static function getDevelopment() {
        $development = ArrHelper::array_get(self::$config_array, 'exception.environment.development');
        return $development;
    }

    private static function getRelease() {
        $release = ArrHelper::array_get(self::$config_array, 'exception.environment.release');
        return $release;
    }
    /**
     * @return null
     */
    protected static function getDbh() {
        return self::$dbh;
    }

    /**
     * @param null $dbh
     */
    private static function setDbh($dbh) {
        self::$dbh = $dbh;
    }

    /**
     * @return mixed
     */
    private static function getDriver() {
        self::$driver = ArrHelper::array_get(self::$config_array, 'default');
        return self::$driver;
    }

    /**
     * @param null $key
     * @param null $value
     * @internal param array $driverOptions
     */
    private static function setDriverOptions($key = null, $value = null) {
        self::$driverOptions[$key] = $value;
    }

    /**
     * @return mixed
     */

    private static function setConnection() {
        $driver = self::getDriver();
        $host = ArrHelper::array_get(self::$config_array, 'connections.mysql.read.host');
        $database = ArrHelper::array_get(self::$config_array, 'connections.mysql.database');
        $charset = ArrHelper::array_get(self::$config_array, 'connections.mysql.charset');
        $persistent = ArrHelper::array_get(self::$config_array, 'connections.mysql.persistent');
        switch ($driver) {
            case 'mysql':
                self::$connectionString = "mysql:host={$host};dbname={$database}";
                break;
            case 'sqlite':
                self::$connectionString = "sqlite:{$database}";
                break;
            case 'pgsql':
                self::$connectionString = "pgsql:host={$host};dbname={$database}";
                break;
        }
        self::setDriverOptions(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES ´{$charset}´");
        self::setDriverOptions(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER SET {$charset}");
        self::setDriverOptions(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($persistent) {
            self::setDriverOptions(PDO::ATTR_PERSISTENT, true);
        }
    }

    private static function open() {
        $username = ArrHelper::array_get(self::$config_array, 'connections.mysql.username');
        $password = ArrHelper::array_get(self::$config_array, 'connections.mysql.password');
        $message = ArrHelper::array_get(self::$config_array, 'message.notConnection');
        self::setConnection();
        try {
            $dbh = new PDO(self::$connectionString, $username, $password, self::$driverOptions);
            self::setDbh($dbh);
            //if (self::getDevelopment()) echo "Connection Success";
        } catch (PDOException $e) {
            echo self::exceptionOutput($e, "{$message}");
            self::$connectionString = null;
            self::$dbh = null;
            exit;
        }
    }

    protected static function close() {
        self::$connectionString = null;
        self::$dbh = null;
        return self::$dbh;
    }

    protected static function exceptionOutput($e = null, $message = null) {
        if (is_object($e)) {
            if (self::getDevelopment()) {
                return $e->getMessage();
            } elseif (self::getRelease()) {
                return '<p>'.$message.'</p>';
            } else {
                return null;
            }
        }
    }
}
