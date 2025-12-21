<?php

namespace App\Utils;

use App\Models\Subject;

class Navigation
{
    private Subject $subjectModel;

    public function __construct()
    {
        $this->subjectModel = new Subject();
    }

    public function findSelectedPage(): array
    {
        $selSubject = null;
        $selPage = null;

        if (isset($_GET['page'])) {
            $pageId = (int)$_GET['page'];
            $pageModel = new \App\Models\Page();
            $selPage = $pageModel->getById($pageId);
            $selSubject = $selPage ? $this->subjectModel->getById($selPage['subject_id']) : null;

        } elseif (isset($_GET['subj'])) {
            $subjectId = (int)$_GET['subj'];
            $selSubject = $this->subjectModel->getById($subjectId);
            // Don't automatically select the default page when a subject is selected
            $selPage = null;

        }

        return ['subject' => $selSubject, 'page' => $selPage];
    }

    public function publicNavigation(?array $selSubject, ?array $selPage): string
    {
        $output = "<ul class=\"pl-0 list-none\">";
        $subjects = $this->subjectModel->getAll(true);

        foreach ($subjects as $subject) {
            $liClass = "block mb-[6px]";
            if ($selSubject && $subject['id'] == $selSubject['id']) {
                $liClass .= " font-bold";
            }
            $output .= "<li class=\"{$liClass}\"><a class=\"block text-[#D4E6F4] no-underline\" href=\"index.php?subj={$subject['id']}\">"
                    . htmlspecialchars($subject['menu_name']) . "</a>";

            // Show pages for the selected subject (whether selected directly or through a page)
            if ($selSubject && $subject['id'] == $selSubject['id']) {
                $pages = $this->subjectModel->getPages($subject['id'], true);
                $output .= "<ul class=\"pl-8 list-square\">";
                foreach ($pages as $page) {
                    $pageLiClass = "block mb-[6px]";
                    if ($selPage && $page['id'] == $selPage['id']) {
                        $pageLiClass .= " font-bold";
                    }
                    $output .= "<li class=\"{$pageLiClass}\"><a class=\"block text-[#D4E6F4] no-underline\" href=\"index.php?page={$page['id']}\">"
                            . htmlspecialchars($page['menu_name']) . "</a></li>";
                }
                $output .= "</ul>";
            }
            $output .= "</li>";
        }

        return $output . "</ul>";
    }

    public function adminNavigation(?array $selSubject, ?array $selPage): string
    {
        $output = "<ul class=\"pl-0 list-none\">";
        $subjects = $this->subjectModel->getAll(false);

        foreach ($subjects as $subject) {
            $liClass = "block mb-[6px]";
            if ($selSubject && $subject['id'] == $selSubject['id']) {
                $liClass .= " font-bold";
            }
            $output .= "<li class=\"{$liClass}\"><a class=\"block text-[#D4E6F4] no-underline\" href=\"edit_subject.php?subj={$subject['id']}\">"
                    . htmlspecialchars($subject['menu_name']) . "</a></li>";

            $pages = $this->subjectModel->getPages($subject['id'], false);
            $output .= "<ul class=\"pl-8 list-square\">";
            foreach ($pages as $page) {
                $pageLiClass = "block mb-[6px]";
                if ($selPage && $page['id'] == $selPage['id']) {
                    $pageLiClass .= " font-bold";
                }
                $output .= "<li class=\"{$pageLiClass}\"><a class=\"block text-[#D4E6F4] no-underline\" href=\"content.php?page={$page['id']}\">"
                        . htmlspecialchars($page['menu_name']) . "</a></li>";
            }
            $output .= "</ul>";
        }

        return $output . "</ul>";
    }
}
