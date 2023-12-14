<?php

session_start();

require("tools/authenticate.php");
require("tools/connect.php");
require("tools/getthumbnail.php");
require("tools/getleaguename.php");

$sort = isset($_GET["sort"]) ? $_GET["sort"] : "team_name";

$list = ["team_name", "arena", "home_city", "head_coach"];

if (!in_array($sort, $list)) {
    header("Location: myteams.php");
    exit();
}

$query = "SELECT * FROM teams WHERE user_id = :user_id ORDER BY {$sort}";

$statement = $db->prepare($query);
$statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$statement->execute();

$teams = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>My Teams Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <h2>My Teams</h2>

            <form class="form-horizontal form-sort">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="team_name" <?php if ($sort === "team_name" || !isset($_GET["sort"])): ?>selected<?php endif ?>>Team Name</option>
                    <option value="arena" <?php if ($sort === "arena"): ?>selected<?php endif ?>>Arena</option>
                    <option value="home_city" <?php if ($sort === "home_city"): ?>selected<?php endif ?>>Home City</option>
                    <option value="head_coach" <?php if ($sort === "head_coach"): ?>selected<?php endif ?>>Head Coach</option>
                </select>
            </form>

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
                <p class="center">You have no team.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
