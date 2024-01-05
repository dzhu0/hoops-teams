<?php

// Include a file for user authentication
require("authenticate.php");

// Check if the user's permission level is less than 2 (not authorized)
if ($_SESSION["level"] < 2) {

    // Set an error message in the session variable
    $_SESSION["error"] = "You cannot access this page!";

    // Redirect the user to the error.php page
    header("Location: error.php");
    exit();
}

?>
