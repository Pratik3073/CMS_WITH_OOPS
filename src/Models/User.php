<?php

namespace App\Models;

use App\Core\Database;

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::get_instance();
    }

    public function authenticate(string $username, string $password): ?array
    {
        $username = $this->db->escape($username);

        $query = "SELECT * FROM users WHERE username = '{$username}' LIMIT 1";
        $result = $this->db->query($query);

        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['hashed_password'])) {
            return $user;
        }

        return null;
    }


    public function create(array $data): bool
    {
        $username = $this->db->escape($data['username']);
        $password = $this->db->escape($data['password']);

        $query = "INSERT INTO users (username, hashed_password)
                  VALUES ('{$username}', '{$password}')";

        $result = $this->db->query($query);
        return $result !== false;
    }
}
