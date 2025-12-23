<?php

namespace App\Models;

use App\Core\Database;

class Page
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::get_instance();
    }

    public function get_by_id(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    
        $stmt->close();
    
        return $data ?: null;
    }
    

    public function get_pages(int $subjectId, bool $public = true): array
    {
        if ($public) {
            $stmt = $this->db->prepare(
                "SELECT * FROM pages 
                 WHERE subject_id = ? AND visible = 1 
                 ORDER BY position ASC"
            );
            $stmt->bind_param("i", $subjectId);
        } else {
            $stmt = $this->db->prepare(
                "SELECT * FROM pages 
                 WHERE subject_id = ? 
                 ORDER BY position ASC"
            );
            $stmt->bind_param("i", $subjectId);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
    
        return $data;
    }
    

    public function get_default_page(int $subjectId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM pages
             WHERE subject_id = ?
             AND visible = 1
             ORDER BY position ASC
             LIMIT 1"
        );
    
        $stmt->bind_param("i", $subjectId);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    
        $stmt->close();
    
        return $data ?: null;
    }
    

    public static function find_selected_page(Page $pageModel, Subject $subjectModel): array
    {
        $selSubject = null;
        $selPage = null;

        if (isset($_GET['page'])) {
            $pageId = (int)$_GET['page'];
            $selPage = $pageModel->get_by_id($pageId);
            $selSubject = $selPage ? $subjectModel->get_by_subject($selPage['subject_id']) : null;

        } elseif (isset($_GET['subj'])) {
            $subjectId = (int)$_GET['subj'];
            $selSubject = $subjectModel->get_by_subject($subjectId);
            // Don't automatically select the default page when a subject is selected
            $selPage = null;

        }

        return ['subject' => $selSubject, 'page' => $selPage];
    }

}
