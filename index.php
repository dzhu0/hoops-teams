<?php

// Start a session to manage user data across requests
session_start();

// Include files to establish a database connection and retrieve thumbnail and league name information
require("tools/connect.php");
require("tools/getthumbnail.php");
require("tools/getleaguename.php");

// Determine the sorting parameter, default to "team_name" if not provided
$sort = isset($_GET["sort"]) ? $_GET["sort"] : "team_name";

// List of valid sorting parameters
$list = ["team_name", "arena", "home_city", "head_coach"];

// Redirect to the index page if the sorting parameter is not valid
if (!in_array($sort, $list)) {
    header("Location: index.php");
    exit();
}

// Construct a SQL query to retrieve teams based on the selected sorting parameter
$query = "SELECT * FROM teams ORDER BY {$sort}";

// Prepare and execute the SQL query
$statement = $db->prepare($query);
$statement->execute();

// Fetch all teams from the result set
$teams = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Home Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <h2>All Teams</h2>

            <?php if (isset($_SESSION["signin"])): ?>
                <!-- Form for sorting teams -->
                <form class="form-horizontal form-sort">
                    <label for="sort">Sort by:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()">
                        <!-- Dropdown options for sorting parameters -->
                        <option value="team_name" <?php if ($sort === "team_name" || !isset($_GET["sort"])): ?>selected<?php endif ?>>Team Name</option>
                        <option value="arena" <?php if ($sort === "arena"): ?>selected<?php endif ?>>Arena</option>
                        <option value="home_city" <?php if ($sort === "home_city"): ?>selected<?php endif ?>>Home City</option>
                        <option value="head_coach" <?php if ($sort === "head_coach"): ?>selected<?php endif ?>>Head Coach</option>
                    </select>
                </form>
            <?php endif ?>

            <?php if (count($teams)): ?>
                <!-- Display teams information -->
                <div class="teams">
                    <?php foreach ($teams as $team): ?>
                        <!-- Details container for each team -->
                        <details open>
                            <!-- Team summary section -->
                            <summary>
                                <?php if ($team["logo"]): ?>
                                    <!-- Display team logo with thumbnail -->
                                    <img src="<?= getThumbnail($team["logo"]) ?>" alt="<?= $team["team_name"] ?>" class="logo">
                                <?php else: ?>
                                    <!-- Display a message if no logo is available -->
                                    <span class="no-logo small">
                                        No Logo
                                    </span>
                                <?php endif ?>
                                <!-- Team name as a link to the team details page -->
                                <h2>
                                    <a href="team.php?id=<?= $team["team_id"] ?>">
                                        <?= $team["team_name"] ?>
                                    </a>
                                </h2>
                            </summary>
                            <!-- Team details information -->
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
                                <!-- Retrieve and display the league name using the league_id -->
                                <?= getLeagueName($db, $team["league_id"]) ?>
                            </p>
                        </details>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <!-- Display a message if there are no teams -->
                <p class="center">No teams yet.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
