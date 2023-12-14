<?php

session_start();

require("tools/connect.php");

if (!isset($_SESSION["error"])) {
    header("Location: index.php");
    exit();
}

$error = $_SESSION["error"];
unset($_SESSION["error"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Error Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <h2 class="error">
                <?= $error ?>
            </h2>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
