<?php
require_once("vendor/autoload.php");
require_once("includes/session.php");

use App\Utils\Helpers;

if (isset($_SESSION['user_id'])) {
    Helpers::redirectTo("staff.php");
}

$message = "";
$errors  = [];

if (isset($_POST['submit'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Username and password are required.";
    } else {
        if (Helpers::attemptLogin($username, $password)) {
            Helpers::redirectTo("staff.php");
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

    <td class="pl-8 align-top bg-[#EEE4B9]">
        <h2 class="text-[#8D0D19] mt-8">Staff Login</h2>

        <?php if (!empty($message)) { ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form action="login.php" method="post">
            <p>
                Username:
                <input type="text" name="username" />
            </p>
            <p>
                Password:
                <input type="password" name="password" />
            </p>
            <input type="submit" name="submit" value="Login" />
        </form>
    </td>
</tr>
</table>

<?php include("includes/footer.php"); ?>
