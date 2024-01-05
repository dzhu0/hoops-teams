<?php

// Start a session to manage user data across requests
session_start();

// Include the file to authenticate users
require("tools/authenticate.php");

// Include the file to establish a database connection
require("tools/connect.php");

// SQL query to retrieve leagues associated with the current user, ordered by league name
$query = "SELECT * FROM leagues WHERE user_id = :user_id ORDER BY league_name";

$statement = $db->prepare($query);

// Bind the user_id parameter to the current user's ID in the session
$statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$statement->execute();

// Fetch all the leagues associated with the user
$leagues = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>My Leagues Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <h2>My Leagues</h2>

            <?php if (count($leagues)): ?>
                <!-- Check if the user has leagues; if true, display the list of leagues -->
                <ul>
                    <?php foreach ($leagues as $league): ?>
                        <!-- Iterate through each league and create a list item with a link to its details page -->
                        <li>
                            <a href="league.php?id=<?= $league["league_id"] ?>">
                                <?= $league["league_name"] ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php else: ?>
                <!-- Display a message if the user has no leagues -->
                <p class="center">You have no league.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
