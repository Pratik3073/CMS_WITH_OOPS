<?php
// This file stores all basic functions

/* -----------------------------
   Escape string safely
------------------------------ */
function mysql_prep($value) {
    global $connection;
    return mysqli_real_escape_string($connection, $value);
}

/* -----------------------------
   Redirect helper
------------------------------ */
function redirect_to($location = null) {
    if ($location !== null) {
        header("Location: {$location}");
        exit;
    }
}











?>
