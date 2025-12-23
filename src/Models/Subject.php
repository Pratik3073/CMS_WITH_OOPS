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
    if ($public) {
        $stmt = $this->db->prepare(
            "SELECT * FROM subjects
             WHERE visible = 1
             ORDER BY position ASC"
        );
    } else {
        $stmt = $this->db->prepare(
            "SELECT * FROM subjects
             ORDER BY position ASC"
        );
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $data;
}


public function get_by_subject(int $id): ?array
{
    $stmt = $this->db->prepare(
        "SELECT * FROM subjects WHERE id = ? LIMIT 1"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $stmt->close();

    return $data ?: null;
}


  
public function update_position_safely(int $subjectId, int $newPosition): bool
{
    // 1. Get current subject
    $subject = $this->get_by_subject($subjectId);
    if (!$subject) {
        return false;
    }

    $currentPosition = (int) $subject['position'];

    // 2. If position has not changed, do nothing
    if ($currentPosition === $newPosition) {
        return true;
    }

    // 3. Check if new position is already taken
    $stmt = $this->db->prepare(
        "SELECT id FROM subjects WHERE position = ? AND id != ?"
    );
    $stmt->bind_param("ii", $newPosition, $subjectId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isTaken = $result->num_rows > 0;
    $stmt->close();

    // 4. Shift other subjects if needed
    if ($isTaken) {
        if ($newPosition > $currentPosition) {
            // Moving DOWN (numerically higher)
            $stmt = $this->db->prepare(
                "UPDATE subjects
                 SET position = position - 1
                 WHERE position > ? AND position <= ? AND id != ?"
            );
            $stmt->bind_param("iii", $currentPosition, $newPosition, $subjectId);
        } else {
            // Moving UP (numerically lower)
            $stmt = $this->db->prepare(
                "UPDATE subjects
                 SET position = position + 1
                 WHERE position >= ? AND position < ? AND id != ?"
            );
            $stmt->bind_param("iii", $newPosition, $currentPosition, $subjectId);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        $stmt->close();
    }

    // 5. Update the subject to the new position
    $stmt = $this->db->prepare(
        "UPDATE subjects SET position = ? WHERE id = ?"
    );
    $stmt->bind_param("ii", $newPosition, $subjectId);
    $stmt->execute();

    $affected = $stmt->affected_rows;
    $stmt->close();

    return $affected > 0;
}

}
