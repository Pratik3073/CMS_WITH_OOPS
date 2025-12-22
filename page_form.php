<?php require_once("includes/session.php"); ?>
<?php confirm_logged_in(); ?>
<?php // included by new_page.php and edit_page.php ?>
<?php if (!isset($new_page)) { $new_page = false; } ?>
<?php
require_once("vendor/autoload.php");

use App\Models\Page;

$pageModel = new Page();
?>

<p class="mb-4 mt-4">
    Page name:
    <input type="text"
           name="menu_name"
           id="menu_name"
           value="<?php echo htmlspecialchars($sel_page['menu_name'] ?? ''); ?>" />
</p>

<p class="mb-4">
    Position:
    <select name="position">
        <?php
        if (!$new_page) {
            $page_set   = $pageModel->get_pages($sel_page['subject_id']);
            $page_count = count($page_set);
        } else {
            $page_set   = $pageModel->get_pages($sel_subject['id']);
            $page_count = count($page_set) + 1;
        }

        for ($count = 1; $count <= $page_count; $count++) {
            echo "<option value=\"{$count}\"";
            if (($sel_page['position'] ?? 0) == $count) {
                echo " selected";
            }
            echo ">{$count}</option>";
        }
        ?>
    </select>
</p>

<p class="mb-4">
    Visible:
    <input type="radio" name="visible" value="0"
        <?php if (($sel_page['visible'] ?? 0) == 0) { echo "checked"; } ?> /> No
    &nbsp;
    <input type="radio" name="visible" value="1"
        <?php if (($sel_page['visible'] ?? 0) == 1) { echo "checked"; } ?> /> Yes
</p>

<p>
    <span class="mb-2 inline-block ">Content:</span><br />
    <textarea name="content" rows="20" cols="80" class="rounded-sm pl-1"><?php
        echo htmlspecialchars($sel_page['content'] ?? '');
    ?></textarea>
</p>
