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

    public function update_position_safely(int $subjectId, int $newPosition): bool
    {
        // Get current subject
        $subject = $this->get_by_subid($subjectId);
        if (!$subject) return false;

        $currentPosition = $subject['position'];

        // If position hasn't changed, do nothing
        if ($currentPosition == $newPosition) {
            return true;
        }

        // Check if new position is already taken
        $query = "SELECT id FROM subjects WHERE position = {$newPosition} AND id != {$subjectId}";
        $result = $this->db->query($query);

        if ($result->num_rows > 0) {
            // Position is taken, shift other subjects
            if ($newPosition > $currentPosition) {
                // Moving to higher position - shift subjects between current and new position up
                $query = "UPDATE subjects SET position = position - 1
                         WHERE position > {$currentPosition} AND position <= {$newPosition} AND id != {$subjectId}";
            } else {
                // Moving to lower position - shift subjects between new and current position down
                $query = "UPDATE subjects SET position = position + 1
                         WHERE position >= {$newPosition} AND position < {$currentPosition} AND id != {$subjectId}";
            }
            $updateResult = $this->db->query($query);
            if (!$updateResult) {
                return false;
            }
        }

        // Update the subject to new position
        $query = "UPDATE subjects SET position = {$newPosition} WHERE id = {$subjectId}";
        $result = $this->db->query($query);

        return $result !== false && $this->db->affected_rows() > 0;
    }
}
