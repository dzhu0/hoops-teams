<?php

session_start();

require("admin/authenticate.php");
require("tools/getteam.php");
require("vendor/gumlet/php-image-resize/lib/ImageResize.php");

if ($_SESSION["user_id"] !== $team["user_id"] && $_SESSION["level"] === 1) {
    $_SESSION["error"] = "You cannot edit this team!";
    header("Location: error.php");
    exit();
}

$invalid = "";

if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

if ($_POST && isset($_POST["command"]) && $_POST["command"] === "save") {
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
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: edit.php?id={$team_id}");
        exit();
    }


    if (isset($_POST["delete_logo"]) && $_POST["delete_logo"]) {
        if ($team["logo"]) {
            deleteLogo($team["logo"]);
        }

        $query = "UPDATE teams SET logo = '' WHERE team_id = :team_id";

        $statement = $db->prepare($query);
        $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
        $statement->execute();
    } else if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] === 0) {
        if ($team["logo"]) {
            deleteLogo($team["logo"]);
        }

        $logo = $_FILES["logo"]["name"];
        $temporary_logo_path = $_FILES["logo"]["tmp_name"];
        $new_logo_path = file_upload_path($logo);

        if (!file_type_is_valid($temporary_logo_path, $new_logo_path)) {
            $_SESSION["invalid"] = "Invalid image type!";
            header("Location: edit.php?id={$team_id}");
            exit();
        }

        move_uploaded_file($temporary_logo_path, $new_logo_path);
        resize_image($new_logo_path, 400, 400);
        resize_image($new_logo_path, 200, 200, "_thumbnail");

        $query = "UPDATE teams SET logo = :logo WHERE team_id = :team_id";

        $statement = $db->prepare($query);
        $statement->bindValue(":logo", $new_logo_path);
        $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
        $statement->execute();
    }

    $team_name = filter_input(INPUT_POST, "team_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $arena = filter_input(INPUT_POST, "arena", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $home_city = filter_input(INPUT_POST, "home_city", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $head_coach = filter_input(INPUT_POST, "head_coach", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "UPDATE teams SET team_name = :team_name, arena = :arena, home_city = :home_city, head_coach = :head_coach WHERE team_id = :team_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":team_name", $team_name);
    $statement->bindValue(":arena", $arena);
    $statement->bindValue(":home_city", $home_city);
    $statement->bindValue(":head_coach", $head_coach);
    $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
    $statement->execute();

    header("Location: team.php?id={$team_id}");
    exit();
} else if ($_POST && isset($_POST["command"]) && $_POST["command"] === "delete") {
    if ($team["logo"]) {
        deleteLogo($team["logo"]);
    }

    $query = "DELETE FROM teams WHERE team_id = :team_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
    $statement->execute();

    header("Location: index.php");
    exit();
}

function file_upload_path($original_filename, $upload_subfolder_name = "logos"): string
{
    $path_segments = [$upload_subfolder_name, basename($original_filename)];
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

function file_type_is_valid($temporary_path, $new_path): bool
{
    $allowed_file_extensions = ["jpg", "jpeg", "png"];
    $allowed_mime_types = ["image/jpeg", "image/png"];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = mime_content_type($temporary_path);

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

function resize_image($path, $width, $height, $name = "")
{
    $image = new \Gumlet\ImageResize($path);
    $image->resize($width, $height, true);
    $image->save(pathinfo($path, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_FILENAME) . "{$name}." . pathinfo($path, PATHINFO_EXTENSION));
}

function deleteLogo($logo)
{
    $thumbnail = pathinfo($logo, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($logo, PATHINFO_FILENAME) . "_thumbnail." . pathinfo($logo, PATHINFO_EXTENSION);
    unlink($thumbnail);
    unlink($logo);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Edit Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <form method="post" enctype="multipart/form-data" class="form">
                <h2>Edit Team</h2>

                <?php if ($team["logo"]): ?>
                    <img src="<?= $team["logo"] ?>" alt="<?= $team["team_name"] ?>" class="logo">
                <?php else: ?>
                    <div class="no-logo">
                        No Logo
                    </div>
                <?php endif ?>

                <label for="team_name">Team Name:</label>
                <input type="text" name="team_name" id="team_name" value="<?= $team["team_name"] ?>">

                <label for="arena">Arena:</label>
                <input type="text" name="arena" id="arena" value="<?= $team["arena"] ?>">

                <label for="home_city">Home City:</label>
                <input type="text" name="home_city" id="home_city" value="<?= $team["home_city"] ?>">

                <label for="head_coach">Head Coach:</label>
                <input type="text" name="head_coach" id="head_coach" value="<?= $team["head_coach"] ?>">

                <label for="logo">New Logo Image (optional):</label>
                <input type="file" name="logo" id="logo">
                <?php if ($team["logo"]): ?>
                    <div class="form-checkbox">
                        <input type="checkbox" name="delete_logo" id="delete_logo">
                        <label for="delete_logo">Delete logo</label>
                    </div>
                <?php endif ?>

                <p class="error">
                    <?= $invalid ?>
                </p>

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
