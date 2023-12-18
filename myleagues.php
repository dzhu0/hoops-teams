<?php

session_start();

require("tools/authenticate.php");
require("tools/connect.php");

$query = "SELECT * FROM leagues WHERE user_id = :user_id ORDER BY league_name";

$statement = $db->prepare($query);
$statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$statement->execute();

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
                <ul>
                    <?php foreach ($leagues as $league): ?>
                        <li>
                            <a href="league.php?id=<?= $league["league_id"] ?>">
                                <?= $league["league_name"] ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php else: ?>
                <p class="center">You have no league.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
