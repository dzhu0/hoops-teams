<?php

// Start a session to manage user data across requests
session_start();

// Include the file to establish a database connection
require("tools/connect.php");

// Include the file to get the thumbnail of an image
require("tools/getthumbnail.php");

// Include the file to get the name of a league
require("tools/getleaguename.php");

// Initialize an empty array to store search results
$teams = array();

// Check if the request is a GET request and if search parameters are present
if ($_GET && (isset($_GET["search_id"]) || isset($_GET["search_keys"]))) {

    // Begin constructing the SQL query to retrieve teams
    $query = "SELECT * FROM teams WHERE league_id IS NOT NULL ";

    // Check if a specific league ID is provided in the search parameters
    if (isset($_GET["search_id"])) {
        $league_id = filter_input(INPUT_GET, "search_id", FILTER_SANITIZE_NUMBER_INT);
        if ($league_id) {
            // Add a condition to filter teams by the specified league ID
            $query .= "AND league_id = {$league_id} ";
        }
    }

    // Check if search keys (keywords) are provided in the search parameters
    if (isset($_GET["search_keys"])) {
        // Extract and sanitize search keys, split them into an array
        $keys = explode(" ", preg_replace("/\s+/", " ", trim(filter_input(INPUT_GET, "search_keys", FILTER_SANITIZE_FULL_SPECIAL_CHARS))));

        // Initialize an array to store conditions for each search key
        $conds = array();

        // Construct conditions for SQL query based on each search key
        foreach ($keys as $key) {
            $conds[] = "team_name LIKE '{$key}' OR team_name LIKE '{$key} %' OR team_name LIKE '% {$key}' OR team_name LIKE '% {$key} %'";
        }

        // Add the conditions to the SQL query
        $query .= "AND (" . implode(" OR ", $conds) . ") ";
    }

    // Add the final part of the SQL query to order results by team_name
    $query .= "ORDER BY team_name";

    $statement = $db->prepare($query);
    $statement->execute();

    // Fetch all the teams that match the search criteria
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
                <!-- Check if teams are found; if true, display the list of teams -->
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
                <!-- Display a message if no teams are found -->
                <p class="center">No teams found.</p>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
