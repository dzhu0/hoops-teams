<?php

session_start();

if (isset($_SESSION["login"])) {
    unset($_SESSION["login"]);
    unset($_SESSION["user_id"]);
    unset($_SESSION["level"]);
    unset($_SESSION["username"]);
}

header("Location: index.php");
exit();

?>
