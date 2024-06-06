<?php

require_once(realpath(dirname(__FILE__) . "/database.php"));

class User {
    public int $id;
    public string $email;
    public string $password;
    public string $created_at;

    /**
     * Create new user in database.
     */
    public function save() : bool {
        $query = Db::get()->prepare('INSERT INTO users (email, password) VALUES (?, ?)');
        $response = $query->execute([$this->email, $this->password]);
        $this->id = Db::get()->lastInsertId();
        return $response;
    }

    /**
     * Check if user email and password is correct.
     */
    public static function authenticate($email, $password) : User|null {
        $response = Db::get()
            ->query('SELECT * FROM users WHERE email="' . $email . '" LIMIT 1')
            ->fetchAll(PDO::FETCH_CLASS, 'User');
        
        if (!empty($response)) {
            $user = $response[0];

            if ($user->email == $email && $user->password == md5($password)) {
                $user->password = ''; // not returning password, we don't need it
                return $user;
            }
        }

        return null;
    }
}
