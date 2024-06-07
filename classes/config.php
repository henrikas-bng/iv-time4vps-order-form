<?php

class Config {
    private static $data = [
        'HOST' => '127.0.0.1:3307',
        'DB_NAME' => 'time4vps_orders',
        'USERNAME' => 'root',
        'PASSWORD' => '',
    ];

    /**
     * Get config data by key.
     */
    public static function get($key = '') : string {
        if (isset(self::$data[$key])) {
            return self::$data[$key];
        }

        return '';
    }
}
