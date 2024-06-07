<?php

require_once(realpath(dirname(__FILE__) . "/config.php"));

class Db {
    private static $db = null;

    public static function get() {
        if (self::$db === null) {
            $dsn = 'mysql:host=' . Config::get('HOST') . ';dbname=' . Config::get('DB_NAME');

            try {
                self::$db = new \PDO($dsn, Config::get('USERNAME'), Config::get('PASSWORD'));
            } catch (PDOException $e) {
                echo $e->getMessage();
                exit();
            }
        }

        return self::$db;
    }
}
