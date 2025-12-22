<?php

namespace App\Models;

use App\Core\Database;

class Subject
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::get_instance();
    }

    public function get_all(bool $public = true): array
    {
        $query = "SELECT * FROM subjects ";
        if ($public) {
            $query .= "WHERE visible = 1 ";
        }
        $query .= "ORDER BY position ASC";

        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_by_subid(int $id): ?array
    {
        $query = "SELECT * FROM subjects WHERE id = {$id} LIMIT 1";
        $result = $this->db->query($query);

        return $result->fetch_assoc() ?: null;
    }

    public function getAllSubject(int $subjectId, bool $public = true): array
    {
        $query = "SELECT * FROM pages WHERE subject_id = {$subjectId} ";
        if ($public) {
            $query .= "AND visible = 1 ";
        }
        $query .= "ORDER BY position ASC";

        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
