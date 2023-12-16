<?php

session_start();

$invalid = "";

if (isset($_SESSION["signin"])) {
    header("Location: index.php");
    exit();
} else if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

require("tools/connect.php");

if ($_POST && isset($_POST["username"]) && isset($_POST["password"])) {
    switch (true) {
        case empty($_POST["username"]):
            $_SESSION["invalid"] = "Username is required!";
            break;
        case empty($_POST["password"]):
            $_SESSION["invalid"] = "Password is required!";
            break;
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: signin.php");
        exit();
    }

    if (!isset($_SESSION["signin"])) {
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "SELECT * FROM users WHERE username = :username";

        $statement = $db->prepare($query);
        $statement->bindValue(":username", strtolower($username));
        $statement->execute();

        $user = $statement->fetch();

        if (password_verify($password, $user["password"])) {
            $_SESSION["signin"] = true;
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["level"] = $user["level"];
            $_SESSION["username"] = $user["username"];

            header("Location: index.php");
            exit();
        } else {
            $_SESSION["invalid"] = "Invalid username or password!";
            header("Location: signin.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Sign In Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <form method="post" class="form">
                <h2>Sign In</h2>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" autofocus>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password">

                <p class="error">
                    <?= $invalid ?>
                </p>

                <div class="btns">
                    <button name="command" value="signin">Sign In</button>
                </div>
            </form>

            <div class="form-link">
                <a href="signup.php">Sign Up</a>
            </div>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
