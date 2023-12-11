<?php

if (!isset($_SESSION["signin"])) {
    $_SESSION["error"] = "You cannot access this page!";
    header("Location: error.php");
    exit();
}

?>
