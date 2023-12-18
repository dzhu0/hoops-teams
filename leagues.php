<?php

session_start();

require("tools/connect.php");
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
                <p class="center">No leagues yet.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
