<?php

namespace App\Utils;

class Helpers
{
    public static function redirect_to(?string $location = null): void
    {
        if ($location !== null) {
            header("Location: {$location}");
            exit;
        }
    }

    public static function confirm_logged_in(): void
    {
        if (!isset($_SESSION['user_id'])) {
            self::redirect_to('login.php');
        }
    }

    public static function attempt_login(string $username, string $password): bool
    {
        $userModel = new \App\Models\User();
        $user = $userModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        self::redirect_to('login.php');
    }
}
