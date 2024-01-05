<?php

// Start a session to manage user data across requests
session_start();

// Include file to establish a database connection
require("tools/connect.php");

// Check if there is no stored error message in the session
if (!isset($_SESSION["error"])) {
    // Redirect to the index page if there is no error message
    header("Location: index.php");
    exit();
}

// Retrieve the error message from the session and unset it to avoid displaying it again
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
            <!-- Display the error message -->
            <h2 class="error">
                <?= $error ?>
            </h2>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
