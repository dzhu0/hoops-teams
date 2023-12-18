<?php

session_start();

require("tools/authenticate.php");
require("tools/getleague.php");

if ($_SESSION["user_id"] !== $league["user_id"] && $_SESSION["level"] === 1) {
    $_SESSION["error"] = "You cannot edit this league!";
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
        case !isset($_POST["league_name"]) || empty($_POST["league_name"]):
            $_SESSION["invalid"] = "League Name is required!";
            break;
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: editleague.php?id={$league_id}");
        exit();
    }

    $league_name = filter_input(INPUT_POST, "league_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "UPDATE leagues SET league_name = :league_name WHERE league_id = :league_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":league_name", $league_name);
    $statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
    $statement->execute();

    header("Location: league.php?id={$league_id}");
    exit();
} else if ($_POST && isset($_POST["command"]) && $_POST["command"] === "delete") {
    $query = "DELETE FROM leagues WHERE league_id = :league_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
    $statement->execute();

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

                <label for="league_name">League Name:</label>
                <input type="text" name="league_name" id="league_name" value="<?= $league["league_name"] ?>" autofocus>

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
