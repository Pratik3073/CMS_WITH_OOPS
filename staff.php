<?php
require_once("vendor/autoload.php");
require_once("includes/session.php");

use App\Utils\Helpers;

Helpers::confirm_logged_in();
include("includes/header.php");
?>
<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
	<tr>
		<td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
			&nbsp;
		</td>
		<td class="pl-8 align-top bg-[#EEE4B9]">
			<h2 class="text-[#8D0D19] mt-8">Staff Menu</h2>
			<p>Welcome to the staff area, <?php echo $_SESSION['username']; ?>.</p>
			<ul class="list-disc pl-5 my-[10px]">
				<li class="mb-2"><a class="text-[#8D0D19] no-underline font-bold hover:underline" href="content.php">Manage Website Content</a></li>
				<li class="mb-2"><a class="text-[#8D0D19] no-underline font-bold hover:underline" href="new_user.php">Add Staff User</a></li>
				<li class="mb-2"><a class="text-[#8D0D19] no-underline font-bold hover:underline" href="logout.php">Logout</a></li>
			</ul>
		</td>
	</tr>
</table>
<?php include("includes/footer.php"); ?>
