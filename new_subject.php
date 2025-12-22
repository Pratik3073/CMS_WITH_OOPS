<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
require_once("vendor/autoload.php");

use App\Utils\Navigation;
use App\Models\Subject;

$navigation = new Navigation();
$selected = $navigation->findSelectedPage();
$sel_subject = $selected['subject'];
$sel_page = $selected['page'];

$subjectModel = new Subject();
?>

<?php include("includes/header.php"); ?>
<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
	<tr>
		<td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
			<?php echo $navigation->admin_navigation($sel_subject, $sel_page); ?>
		</td>
		<td class="pl-8 align-top bg-[#EEE4B9]">
			<h2 class="text-[#8D0D19] mt-8">Add Subject</h2>
			<form action="create_subject.php" method="post">
				<p>Subject name:
					<input type="text" name="menu_name" value="" id="menu_name" />
				</p>
				<p>Position:
					<select name="position" class="mt-1">
						<?php
							$subject_set = $subjectModel->get_all();
							$subject_count = count($subject_set);
							// $subject_count + 1 b/c we are adding a subject
							for($count=1; $count <= $subject_count+1; $count++) {
								echo "<option value=\"{$count}\">{$count}</option>";
							}
						?>
					</select>
				</p>
				<p class="mt-1">Visible:
					<input type="radio" name="visible" value="0" /> No
					&nbsp;
					<input type="radio" name="visible" value="1" /> Yes
				</p>
				<input type="submit" value="Add Subject" class="mt-2"/>
			</form>
			<br />
			<a class="text-[#8D0D19] no-underline font-bold hover:underline" href="content.php">Cancel</a>
		</td>
	</tr>
</table>
<?php require("includes/footer.php"); ?>
