<?php

// Start a session to manage user data across requests
session_start();

// Include file to authenticate users
require("tools/authenticate.php");

// Include file to retrieve league information
require("tools/getleague.php");

// Check if the logged-in user has the necessary permissions to edit this league
if ($_SESSION["user_id"] !== $league["user_id"] && $_SESSION["level"] === 1) {
    $_SESSION["error"] = "You cannot edit this league!";
    header("Location: error.php");
    exit();
}

// Initialize an empty string for invalid input messages
$invalid = "";

// Check if there is a previous invalid input message stored in the session
if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

// Check if the form is submitted and the command is to save changes
if ($_POST && isset($_POST["command"]) && $_POST["command"] === "save") {
    switch (true) {
        case !isset($_POST["league_name"]) || empty($_POST["league_name"]):
            // If League Name is not provided, set an invalid input message
            $_SESSION["invalid"] = "League Name is required!";
            break;
    }

    // If there is an invalid input, redirect to the edit league page with an error message
    if (isset($_SESSION["invalid"])) {
        header("Location: editleague.php?id={$league_id}");
        exit();
    }

    // Filter and sanitize the League Name from the form submission
    $league_name = filter_input(INPUT_POST, "league_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // SQL query to update the league name in the database
    $query = "UPDATE leagues SET league_name = :league_name WHERE league_id = :league_id";

    // Prepare and execute the SQL query with the provided league name and ID
    $statement = $db->prepare($query);
    $statement->bindValue(":league_name", $league_name);
    $statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
    $statement->execute();

    // Redirect to the league page after saving changes
    header("Location: league.php?id={$league_id}");
    exit();
}
// If the form is submitted and the command is to delete the league
else if ($_POST && isset($_POST["command"]) && $_POST["command"] === "delete") {
    // SQL query to delete the league from the database
    $query = "DELETE FROM leagues WHERE league_id = :league_id";

    // Prepare and execute the SQL query with the provided league ID
    $statement = $db->prepare($query);
    $statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
    $statement->execute();

    // Redirect to the leagues page after deleting the league
    header("Location: leagues.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Edit League Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <form method="post" enctype="multipart/form-data" class="form">
                <h2>Edit League</h2>

                <!-- Input field for editing League Name with current value pre-filled -->
                <label for="league_name">League Name:</label>
                <input type="text" name="league_name" id="league_name" value="<?= $league["league_name"] ?>" autofocus>

                <!-- Display any previous invalid input message -->
                <p class="error">
                    <?= $invalid ?>
                </p>

                <!-- Buttons for saving changes and deleting the league -->
                <div class="btns">
                    <button name="command" value="save">Save</button>
                    <button name="command" value="delete" class="btn-secondary">Delete</button>
                </div>
            </form>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
