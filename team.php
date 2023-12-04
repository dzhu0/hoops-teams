<?php

session_start();

require("tools/getteam.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>
        <?= $team["team_name"] ?>
    </title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <div class="team">
                <?php if ($team["logo"]): ?>
                    <img src="<?= $team["logo"] ?>" alt="<?= $team["team_name"] ?>" class="logo">
                <?php else: ?>
                    <div class="no-logo">
                        No Logo
                    </div>
                <?php endif ?>
                <h2>
                    <?= $team["team_name"] ?>
                </h2>
                <div>
                    <p><b>Arena:</b>
                        <?= $team["arena"] ?>
                    </p>
                    <p><b>Home City:</b>
                        <?= $team["home_city"] ?>
                    </p>
                    <p><b>Head Coach:</b>
                        <?= $team["head_coach"] ?>
                    </p>
                </div>
            </div>

            <div class="form-link">
                <a href="edit.php?id=<?= $team["team_id"] ?>">Edit</a>
            </div>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
