<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php find_selected_page(); ?>

<?php include("includes/header.php"); ?>
<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
	<tr>
		<td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
			<?php echo navigation($sel_subject, $sel_page); ?>
		</td>
		<td class="pl-8 align-top bg-[#EEE4B9]">
			<h2 class="text-[#8D0D19] mt-8">Add Subject</h2>
			<form action="create_subject.php" method="post">
				<p>Subject name:
					<input type="text" name="menu_name" value="" id="menu_name" />
				</p>
				<p>Position:
					<select name="position">
						<?php
							$subject_set = get_all_subjects();
							$subject_count = mysqli_num_rows($subject_set);
							// $subject_count + 1 b/c we are adding a subject
							for($count=1; $count <= $subject_count+1; $count++) {
								echo "<option value=\"{$count}\">{$count}</option>";
							}
						?>
					</select>
				</p>
				<p>Visible:
					<input type="radio" name="visible" value="0" /> No
					&nbsp;
					<input type="radio" name="visible" value="1" /> Yes
				</p>
				<input type="submit" value="Add Subject" />
			</form>
			<br />
			<a class="text-[#8D0D19] no-underline font-bold hover:underline" href="content.php">Cancel</a>
		</td>
	</tr>
</table>
<?php require("includes/footer.php"); ?>
