<?php

if (!isset($_SESSION["login"])) {
    $_SESSION["error"] = "You cannot access this page!";
    header("Location: error.php");
    exit();
}

?>
