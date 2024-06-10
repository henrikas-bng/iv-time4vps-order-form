<?php

require_once(realpath(dirname(__FILE__)) . '/config.php');

class Api {
    private static $base_url = 'https://billing.time4vps.com/api';

    public static function call(string $request_path, string $method = 'get', array $params = []) {
        $url = self::$base_url . $request_path;
        $auth = base64_encode(Config::get('API_AUTH_EMAIL') . ':' . Config::get('API_AUTH_PASSWORD'));
        $certificate = realpath(dirname(__FILE__) . '/../cacert.pem');
        
        $headers = [
            'Authorization: Basic ' . $auth,
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_CAINFO, $certificate);

        if ($method == 'post' && !empty($params)) {
            $post_data = http_build_query($params);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}
