<?php

session_start();

require("tools/leveltwo.php");
require("tools/getuser.php");

$query = "SELECT * FROM users ORDER BY level DESC";

$statement = $db->prepare($query);
$statement->execute();

if ($statement->rowCount() >= 2) {
    $top = $statement->fetch();
    $secondTop = $statement->fetch();
}

if ($_SESSION["level"] <= $user["level"] && $_SESSION["user_id"] !== $top["user_id"]) {
    $_SESSION["error"] = "You cannot edit this user!";
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
        case !isset($_POST["username"]) || empty($_POST["username"]):
            $_SESSION["invalid"] = "Username is required!";
            break;
        case !isset($_POST["email"]) || empty($_POST["email"]):
            $_SESSION["invalid"] = "Email is required!";
            break;
        case !isset($_POST["level"]) || empty($_POST["level"]):
            $_SESSION["invalid"] = "Level is required!";
            break;
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: edituser.php?id={$user_id}");
        exit();
    }

    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $level = filter_input(INPUT_POST, "level", FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT * FROM users WHERE username = :username";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", strtolower($username));
    $statement->execute();

    if ($username !== $user["username"] && $statement->rowCount()) {
        $_SESSION["invalid"] = "Username already exists!";
    } else if (!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
        $_SESSION["invalid"] = "Email is invalid!";
    } else if ((int) $user_id === $top["user_id"]) {
        if ($level > $secondTop["level"]) {
            $_SESSION["level"] = $level;
        } else {
            $_SESSION["invalid"] = "Level is too low!";
        }
    } else if ($level >= $_SESSION["level"]) {
        $_SESSION["invalid"] = "Level is too high!";
    } else if ($level <= 0) {
        $_SESSION["invalid"] = "Level is too low!";
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: edituser.php?id={$user_id}");
        exit();
    }

    $query = "UPDATE users SET username = :username, email = :email, level = :level WHERE user_id = :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", $username);
    $statement->bindValue(":email", $email);
    $statement->bindValue(":level", $level, PDO::PARAM_INT);
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    header("Location: admin.php");
    exit();
} else if ($_POST && isset($_POST["command"]) && $_POST["command"] === "delete") {
    if ((int) $user_id === $top["user_id"]) {
        $_SESSION["invalid"] = "Cannot delete this user!";
        header("Location: edituser.php?id={$user_id}");
        exit();
    }

    $query = "DELETE FROM users WHERE user_id = :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    header("Location: admin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Edit User Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <form method="post" class="form">
                <h2>Edit User</h2>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?= $user["username"] ?>" autofocus>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= $user["email"] ?>">

                <label for="level">Level:</label>
                <input type="number" name="level" id="level" value="<?= $user["level"] ?>">

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
