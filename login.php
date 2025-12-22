<?php
require_once("vendor/autoload.php");
require_once("includes/session.php");

use App\Utils\Helpers;

if (isset($_SESSION['user_id'])) {
    Helpers::redirect_to("staff.php");
}

$message = "";
$errors  = [];

if (isset($_POST['submit'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Username and password are required.";
    } else {
        if (Helpers::attempt_login($username, $password)) {
            Helpers::redirect_to("staff.php");
        } else {
            $message = "Username / password incorrect.";
        }
    }
}
?>

<?php include("includes/header.php"); ?>

<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
<tr>
    <td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
        <a class="block text-[#D4E6F4] no-underline" href="index.php">Return to public site</a>
    </td>

    <td class="pl-8 pt-8 pb-8 pr-8 align-top bg-[#EEE4B9]">
        <h2 class="text-[#8D0D19] mt-0 mb-6">Staff Login</h2>

        <?php if (!empty($message)) { ?>
            <p class="mb-4"><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form action="login.php" method="post">
            <p class="mb-4">
                Username:
                <input type="text" name="username" class="ml-2" />
            </p>
            <p class="mb-4">
                Password:
                <input type="password" name="password" class="ml-2" />
            </p>
            <input type="submit" name="submit" value="Login" class="mt-4" />
        </form>
    </td>
</tr>
</table>

<?php include("includes/footer.php"); ?>
