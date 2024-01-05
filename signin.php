<?php

// Start a session to manage user data across requests
session_start();

// Initialize an empty string to store error messages
$invalid = "";

// Redirect the user to the index page if already signed in
if (isset($_SESSION["signin"])) {
    header("Location: index.php");
    exit();
} else if (isset($_SESSION["invalid"])) {
    // If there are invalid session data, retrieve and unset it
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

// Include the file to establish a database connection
require("tools/connect.php");

// Check if the form is submitted (POST request) and if username and password are set
if ($_POST && isset($_POST["username"]) && isset($_POST["password"])) {

    // Validate and handle empty username or password fields
    switch (true) {
        case empty($_POST["username"]):
            $_SESSION["invalid"] = "Username is required!";
            break;
        case empty($_POST["password"]):
            $_SESSION["invalid"] = "Password is required!";
            break;
    }

    // If there are invalid session data, redirect to the signin page
    if (isset($_SESSION["invalid"])) {
        header("Location: signin.php");
        exit();
    }

    // If not already signed in, proceed to check credentials
    if (!isset($_SESSION["signin"])) {
        // Sanitize and retrieve username and password from the form
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Prepare and execute a database query to retrieve user data
        $query = "SELECT * FROM users WHERE username = :username";

        $statement = $db->prepare($query);
        $statement->bindValue(":username", strtolower($username));
        $statement->execute();

        // Fetch the user data
        $user = $statement->fetch();

        // Verify the password using password_verify function
        if (password_verify($password, $user["password"])) {
            // If the credentials are valid, set session data and redirect to the index page
            $_SESSION["signin"] = true;
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["level"] = $user["level"];
            $_SESSION["username"] = $user["username"];

            header("Location: index.php");
            exit();
        } else {
            // If the credentials are invalid, set an error message and redirect to the signin page
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
            <!-- Display the sign-in form -->
            <form method="post" class="form">
                <h2>Sign In</h2>

                <label for="username">Username:</label>
                <input type="text" name="username" id="username" autofocus>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password">

                <!-- Display any error messages -->
                <p class="error">
                    <?= $invalid ?>
                </p>

                <!-- Display sign-in button -->
                <div class="btns">
                    <button name="command" value="signin">Sign In</button>
                </div>
            </form>

            <!-- Display a link to the sign-up page -->
            <div class="form-link">
                <a href="signup.php">Sign Up</a>
            </div>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
