<?php

namespace App\Utils;

use App\Models\Subject;
use App\Models\Page;

class Navigation
{
    private Subject $subjectModel;
    private Page $pageModel;

    public function __construct()
    {
        $this->subjectModel = new Subject();
        $this->pageModel = new Page();
    }

    public function find_page_for_nav(): array
    {
        return Page::find_selected_page($this->pageModel, $this->subjectModel);
    }


    public function public_navigation(?array $selSubject, ?array $selPage): string
    {
        $output = "<ul class=\"pl-0 list-none\">";
        $subjects = $this->subjectModel->get_all(true);

        foreach ($subjects as $subject) {
            $liClass = "block mb-[6px]";
            if ($selSubject && $subject['id'] == $selSubject['id']) {
                $liClass .= " font-bold";
            }
            $output .= "<li class=\"{$liClass}\"><a class=\"block text-[#D4E6F4] no-underline\" href=\"index.php?subj={$subject['id']}\">"
                    . htmlspecialchars($subject['menu_name']) . "</a>";

            // Show pages for the selected subject (whether selected directly or through a page)
            if ($selSubject && $subject['id'] == $selSubject['id']) {
                $pages = $this->pageModel->get_pages($subject['id'], true);
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

    public function admin_navigation(?array $selSubject, ?array $selPage): string
    {
        $output = "<ul class=\"pl-0 list-none\">";
        $subjects = $this->subjectModel->get_all(false);

        foreach ($subjects as $subject) {
            $liClass = "block mb-[6px]";
            if ($selSubject && $subject['id'] == $selSubject['id']) {
                $liClass .= " font-bold";
            }
            $output .= "<li class=\"{$liClass}\"><a class=\"block text-[#D4E6F4] no-underline\" href=\"edit_subject.php?subj={$subject['id']}\">"
                    . htmlspecialchars($subject['menu_name']) . "</a></li>";

            $pages = $this->pageModel->get_pages($subject['id'], false);
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
