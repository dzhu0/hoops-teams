<?php

// Start a session to manage user data across requests
session_start();

// Include file to authenticate users
require("tools/authenticate.php");

// Include file for handling logo-related operations
require("tools/logotools.php");

// Include file to retrieve team information
require("tools/getteam.php");

// Include file to retrieve information about available leagues
require("tools/getleagues.php");

//Include package for resizing the images
require("vendor/gumlet/php-image-resize/lib/ImageResize.php");

// Check if the logged-in user has the necessary permissions to edit this team
if ($_SESSION["user_id"] !== $team["user_id"] && $_SESSION["level"] === 1) {
    $_SESSION["error"] = "You cannot edit this team!";
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
        case !isset($_POST["team_name"]) || empty($_POST["team_name"]):
            // If Team Name is not provided, set an invalid input message
            $_SESSION["invalid"] = "Team Name is required!";
            break;
        case !isset($_POST["arena"]) || empty($_POST["arena"]):
            // If Arena is not provided, set an invalid input message
            $_SESSION["invalid"] = "Arena is required!";
            break;
        case !isset($_POST["home_city"]) || empty($_POST["home_city"]):
            // If Home City is not provided, set an invalid input message
            $_SESSION["invalid"] = "Home City is required!";
            break;
        case !isset($_POST["head_coach"]) || empty($_POST["head_coach"]):
            // If Head Coach is not provided, set an invalid input message
            $_SESSION["invalid"] = "Head Coach is required!";
            break;
        case !isset($_POST["league_id"]):
            // If League ID is not provided, set an invalid input message
            $_SESSION["invalid"] = "League Name is required!";
            break;
    }

    // If there is an invalid input, redirect to the edit team page with an error message
    if (isset($_SESSION["invalid"])) {
        header("Location: editteam.php?id={$team_id}");
        exit();
    }

    // Check if the user wants to delete the existing logo
    if (isset($_POST["delete_logo"]) && $_POST["delete_logo"]) {
        // Check if the team has an existing logo
        if ($team["logo"]) {
            // Delete the existing logo and update the database
            deleteLogo($team["logo"]);

            $query = "UPDATE teams SET logo = NULL WHERE team_id = :team_id";

            $statement = $db->prepare($query);
            $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
            $statement->execute();
        }
    }
    // If a new logo is uploaded
    else if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] === 0) {
        // Check if the team has an existing logo
        if ($team["logo"]) {
            // Delete the existing logo before uploading a new one
            deleteLogo($team["logo"]);
        }

        // Get information about the uploaded logo
        $logo = $_FILES["logo"]["name"];
        $temporary_logo_path = $_FILES["logo"]["tmp_name"];
        $new_logo_path = file_upload_path($logo);

        // Check if the uploaded logo is a valid image type
        if (!file_type_is_valid($temporary_logo_path, $new_logo_path)) {
            $_SESSION["invalid"] = "Invalid image type!";
            header("Location: editteam.php?id={$team_id}");
            exit();
        }

        // Move the uploaded logo to the appropriate directory and create thumbnail versions
        move_uploaded_file($temporary_logo_path, $new_logo_path);
        resize_image($new_logo_path, 400, 400);
        resize_image($new_logo_path, 200, 200, "_thumbnail");

        // Update the team's logo in the database
        $query = "UPDATE teams SET logo = :logo WHERE team_id = :team_id";

        $statement = $db->prepare($query);
        $statement->bindValue(":logo", $new_logo_path);
        $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
        $statement->execute();
    }

    // Filter and sanitize form input data
    $league_id = filter_input(INPUT_POST, "league_id", FILTER_SANITIZE_NUMBER_INT);
    $team_name = filter_input(INPUT_POST, "team_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $arena = filter_input(INPUT_POST, "arena", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $home_city = filter_input(INPUT_POST, "home_city", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $head_coach = filter_input(INPUT_POST, "head_coach", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate the league ID
    $valid_league_id = false;
    for ($i = 0; $i < count($leagues) && !$valid_league_id; $i++) {
        $valid_league_id = $leagues[$i]["league_id"] === (int) $league_id;
    }

    // Update team information in the database
    $query = "UPDATE teams SET league_id = :league_id, team_name = :team_name, arena = :arena, home_city = :home_city, head_coach = :head_coach WHERE team_id = :team_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":league_id", $valid_league_id ? $league_id : NULL, PDO::PARAM_INT);
    $statement->bindValue(":team_name", $team_name);
    $statement->bindValue(":arena", $arena);
    $statement->bindValue(":home_city", $home_city);
    $statement->bindValue(":head_coach", $head_coach);
    $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
    $statement->execute();

    // Redirect to the team page after saving changes
    header("Location: team.php?id={$team_id}");
    exit();
}
// If the form is submitted and the command is to delete the team
else if ($_POST && isset($_POST["command"]) && $_POST["command"] === "delete") {
    // Check if the team has an existing logo and delete it
    if ($team["logo"]) {
        deleteLogo($team["logo"]);
    }

    // Delete the team from the database
    $query = "DELETE FROM teams WHERE team_id = :team_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
    $statement->execute();

    // Redirect to the index page after deleting the team
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Edit Team Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <form method="post" enctype="multipart/form-data" class="form">
                <h2>Edit Team</h2>

                <!-- Display the existing team logo or a placeholder if no logo is present -->
                <?php if ($team["logo"]): ?>
                    <img src="<?= $team["logo"] ?>" alt="<?= $team["team_name"] ?>" class="logo">
                <?php else: ?>
                    <div class="no-logo">
                        No Logo
                    </div>
                <?php endif ?>

                <!-- Input fields for editing team details with current values pre-filled -->
                <label for="team_name">Team Name:</label>
                <input type="text" name="team_name" id="team_name" value="<?= $team["team_name"] ?>" autofocus>

                <label for="arena">Arena:</label>
                <input type="text" name="arena" id="arena" value="<?= $team["arena"] ?>">

                <label for="home_city">Home City:</label>
                <input type="text" name="home_city" id="home_city" value="<?= $team["home_city"] ?>">

                <label for="head_coach">Head Coach:</label>
                <input type="text" name="head_coach" id="head_coach" value="<?= $team["head_coach"] ?>">

                <!-- Dropdown menu for selecting the league -->
                <label for="league_id">League Name:</label>
                <select name="league_id" id="league_id">
                    <option value="0">N/A</option>

                    <!-- Populate dropdown with available leagues -->
                    <?php if (count($leagues)): ?>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?= $league["league_id"] ?>" <?php if ($league["league_id"] === $team["league_id"]): ?>selected<?php endif ?>>
                                <?= $league["league_name"] ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>

                <!-- Input field for uploading a new logo -->
                <label for="logo">New Logo Image (optional):</label>
                <input type="file" name="logo" id="logo">

                <!-- Checkbox to indicate if the user wants to delete the existing logo -->
                <?php if ($team["logo"]): ?>
                    <div class="form-checkbox">
                        <input type="checkbox" name="delete_logo" id="delete_logo">
                        <label for="delete_logo">Delete logo</label>
                    </div>
                <?php endif ?>

                <!-- Display any previous invalid input message -->
                <p class="error">
                    <?= $invalid ?>
                </p>

                <!-- Buttons for saving changes and deleting the team -->
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
