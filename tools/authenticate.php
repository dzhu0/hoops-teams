<?php

// Check if the user is not signed in
if (!isset($_SESSION["signin"])) {

    // If not signed in, set an error message in the session variable
    $_SESSION["error"] = "You cannot access this page!";

    // Redirect the user to the error.php page
    header("Location: error.php");
    exit();
}

?>
