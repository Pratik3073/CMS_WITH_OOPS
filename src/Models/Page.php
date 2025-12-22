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

    public static function findSelectedPage(Page $pageModel, Subject $subjectModel): array
    {
        $selSubject = null;
        $selPage = null;

        if (isset($_GET['page'])) {
            $pageId = (int)$_GET['page'];
            $selPage = $pageModel->getById($pageId);
            $selSubject = $selPage ? $subjectModel->getById($selPage['subject_id']) : null;

        } elseif (isset($_GET['subj'])) {
            $subjectId = (int)$_GET['subj'];
            $selSubject = $subjectModel->getById($subjectId);
            // Don't automatically select the default page when a subject is selected
            $selPage = null;

        }

        return ['subject' => $selSubject, 'page' => $selPage];
    }

}
