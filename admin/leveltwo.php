<?php

require("authenticate.php");

if ($_SESSION["level"] < 2) {
    $_SESSION["error"] = "You cannot access this page!";
    header("Location: error.php");
    exit();
}

?>
