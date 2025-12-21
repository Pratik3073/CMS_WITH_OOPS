<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php
	// make sure the subject id sent is an integer
	if (intval($_GET['page']) == 0) {
		redirect_to('content.php');
	}

	include_once("includes/form_functions.php");

	// START FORM PROCESSING
	// only execute the form processing if the form has been submitted
	if (isset($_POST['submit'])) {
		// initialize an array to hold our errors
		$errors = array();
	
		// perform validations on the form data
		$required_fields = array('menu_name', 'position', 'visible', 'content');
		$errors = array_merge(
			$errors,
			check_required_fields($required_fields, $_POST)
		);
		
		$fields_with_lengths = array('menu_name' => 30);
		$errors = array_merge(
			$errors,
			check_max_field_lengths($fields_with_lengths, $_POST)
		);
		
		
		// clean up the form data before putting it in the database
		$id = mysql_prep($_GET['page']);
		$menu_name = trim(mysql_prep($_POST['menu_name']));
		$position = mysql_prep($_POST['position']);
		$visible = mysql_prep($_POST['visible']);
		$content = mysql_prep($_POST['content']);
	
		// Database submission only proceeds if there were NO errors.
		if (empty($errors)) {
			$query = 	"UPDATE pages SET 
							menu_name = '{$menu_name}',
							position = {$position}, 
							visible = {$visible},
							content = '{$content}'
						WHERE id = {$id}";
			$result = mysqli_query($connection, $query);

			if (mysqli_affected_rows($connection) === 1) {
				$message = "The page was successfully updated.";
			} else {
				$message = "The page could not be updated.";
				$message .= "<br />" . mysqli_error($connection);
			}
			
		} else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . " errors in the form.";
			}
		}
		// END FORM PROCESSING
	}
?>
<?php find_selected_page(); ?>
<?php include("includes/header.php"); ?>
<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
	<tr>
		<td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
			<?php echo navigation($sel_subject, $sel_page); ?>
			<br />
			<a class="block text-[#D4E6F4] no-underline" href="new_subject.php">+ Add a new subject</a>
		</td>
		<td class="pl-8 align-top bg-[#EEE4B9]">
			<h2 class="text-[#8D0D19] mt-8">Edit page: <?php echo $sel_page['menu_name']; ?></h2>
			<?php if (!empty($message)) {echo "<p class=\"message\">" . $message . "</p>";} ?>
			<?php if (!empty($errors)) { display_errors($errors); } ?>
			
			<form action="edit_page.php?page=<?php echo $sel_page['id']; ?>" method="post">
				<?php include "page_form.php" ?>
				<input type="submit" name="submit" value="Update Page" />&nbsp;&nbsp;
				<a class="text-[#8D0D19] no-underline font-bold hover:underline" href="delete_page.php?page=<?php echo $sel_page['id']; ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete page</a>
			</form>
			<br />
			<a class="text-[#8D0D19] no-underline font-bold hover:underline" href="content.php?page=<?php echo $sel_page['id']; ?>">Cancel</a><br />
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>