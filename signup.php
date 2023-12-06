<?php

session_start();

$invalid = "";

if (isset($_SESSION["login"]) && $_SESSION["level"] < 2) {
    header("Location: index.php");
    exit();
} else if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

require("admin/connect.php");

if ($_POST && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
    switch (true) {
        case empty($_POST["username"]):
            $_SESSION["invalid"] = "Username is required!";
            break;
        case empty($_POST["email"]):
            $_SESSION["invalid"] = "Email is required!";
            break;
        case empty($_POST["password"]):
            $_SESSION["invalid"] = "Password is required!";
            break;
        case empty($_POST["confirm_password"]):
            $_SESSION["invalid"] = "Confirm password is required!";
            break;
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: signup.php");
        exit();
    }

    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirm_password = filter_input(INPUT_POST, "confirm_password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "SELECT * FROM users WHERE username = :username";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", strtolower($username));
    $statement->execute();

    if ($statement->rowCount()) {
        $_SESSION["invalid"] = "Username already exists!";
    } else if (!$email) {
        $_SESSION["invalid"] = "Email is invalid!";
    } else if ($password !== $confirm_password) {
        $_SESSION["invalid"] = "Passwords does not match!";
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: signup.php");
        exit();
    }

    $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", strtolower($username));
    $statement->bindValue(":email", strtolower($email));
    $statement->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
    $statement->execute();

    $user_id = $db->lastInsertId();

    $query = "SELECT * FROM users WHERE user_id = :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $user_id);
    $statement->execute();

    $user = $statement->fetch();
    
    if (isset($_SESSION["login"])) {
        header("Location: admin.php");
        exit();
    } else {
        $_SESSION["login"] = true;
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["level"] = $user["level"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];

        header("Location: index.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Sign Up Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <form method="post" class="form">
                <h2>Sign Up</h2>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username">

                <label for="email">Email:</label>
                <input type="email" name="email" id="email">

                <label for="password">Password:</label>
                <input type="password" name="password" id="password">

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password">

                <p class="error">
                    <?= $invalid ?>
                </p>

                <div class="btns">
                    <button name="command" value="signup">Sign Up</button>
                </div>
            </form>

            <?php if (!isset($_SESSION["login"])): ?>
                <div class="form-link">
                    <a href="login.php">Log In</a>
                </div>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
