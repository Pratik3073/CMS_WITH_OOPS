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

    public static function get_instance(): Database  //self means the current class itself.
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_connection(): mysqli
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
        return $this->connection->real_escape_string($value); //escapes special characters in a string before sending it to MySQL.
    }

    public function affected_rows(): int
    {
        return $this->connection->affected_rows;
    }

    public function __destruct()
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}
