<?php

namespace App\Core;

use mysqli;

class Database
{
    private static ?Database $instance = null;
    private mysqli $connection;

    private function __construct()
    {
        require_once(__DIR__ . '/../../includes/constants.php');

        $this->connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

        if ($this->connection->connect_error) {
            die("Database connection failed: " . $this->connection->connect_error);
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    public function query(string $sql): mixed
    {
        $result = $this->connection->query($sql);
        if (!$result) {
            die("Database query failed: " . $this->connection->error);
        }
        return $result;
    }

    public function escape(string $value): string
    {
        return $this->connection->real_escape_string($value);
    }

    public function __destruct()
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}
