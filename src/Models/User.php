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
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE username = ? LIMIT 1"
        );
    
        $stmt->bind_param("s", $username);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    
        $stmt->close();
    
        if ($user && password_verify($password, $user['hashed_password'])) {
            return $user;
        }
    
        return null;
    }
    


    public function create(array $data): bool
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, hashed_password)
             VALUES (?, ?)"
        );
    
        $stmt->bind_param("ss", $data['username'], $hashedPassword);
    
        $success = $stmt->execute();
        $stmt->close();
    
        return $success;
    }
    
}
