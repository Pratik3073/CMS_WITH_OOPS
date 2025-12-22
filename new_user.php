
<?php
require_once("vendor/autoload.php");
require_once("includes/session.php");

use App\Utils\Helpers;
use App\Models\User;

Helpers::confirm_logged_in();

$userModel = new User();
$username = "";
$message  = "";

/* -----------------------------
   FORM PROCESSING
------------------------------ */
if (isset($_POST['submit'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Username and password are required.";
    } elseif (strlen($username) > 30) {
        $message = "Username must be 30 characters or less.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        if ($userModel->create([
            'username' => $username,
            'password' => $hashedPassword
        ])) {
            $message  = "The user was successfully created.";
            $username = "";
        } else {
            $message = "User creation failed.";
        }
    }
}
?>

<?php include("includes/header.php"); ?>

<table class="h-[600px] w-full border-collapse align-top text-sm leading-[15px]">
<tr>
    <td class="w-40 p-8 text-[#D4E6F4] bg-[#8D0D19] align-top">
        <a class="block text-[#D4E6F4] no-underline" href="staff.php">Return to Menu</a><br /><br />
    </td>

    <td class="pl-8 align-top bg-[#EEE4B9]">
        <h2 class="text-[#8D0D19] mt-8">Create New User</h2>

        <?php if (!empty($message)) { ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php } ?>

        <form action="new_user.php" method="post">
            <table>
                <tr>
                    <td>Username:</td>
                    <td>
                        <input type="text"
                               name="username"
                               maxlength="30"
                               value="<?php echo htmlspecialchars($username); ?>" />
                    </td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td>
                        <input type="password"
                               name="password" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="submit" value="Create User" />
                    </td>
                </tr>
            </table>
        </form>
    </td>
</tr>
</table>

<?php include("includes/footer.php"); ?>
