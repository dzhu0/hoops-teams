<?php

// Start a session to manage user data across requests
session_start();

// Check if the user is signed in
if (isset($_SESSION["signin"])) {
    // Unset (clear) session variables related to user data
    unset($_SESSION["signin"]);
    unset($_SESSION["user_id"]);
    unset($_SESSION["level"]);
    unset($_SESSION["username"]);
}

// Redirect the user to the index page
header("Location: index.php");
exit();

?>
