<?php

// Start a session to manage user data across requests
session_start();

// Include necessary files for user authentication, database connection, logo tools, and league data retrieval
require("tools/authenticate.php");
require("tools/connect.php");
require("tools/logotools.php");
require("tools/getleagues.php");
require("vendor/gumlet/php-image-resize/lib/ImageResize.php");

// Initialize an empty string for invalid input messages
$invalid = "";

// Check if there is a previous invalid input message stored in the session
if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];

    // Clear the stored invalid message to avoid displaying it again
    unset($_SESSION["invalid"]);
}

// Check if the form is submitted and the command is to create a team
if ($_POST && isset($_POST["command"]) && $_POST["command"] === "create") {

    // Validate input for team creation
    switch (true) {
        case !isset($_POST["team_name"]) || empty($_POST["team_name"]):
            $_SESSION["invalid"] = "Team Name is required!";
            break;
        case !isset($_POST["arena"]) || empty($_POST["arena"]):
            $_SESSION["invalid"] = "Arena is required!";
            break;
        case !isset($_POST["home_city"]) || empty($_POST["home_city"]):
            $_SESSION["invalid"] = "Home City is required!";
            break;
        case !isset($_POST["head_coach"]) || empty($_POST["head_coach"]):
            $_SESSION["invalid"] = "Head Coach is required!";
            break;
        case !isset($_POST["league_id"]):
            $_SESSION["invalid"] = "League Name is required!";
            break;
    }

    // If there is an invalid input, redirect to the create team page with an error message
    if (isset($_SESSION["invalid"])) {
        header("Location: createteam.php");
        exit();
    }

    // Check if a logo file is provided and there are no file upload errors
    if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] === 0) {
        $logo = $_FILES["logo"]["name"];
        $temporary_logo_path = $_FILES["logo"]["tmp_name"];
        $new_logo_path = file_upload_path($logo);

        // Check if the file type is valid
        if (!file_type_is_valid($temporary_logo_path, $new_logo_path)) {
            $_SESSION["invalid"] = "Invalid image type!";
            header("Location: createteam.php");
            exit();
        }

        // Move the uploaded logo file to the destination path and create thumbnail versions
        move_uploaded_file($temporary_logo_path, $new_logo_path);
        resize_image($new_logo_path, 400, 400);
        resize_image($new_logo_path, 200, 200, "_thumbnail");
    }

    // Filter and sanitize input for database insertion
    $league_id = filter_input(INPUT_POST, "league_id", FILTER_SANITIZE_NUMBER_INT);
    $team_name = filter_input(INPUT_POST, "team_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $arena = filter_input(INPUT_POST, "arena", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $home_city = filter_input(INPUT_POST, "home_city", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $head_coach = filter_input(INPUT_POST, "head_coach", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Check if the selected league ID is valid
    $valid_league_id = false;

    for ($i = 0; $i < count($leagues) && !$valid_league_id; $i++) {
        $valid_league_id = $leagues[$i]["league_id"] === (int) $league_id;
    }

    // SQL query to insert a new team into the database
    $query = "INSERT INTO teams (user_id, league_id, team_name, arena, home_city, head_coach)
              VALUES (:user_id, :league_id, :team_name, :arena, :home_city, :head_coach)";

    // Prepare and execute the SQL query with user input
    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
    $statement->bindValue(":league_id", $valid_league_id ? $league_id : NULL, PDO::PARAM_INT);
    $statement->bindValue(":team_name", $team_name);
    $statement->bindValue(":arena", $arena);
    $statement->bindValue(":home_city", $home_city);
    $statement->bindValue(":head_coach", $head_coach);
    $statement->execute();

    // Get the last inserted team ID
    $team_id = $db->lastInsertId();

    // If a logo file was provided, update the team record with the logo path
    if (isset($new_logo_path)) {
        $query = "UPDATE teams SET logo = :logo WHERE team_id = :team_id";

        $statement = $db->prepare($query);
        $statement->bindValue(":logo", $new_logo_path);
        $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
        $statement->execute();
    }

    // Redirect to the team page for the newly created team
    header("Location: team.php?id={$team_id}");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Create Team Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <!-- Form for creating a new team -->
            <form method="post" enctype="multipart/form-data" class="form">
                <h2>Create Team</h2>

                <!-- Input fields for team information -->
                <label for="team_name">Team Name:</label>
                <input type="text" name="team_name" id="team_name" autofocus>

                <label for="arena">Arena:</label>
                <input type="text" name="arena" id="arena">

                <label for="home_city">Home City:</label>
                <input type="text" name="home_city" id="home_city">

                <label for="head_coach">Head Coach:</label>
                <input type="text" name="head_coach" id="head_coach">

                <label for="league_id">League Name:</label>
                <select name="league_id" id="league_id">
                    <!-- Default option for no league association -->
                    <option value="0">N/A</option>

                    <!-- Display league options retrieved from the database -->
                    <?php if (count($leagues)): ?>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?= $league["league_id"] ?>">
                                <?= $league["league_name"] ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>

                <!-- Input field for uploading a logo image -->
                <label for="logo">Logo Image (optional):</label>
                <input type="file" name="logo" id="logo">

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
