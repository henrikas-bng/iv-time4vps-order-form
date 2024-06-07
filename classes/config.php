<?php

class Config {
    private static $data = [
        // Database
        'DB_HOST' => '',
        'DB_NAME' => '',
        'DB_USERNAME' => '',
        'DB_PASSWORD' => '',
        // Time4VPS API
        'API_AUTH_EMAIL'=> '',
        'API_AUTH_PASSWORD'=> '',
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
