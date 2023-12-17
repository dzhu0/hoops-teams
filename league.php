<?php

session_start();

require("tools/getleague.php");
require("tools/getthumbnail.php");
require("tools/getleaguename.php");

$query = "SELECT * FROM teams WHERE league_id = :league_id ORDER BY team_name";

$statement = $db->prepare($query);
$statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
$statement->execute();

$teams = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>
        <?= $league["league_name"] ?>
    </title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <h2>
                <?= $league["league_name"] ?>
            </h2>

            <div class="form-link">
                <a href="editleague.php?id=<?= $league["league_id"] ?>">Edit</a>
            </div>

            <?php if (count($teams)): ?>
                <div class="teams">
                    <?php foreach ($teams as $team): ?>
                        <details open>
                            <summary>
                                <?php if ($team["logo"]): ?>
                                    <img src="<?= getThumbnail($team["logo"]) ?>" alt="<?= $team["team_name"] ?>" class="logo">
                                <?php else: ?>
                                    <span class="no-logo small">
                                        No Logo
                                    </span>
                                <?php endif ?>
                                <h2>
                                    <a href="team.php?id=<?= $team["team_id"] ?>">
                                        <?= $team["team_name"] ?>
                                    </a>
                                </h2>
                            </summary>
                            <p><b>Arena:</b>
                                <?= $team["arena"] ?>
                            </p>
                            <p><b>Home City:</b>
                                <?= $team["home_city"] ?>
                            </p>
                            <p><b>Head Coach:</b>
                                <?= $team["head_coach"] ?>
                            </p>
                            <p><b>League Name:</b>
                                <?= getLeagueName($db, $team["league_id"]) ?>
                            </p>
                        </details>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <p class="center">No teams yet.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
