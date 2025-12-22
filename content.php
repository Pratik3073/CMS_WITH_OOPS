<?php
require_once("vendor/autoload.php");
require_once("includes/session.php");

use App\Utils\Helpers;
use App\Utils\Navigation;

Helpers::confirm_logged_in(); //uses the scope resolution operator :: and means you are calling a static method.

$navigation = new Navigation();
$selected = $navigation->findSelectedPage();
$sel_subject = $selected['subject'];
$sel_page = $selected['page'];

include("includes/header.php");
?>

<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
<tr>
    <td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
        <?php echo $navigation->admin_navigation($sel_subject, $sel_page); ?>
        <br />
        <a class="block text-[#D4E6F4] no-underline" href="new_subject.php">+ Add a new subject</a>
    </td>

    <td class="pl-8 align-top bg-[#EEE4B9]">
        <?php if ($sel_page) { ?>

            <h2 class="text-[#8D0D19] mt-8"><?php echo htmlspecialchars($sel_page['menu_name']); ?></h2>
            <div class="page-content">
                <?php echo nl2br(htmlspecialchars($sel_page['content'])); ?>
            </div>
            <br />
            <a class="text-[#8D0D19] no-underline font-bold hover:underline" href="edit_page.php?page=<?php echo $sel_page['id']; ?>">Edit page</a>

        <?php } elseif ($sel_subject) { ?>

            <h2 class="text-[#8D0D19] mt-8"><?php echo htmlspecialchars($sel_subject['menu_name']); ?></h2>
            <p>Please select a page.</p>
            <a class="text-[#8D0D19] no-underline font-bold hover:underline" href="edit_subject.php?subj=<?php echo $sel_subject['id']; ?>">Edit subject</a>

        <?php } else { ?>

            <h2 class="text-[#8D0D19] mt-8">Select a subject or page to edit</h2>

        <?php } ?>
    </td>
</tr>
</table>

<?php include("includes/footer.php"); ?>
