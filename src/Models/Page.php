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

    public function get_by_id(int $id): ?array
    {
        $query = "SELECT * FROM pages WHERE id = {$id} LIMIT 1";
        $result = $this->db->query($query);

        return $result->fetch_assoc() ?: null;
    }

    public function get_pages(int $subjectId, bool $public = true): array
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

    public static function find_selected_page(Page $pageModel, Subject $subjectModel): array
    {
        $selSubject = null;
        $selPage = null;

        if (isset($_GET['page'])) {
            $pageId = (int)$_GET['page'];
            $selPage = $pageModel->get_by_id($pageId);
            $selSubject = $selPage ? $subjectModel->get_by_subid($selPage['subject_id']) : null;

        } elseif (isset($_GET['subj'])) {
            $subjectId = (int)$_GET['subj'];
            $selSubject = $subjectModel->get_by_subid($subjectId);
            // Don't automatically select the default page when a subject is selected
            $selPage = null;

        }

        return ['subject' => $selSubject, 'page' => $selPage];
    }

}
