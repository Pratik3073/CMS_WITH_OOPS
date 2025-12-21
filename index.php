<?php
require_once("vendor/autoload.php");

use App\Utils\Navigation;

/* MUST be called AFTER autoload */
$navigation = new Navigation();
$selected = $navigation->findSelectedPage();
$sel_subject = $selected['subject'];
$sel_page = $selected['page'];
?>

<?php include("includes/header.php"); ?>
<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
    <tr>
        <!-- NAVIGATION -->
		<td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
		<?php echo $navigation->publicNavigation($sel_subject, $sel_page); ?>
</td>
        <!-- PAGE CONTENT -->
        <td class="pl-8 align-top bg-[#EEE4B9]">
            <?php if ($sel_page) { ?>

                <h2 class="text-[#8D0D19] mt-8"><?php echo htmlspecialchars($sel_page['menu_name']); ?></h2>
                <div class="page-content">
                    <?php
                       echo nl2br(htmlspecialchars($sel_page['content']));


                    ?>
                </div>

            <?php } elseif ($sel_subject) { ?>

                <h2 class="text-[#8D0D19] mt-8"><?php echo htmlspecialchars($sel_subject['menu_name']); ?></h2>
                <p>Please select a page from the navigation.</p>

            <?php } else { ?>

                <h2 class="text-[#8D0D19] mt-8">Welcome to Widget Corp</h2>
                <p>Please select a subject.</p>

            <?php } ?>
        </td>
    </tr>
</table>

<?php include("includes/footer.php"); ?>
