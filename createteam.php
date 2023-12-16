<?php

session_start();

require("tools/authenticate.php");
require("tools/connect.php");
require("tools/logotools.php");
require("tools/getleagues.php");
require("vendor/gumlet/php-image-resize/lib/ImageResize.php");

$invalid = "";

if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

if ($_POST && isset($_POST["command"]) && $_POST["command"] === "create") {
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

    if (isset($_SESSION["invalid"])) {
        header("Location: createteam.php");
        exit();
    }

    if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] === 0) {
        $logo = $_FILES["logo"]["name"];
        $temporary_logo_path = $_FILES["logo"]["tmp_name"];
        $new_logo_path = file_upload_path($logo);

        if (!file_type_is_valid($temporary_logo_path, $new_logo_path)) {
            $_SESSION["invalid"] = "Invalid image type!";
            header("Location: createteam.php");
            exit();
        }

        move_uploaded_file($temporary_logo_path, $new_logo_path);
        resize_image($new_logo_path, 400, 400);
        resize_image($new_logo_path, 200, 200, "_thumbnail");
    }

    $league_id = filter_input(INPUT_POST, "league_id", FILTER_SANITIZE_NUMBER_INT);
    $team_name = filter_input(INPUT_POST, "team_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $arena = filter_input(INPUT_POST, "arena", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $home_city = filter_input(INPUT_POST, "home_city", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $head_coach = filter_input(INPUT_POST, "head_coach", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $valid_league_id = false;

    for ($i=0; $i < count($leagues) && !$valid_league_id; $i++) { 
        $valid_league_id = $leagues[$i]["league_id"] === (int) $league_id;
    }

    $query = "INSERT INTO teams (user_id, league_id, team_name, arena, home_city, head_coach)
              VALUES (:user_id, :league_id, :team_name, :arena, :home_city, :head_coach)";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
    $statement->bindValue(":league_id", $valid_league_id ? $league_id : NULL, PDO::PARAM_INT);
    $statement->bindValue(":team_name", $team_name);
    $statement->bindValue(":arena", $arena);
    $statement->bindValue(":home_city", $home_city);
    $statement->bindValue(":head_coach", $head_coach);
    $statement->execute();

    $team_id = $db->lastInsertId();

    if (isset($new_logo_path)) {
        $query = "UPDATE teams SET logo = :logo WHERE team_id = :team_id";

        $statement = $db->prepare($query);
        $statement->bindValue(":logo", $new_logo_path);
        $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
        $statement->execute();
    }

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
            <form method="post" enctype="multipart/form-data" class="form">
                <h2>Create Team</h2>

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
                    <option value="0">N/A</option>

                    <?php if (count($leagues)): ?>
                        <?php foreach ($leagues as $league): ?>
                            <option value="<?= $league["league_id"] ?>">
                                <?= $league["league_name"] ?>
                            </option>
                        <?php endforeach ?>
                    <?php endif ?>
                </select>

                <label for="logo">Logo Image (optional):</label>
                <input type="file" name="logo" id="logo">

                <p class="error">
                    <?= $invalid ?>
                </p>

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
