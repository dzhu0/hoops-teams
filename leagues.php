<?php

// Start a session to manage user data across requests
session_start();

// Include the file to establish a database connection
require("tools/connect.php");

// Include the file to retrieve a list of leagues
require("tools/getleagues.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Leagues Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <h2>All Leagues</h2>

            <?php if (count($leagues)): ?>
                <!-- Check if there are leagues available -->
                <ul>
                    <!-- Iterate through each league and create a list item with a link to its details page -->
                    <?php foreach ($leagues as $league): ?>
                        <li>
                            <a href="league.php?id=<?= $league["league_id"] ?>">
                                <?= $league["league_name"] ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php else: ?>
                <!-- Display a message if there are no leagues available -->
                <p class="center">No leagues yet.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
