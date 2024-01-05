<?php

// Start a session to manage user data across requests
session_start();

// Include the file to authenticate users
require("tools/authenticate.php");

// Include the file to establish a database connection
require("tools/connect.php");

// Include the file to get the thumbnail of an image
require("tools/getthumbnail.php");

// Include the file to get the name of a league
require("tools/getleaguename.php");

// Determine the sorting parameter; default is "team_name"
$sort = isset($_GET["sort"]) ? $_GET["sort"] : "team_name";

// Define a list of valid sorting options
$list = ["team_name", "arena", "home_city", "head_coach"];

// Redirect to the "myteams.php" page if an invalid sorting option is provided
if (!in_array($sort, $list)) {
    header("Location: myteams.php");
    exit();
}

// SQL query to retrieve teams associated with the current user, ordered by the specified sort parameter
$query = "SELECT * FROM teams WHERE user_id = :user_id ORDER BY {$sort}";

$statement = $db->prepare($query);

// Bind the user_id parameter to the current user's ID in the session
$statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$statement->execute();

// Fetch all the teams associated with the user
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

            <!-- Form to select sorting options -->
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
                <!-- Check if the user has teams; if true, display the list of teams -->
                <div class="teams">
                    <?php foreach ($teams as $team): ?>
                        <!-- Display team details using a details/summary HTML structure -->
                        <details open>
                            <summary>
                                <?php if ($team["logo"]): ?>
                                    <!-- Display team logo if available -->
                                    <img src="<?= getThumbnail($team["logo"]) ?>" alt="<?= $team["team_name"] ?>" class="logo">
                                <?php else: ?>
                                    <!-- Display a message if no logo is available -->
                                    <span class="no-logo small">
                                        No Logo
                                    </span>
                                <?php endif ?>
                                <!-- Display team name with a link to its details page -->
                                <h2>
                                    <a href="team.php?id=<?= $team["team_id"] ?>">
                                        <?= $team["team_name"] ?>
                                    </a>
                                </h2>
                            </summary>
                            <!-- Display additional team details -->
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
                <!-- Display a message if the user has no teams -->
                <p class="center">You have no team.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
