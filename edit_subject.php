<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
		if (intval($_GET['subj']) == 0) {
			redirect_to("content.php");
		}
		if (isset($_POST['submit'])) {
			$errors = array();

			$required_fields = array('menu_name', 'position', 'visible');
			foreach($required_fields as $fieldname) {
				if (!isset($_POST[$fieldname]) || (empty($_POST[$fieldname]) && $_POST[$fieldname] != 0)) { 
					$errors[] = $fieldname; 
				}
			}
			$fields_with_lengths = array('menu_name' => 30);
			foreach($fields_with_lengths as $fieldname => $maxlength ) {
				if (strlen(trim(mysql_prep($_POST[$fieldname]))) > $maxlength) { $errors[] = $fieldname; }
			}
			
			if (empty($errors)) {
				// Perform Update
				$id = mysql_prep($_GET['subj']);
				$menu_name = mysql_prep($_POST['menu_name']);
				$position = mysql_prep($_POST['position']);
				$visible = mysql_prep($_POST['visible']);

				// Update name and visibility first
				$query = "UPDATE subjects SET
							menu_name = '{$menu_name}',
							visible = {$visible}
						WHERE id = {$id}";
				$result = mysqli_query($connection, $query);

				if ($result) {
					// Handle position update with conflict resolution
					if (update_subject_position_safely($id, $position)) {
						$message = "The subject was successfully updated.";
					} else {
						$message = "The subject update failed during position change.";
					}
				} else {
					$message = "The subject update failed.";
					$message .= "<br />" . mysqli_error($connection);
				}

			} else {
				// Errors occurred
				$message = "There were " . count($errors) . " errors in the form.";
			}
			
			
			
			
		} // end: if (isset($_POST['submit']))
?>
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
		<td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
			<?php echo $navigation->admin_navigation($sel_subject, $sel_page); ?>
		</td>
		<td class="pl-8 align-top bg-[#EEE4B9]">
			<h2 class="text-[#8D0D19] mt-8">Edit Subject: <?php echo $sel_subject['menu_name']; ?></h2>
			<?php if (!empty($message)) {
				echo "<p class=\"message\">" . $message . "</p>";
			} ?>
			<?php
			// output a list of the fields that had errors
			if (!empty($errors)) {
				echo "<p class=\"errors\">";
				echo "Please review the following fields:<br />";
				foreach($errors as $error) {
					echo " - " . $error . "<br />";
				}
				echo "</p>";
			}
			?>
			<form action="edit_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" method="post">
				<p class="mb-4 mt-4 ">Subject name:
					<input type="text" name="menu_name" value="<?php echo $sel_subject['menu_name']; ?>" id="menu_name" class="pl-1 rounded-sm" />
				</p>
				<p class="mb-4 " >Position:
					<select name="position" class="rounded-sm">
						<?php
							$subject_set = get_all_subjects(false); // Include hidden subjects
							$subject_count = mysqli_num_rows($subject_set);
							// Show positions from 1 to subject_count, but ensure current position is included
							$max_position = max($subject_count, $sel_subject['position']);
							for($count=1; $count <= $max_position; $count++) {
								echo "<option value=\"{$count}\"";
								if ($sel_subject['position'] == $count) {
									echo " selected";
								}
								echo ">{$count}</option>";
							}
						?>
					</select>
				</p>
				<p class="mb-4">Visible:
					<input type="radio" name="visible" value="0"<?php
					if ($sel_subject['visible'] == 0) { echo " checked"; }
					?> /> No
					&nbsp;
					<input type="radio" name="visible" value="1"<?php
					if ($sel_subject['visible'] == 1) { echo " checked"; }
					?> /> Yes
				</p>
				<input type="submit" name="submit" value="Edit Subject" class="bg-[#8D0D19] text-white px-4 py-2 rounded cursor-pointer hover:bg-[#6D0A15]" />
				&nbsp;&nbsp;
				<a class="text-[#8D0D19] no-underline font-bold hover:underline" href="delete_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" onclick="return confirm('Are you sure?');">Delete Subject</a>
			</form>
			<br />
			<a class="text-[#8D0D19] no-underline font-bold hover:underline" href="content.php">Cancel</a>
			<div class="mt-8 border-t border-black">
				<h3 class="text-[#8D0D19]">Pages in this subject:</h3>
				<ul class="list-disc pl-5 my-[10px]">
<?php
	$subject_pages = get_pages_for_subject($sel_subject['id']);
	while($page = mysqli_fetch_array($subject_pages)) {
		echo "<li class=\"mb-2\"><a class=\"text-[#8D0D19] no-underline font-bold hover:underline\" href=\"content.php?page={$page['id']}\">
		{$page['menu_name']}</a></li>";
	}
?>
				</ul>
				<br />
				+ <a class="text-[#8D0D19] no-underline font-bold hover:underline" href="new_page.php?subj=<?php echo $sel_subject['id']; ?>">Add a new page to this subject</a>
			</div>
		</td>
	</tr>
</table>
<?php require("includes/footer.php"); ?>
