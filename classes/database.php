<?php

class Db {
    private static $db = null;

    public static function get() {
        if (self::$db === null) {
            // TODO: gotta throw this into .env or config file of some sorts
            $host = '127.0.0.1:3307';
            $db_name = 'time4vps_orders';
            $username = 'root';
            $password = '';

            try {
                self::$db = new \PDO('mysql:host=' . $host . ';dbname=' . $db_name, $username, $password);
            } catch (PDOException $e) {
                echo $e->getMessage();
                exit();
            }
        }

        return self::$db;
    }
}
