<?php

namespace App\Models;

use App\Core\Database;

class Page
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getById(int $id): ?array
    {
        $query = "SELECT * FROM pages WHERE id = {$id} LIMIT 1";
        $result = $this->db->query($query);

        return $result->fetch_assoc() ?: null;
    }

    public function getAllForSubject(int $subjectId, bool $public = true): array
    {
        return $this->getPages($subjectId, $public);
    }

    public function getPages(int $subjectId, bool $public = true): array
    {
        $query = "SELECT * FROM pages WHERE subject_id = {$subjectId} ";
        if ($public) {
            $query .= "AND visible = 1 ";
        }
        $query .= "ORDER BY position ASC";

        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDefaultPage(int $subjectId): ?array
    {
        $query = "SELECT * FROM pages
                  WHERE subject_id = {$subjectId}
                  AND visible = 1
                  ORDER BY position ASC
                  LIMIT 1";

        $result = $this->db->query($query);
        return $result->fetch_assoc() ?: null;
    }

}
