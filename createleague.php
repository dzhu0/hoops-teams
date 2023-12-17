<?php

session_start();

require("tools/authenticate.php");
require("tools/connect.php");

$invalid = "";

if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

if ($_POST && isset($_POST["command"]) && $_POST["command"] === "create") {
    switch (true) {
        case !isset($_POST["league_name"]) || empty($_POST["league_name"]):
            $_SESSION["invalid"] = "League Name is required!";
            break;
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: createleague.php");
        exit();
    }

    $league_name = filter_input(INPUT_POST, "league_name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "INSERT INTO leagues (league_name, user_id)
              VALUES (:league_name, :user_id)";

    $statement = $db->prepare($query);
    $statement->bindValue(":league_name", $league_name);
    $statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
    $statement->execute();

    $league_id = $db->lastInsertId();

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
            <form method="post" class="form">
                <h2>Create League</h2>

                <label for="league_name">League Name:</label>
                <input type="text" name="league_name" id="league_name" autofocus>

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
