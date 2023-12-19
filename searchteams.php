<?php

session_start();

require("tools/connect.php");
require("tools/getthumbnail.php");
require("tools/getleaguename.php");

$teams = array();

if ($_GET && (isset($_GET["search_id"]) || isset($_GET["search_keys"]))) {
    $query = "SELECT * FROM teams WHERE league_id IS NOT NULL ";

    if (isset($_GET["search_id"])) {
        $league_id = filter_input(INPUT_GET, "search_id", FILTER_SANITIZE_NUMBER_INT);
        if ($league_id) {
            $query .= "AND league_id = {$league_id} ";
        }
    }

    if (isset($_GET["search_keys"])) {
        $keys = explode(" ", preg_replace("/\s+/", " ", trim(filter_input(INPUT_GET, "search_keys", FILTER_SANITIZE_FULL_SPECIAL_CHARS))));

        $conds = array();
        foreach ($keys as $key) {
            $conds[] = "team_name LIKE '{$key}' OR team_name LIKE '{$key} %' OR team_name LIKE '% {$key}' OR team_name LIKE '% {$key} %'";
        }

        $query .= "AND (" . implode(" OR ", $conds) . ") ";
    }

    $query .= "ORDER BY team_name";

    $statement = $db->prepare($query);
    $statement->execute();

    $teams = $statement->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Search Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <h2>Search Results</h2>

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
                <p class="center">No teams found.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
