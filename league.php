<?php

// Start a session to manage user data across requests
session_start();

// Include files to retrieve league, thumbnail, and league name information
require("tools/getleague.php");
require("tools/getthumbnail.php");
require("tools/getleaguename.php");

// Construct a SQL query to retrieve teams of a specific league, ordered by team name
$query = "SELECT * FROM teams WHERE league_id = :league_id ORDER BY team_name";

// Prepare and execute the SQL query, binding the league_id parameter
$statement = $db->prepare($query);
$statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
$statement->execute();

// Fetch all teams from the result set
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
            <!-- Display the league name as the main heading -->
            <h2>
                <?= $league["league_name"] ?>
            </h2>

            <!-- Link to edit the league -->
            <div class="form-link">
                <a href="editleague.php?id=<?= $league["league_id"] ?>">Edit</a>
            </div>

            <?php if (count($teams)): ?>
                <!-- Display teams if available -->
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
                <!-- Display a message if there are no teams in the league -->
                <p class="center">No teams yet.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
