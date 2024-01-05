<?php

// Start a session to manage user data across requests
session_start();

// Include files for user authentication and database connection
require("tools/authenticate.php");
require("tools/connect.php");

// Initialize an empty string for invalid input messages
$invalid = "";

// Check if there is a previous invalid input message stored in the session
if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];

    // Clear the stored invalid message to avoid displaying it again
    unset($_SESSION["invalid"]);
}

// Check if the form is submitted and the command is to create a league
if ($_POST && isset($_POST["command"]) && $_POST["command"] === "create") {

    // Check if the league name is not set or is empty
    switch (true) {
        case !isset($_POST["league_name"]) || empty($_POST["league_name"]):
            $_SESSION["invalid"] = "League Name is required!";
            break;
    }

    // If there is an invalid input, redirect to the create league page with an error message
    if (isset($_SESSION["invalid"])) {
        header("Location: createleague.php");
        exit();
    }

    // Filter and sanitize the league name from the form submission
    $league_name = filter_input(INPUT_POST, "league_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // SQL query to insert a new league into the database
    $query = "INSERT INTO leagues (league_name, user_id)
              VALUES (:league_name, :user_id)";

    // Prepare and execute the SQL query with user input
    $statement = $db->prepare($query);
    $statement->bindValue(":league_name", $league_name);
    $statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
    $statement->execute();

    // Get the last inserted league ID
    $league_id = $db->lastInsertId();

    // Redirect to the league page for the newly created league
    header("Location: league.php?id={$league_id}");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Create League Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <!-- Form for creating a new league -->
            <form method="post" class="form">
                <h2>Create League</h2>

                <!-- Input field for the league name -->
                <label for="league_name">League Name:</label>
                <input type="text" name="league_name" id="league_name" autofocus>

                <!-- Display any previous invalid input message -->
                <p class="error">
                    <?= $invalid ?>
                </p>

                <!-- Buttons for creating and resetting the form -->
                <div class="btns">
                    <button name="command" value="create">Create</button>
                    <button type="reset" class="btn-secondary">Reset</button>
                </div>
            </form>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
