<?php

namespace App\Models;

use App\Core\Database;

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
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

    public function getAll(): array
    {
        $query = "SELECT id, username, first_name, last_name FROM users ORDER BY username ASC";
        $result = $this->db->query($query);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $query = "SELECT id, username, first_name, last_name FROM users WHERE id = {$id} LIMIT 1";
        $result = $this->db->query($query);

        return $result->fetch_assoc() ?: null;
    }

    public function create(array $data): bool
    {
        $username = $this->db->escape($data['username']);
        $password = $this->db->escape($data['password']);
        $firstName = $this->db->escape($data['first_name']);
        $lastName = $this->db->escape($data['last_name']);

        $query = "INSERT INTO users (username, hashed_password, first_name, last_name)
                  VALUES ('{$username}', '{$password}', '{$firstName}', '{$lastName}')";

        $result = $this->db->query($query);
        return $result !== false;
    }

    public function update(int $id, array $data): bool
    {
        $username = $this->db->escape($data['username']);
        $firstName = $this->db->escape($data['first_name']);
        $lastName = $this->db->escape($data['last_name']);

        $query = "UPDATE users SET username = '{$username}', first_name = '{$firstName}', last_name = '{$lastName}' WHERE id = {$id}";

        $result = $this->db->query($query);
        return $this->db->getConnection()->affected_rows > 0;
    }

    public function delete(int $id): bool
    {
        $query = "DELETE FROM users WHERE id = {$id}";
        $result = $this->db->query($query);

        return $this->db->getConnection()->affected_rows > 0;
    }
}
