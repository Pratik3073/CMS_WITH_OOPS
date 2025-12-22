<?php

namespace App\Models;

use App\Core\Database;

class Subject
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(bool $public = true): array
    {
        $query = "SELECT * FROM subjects ";
        if ($public) {
            $query .= "WHERE visible = 1 ";
        }
        $query .= "ORDER BY position ASC";

        $result = $this->db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $query = "SELECT * FROM subjects WHERE id = {$id} LIMIT 1";
        $result = $this->db->query($query);

        return $result->fetch_assoc() ?: null;
    }

    public function normalizePositions(): bool
    {
        $query = "SELECT id FROM subjects ORDER BY position ASC, id ASC";
        $result = $this->db->query($query);

        $position = 1;
        while ($subject = $result->fetch_assoc()) {
            $updateQuery = "UPDATE subjects SET position = {$position} WHERE id = {$subject['id']}";
            $this->db->query($updateQuery);
            $position++;
        }

        return true;
    }

    public function updatePositionSafely(int $subjectId, int $newPosition): bool
    {
        $subject = $this->getById($subjectId);
        if (!$subject) return false;

        $currentPosition = $subject['position'];

        if ($currentPosition == $newPosition) {
            return true;
        }

        $query = "SELECT id FROM subjects WHERE position = {$newPosition} AND id != {$subjectId}";
        $result = $this->db->query($query);

        if ($result->num_rows > 0) {
            if ($newPosition > $currentPosition) {
                $query = "UPDATE subjects SET position = position - 1
                         WHERE position > {$currentPosition} AND position <= {$newPosition} AND id != {$subjectId}";
            } else {
                $query = "UPDATE subjects SET position = position + 1
                         WHERE position >= {$newPosition} AND position < {$currentPosition} AND id != {$subjectId}";
            }
            $this->db->query($query);
        }

        $query = "UPDATE subjects SET position = {$newPosition} WHERE id = {$subjectId}";
        $result = $this->db->query($query);

        return $this->db->getConnection()->affected_rows > 0;
    }

    public function getAllForSubject(int $subjectId, bool $public = true): array
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
