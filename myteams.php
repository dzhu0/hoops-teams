<?php

session_start();

require("admin/authenticate.php");
require("admin/connect.php");

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

function getThumbnail($logo): string
{
    return pathinfo($logo, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($logo, PATHINFO_FILENAME) . "_thumbnail." . pathinfo($logo, PATHINFO_EXTENSION);
}

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

            <form class="form-sort">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort">
                    <option value="team_name" <?php if ($sort === "team_name" || !isset($_GET["sort"])): ?>selected<?php endif ?>>Team Name</option>
                    <option value="arena" <?php if ($sort === "arena"): ?>selected<?php endif ?>>Arena</option>
                    <option value="home_city" <?php if ($sort === "home_city"): ?>selected<?php endif ?>>Home City
                    </option>
                    <option value="head_coach" <?php if ($sort === "head_coach"): ?>selected<?php endif ?>>Head Coach
                    </option>
                </select>

                <button>Sort</button>
            </form>

            <?php if ($statement->rowCount()): ?>
                <div class="teams">
                    <?php while ($team = $statement->fetch()): ?>
                        <details open>
                            <summary>
                                <?php if ($team["logo"]): ?>
                                    <img src="<?= getThumbnail($team["logo"]) ?>" alt="<?= $team["team_name"] ?>" class="logo">
                                <?php else: ?>
                                    <div class="no-logo small">
                                        No Logo
                                    </div>
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
                        </details>
                    <?php endwhile ?>
                </div>
            <?php else: ?>
                <h2>You have no team.</h2>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
