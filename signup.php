<?php

// Start a session to manage user data across requests
session_start();

// Variable to store error messages
$invalid = "";

// Check if the user is signed in and has a level less than 2 (assuming 2 is an admin level)
if (isset($_SESSION["signin"]) && $_SESSION["level"] < 2) {
    // Redirect non-admin users to the index page
    header("Location: index.php");
    exit();
} else if (isset($_SESSION["invalid"])) {
    // If there is a previous invalid session, retrieve and unset it
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

// Include the connection script to connect to the database
require("tools/connect.php");

// Process form submission when the user submits the signup form
if ($_POST && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
    // Validate form input fields
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

    // Redirect to the signup page if there are validation errors
    if (isset($_SESSION["invalid"])) {
        header("Location: signup.php");
        exit();
    }

    // Sanitize and retrieve form input values
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirm_password = filter_input(INPUT_POST, "confirm_password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Check if the username already exists in the database
    $query = "SELECT * FROM users WHERE username = :username";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", strtolower($username));
    $statement->execute();

    // If the username already exists, set an error message
    if ($statement->rowCount()) {
        $_SESSION["invalid"] = "Username already exists!";
    } else if (!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
        // If the email is invalid, set an error message
        $_SESSION["invalid"] = "Email is invalid!";
    } else if ($password !== $confirm_password) {
        // If passwords do not match, set an error message
        $_SESSION["invalid"] = "Passwords do not match!";
    }

    // Redirect to the signup page if there are validation errors
    if (isset($_SESSION["invalid"])) {
        header("Location: signup.php");
        exit();
    }

    // Prevent users that are not signed in from signing up except admin user
    if (!isset($_SESSION["signin"])) {

        // If not signed in, set an error message in the session variable
        $_SESSION["error"] = "Sorry, sign up is currently disabled!";

        // Redirect the user to the error.php page
        header("Location: error.php");
        exit();
    }

    // Insert user data into the database
    $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

    $statement = $db->prepare($query);
    $statement->bindValue(":username", strtolower($username));
    $statement->bindValue(":email", strtolower($email));
    $statement->bindValue(":password", password_hash($password, PASSWORD_DEFAULT));
    $statement->execute();

    // Retrieve the user data from the database
    $user_id = $db->lastInsertId();

    $query = "SELECT * FROM users WHERE user_id = :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $user_id);
    $statement->execute();

    $user = $statement->fetch();

    // Redirect the user based on whether it's an admin signup or a regular signup
    if (isset($_SESSION["signin"])) {
        header("Location: admin.php");
        exit();
    } else {
        // Set session variables for the newly signed up user
        $_SESSION["signin"] = true;
        $_SESSION["user_id"] = $user["user_id"];
        $_SESSION["level"] = $user["level"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];

        // Redirect to the index page
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
            <!-- Sign-up form -->
            <form method="post" class="form">
                <h2>Sign Up</h2>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" autofocus>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email">

                <label for="password">Password:</label>
                <input type="password" name="password" id="password">

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password">

                <!-- Display error message if there is any -->
                <p class="error">
                    <?= $invalid ?>
                </p>

                <!-- Sign-up button -->
                <div class="btns">
                    <button name="command" value="signup">Sign Up</button>
                </div>
            </form>

            <!-- Display link to the Sign In page if not already signed in -->
            <?php if (!isset($_SESSION["signin"])): ?>
                <div class="form-link">
                    <a href="signin.php">Sign In</a>
                </div>
            <?php endif ?>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
