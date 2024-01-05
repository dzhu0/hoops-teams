<?php

// Start a session to manage user data across requests
session_start();

// Include file to check user's authorization level
require("tools/leveltwo.php");

// Include file to retrieve user information
require("tools/getuser.php");

// SQL query to select all users and order them by level in descending order
$query = "SELECT * FROM users ORDER BY level DESC";

// Prepare and execute the SQL query
$statement = $db->prepare($query);
$statement->execute();

// Check if there are at least two users in the database
if ($statement->rowCount() >= 2) {
    // Fetch the user with the highest level (top user)
    $top = $statement->fetch();
    // Fetch the second-highest level user
    $secondTop = $statement->fetch();
}

// Check if the logged-in user has the necessary permissions to edit this user
// Users cannot edit other users with a level greater or equal to their level
// Only top level user can edit their own
if ($_SESSION["level"] <= $user["level"] && $_SESSION["user_id"] !== $top["user_id"]) {
    $_SESSION["error"] = "You cannot edit this user!";
    header("Location: error.php");
    exit();
}

// Initialize an empty string for invalid input messages
$invalid = "";

// Check if there is a previous invalid input message stored in the session
if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

// Check if the form is submitted and the command is to save changes
if ($_POST && isset($_POST["command"]) && $_POST["command"] === "save") {
    switch (true) {
        case !isset($_POST["username"]) || empty($_POST["username"]):
            // If Username is not provided, set an invalid input message
            $_SESSION["invalid"] = "Username is required!";
            break;
        case !isset($_POST["email"]) || empty($_POST["email"]):
            // If Email is not provided, set an invalid input message
            $_SESSION["invalid"] = "Email is required!";
            break;
        case !isset($_POST["level"]) || empty($_POST["level"]):
            // If Level is not provided, set an invalid input message
            $_SESSION["invalid"] = "Level is required!";
            break;
    }

    // If there is an invalid input, redirect to the edit user page with an error message
    if (isset($_SESSION["invalid"])) {
        header("Location: edituser.php?id={$user_id}");
        exit();
    }

    // Filter and sanitize form input data
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $level = filter_input(INPUT_POST, "level", FILTER_SANITIZE_NUMBER_INT);

    // SQL query to check if the provided username already exists in the database
    $query = "SELECT * FROM users WHERE username = :username";

    // Prepare and execute the SQL query
    $statement = $db->prepare($query);
    $statement->bindValue(":username", strtolower($username));
    $statement->execute();

    // Check if the provided username is not the current user's username and already exists
    if ($username !== $user["username"] && $statement->rowCount()) {
        $_SESSION["invalid"] = "Username already exists!";
    }
    // Check if the provided email is invalid
    else if (!filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL)) {
        $_SESSION["invalid"] = "Email is invalid!";
    }
    // Check if the current user is the top user
    else if ((int) $user_id === $top["user_id"]) {
        // Check if the provided level is higher than the second-highest level
        if ($level > $secondTop["level"]) {
            // Update the user's level in the session
            $_SESSION["level"] = $level;
        } else {
            $_SESSION["invalid"] = "Level is too low!";
        }
    }
    // Check if the provided level is higher than or equal to the logged-in user's level
    else if ($level >= $_SESSION["level"]) {
        $_SESSION["invalid"] = "Level is too high!";
    }
    // Check if the provided level is less than or equal to 0
    else if ($level <= 0) {
        $_SESSION["invalid"] = "Level is too low!";
    }

    // If there is an invalid input, redirect to the edit user page with an error message
    if (isset($_SESSION["invalid"])) {
        header("Location: edituser.php?id={$user_id}");
        exit();
    }

    // SQL query to update user information in the database
    $query = "UPDATE users SET username = :username, email = :email, level = :level WHERE user_id = :user_id";

    // Prepare and execute the SQL query
    $statement = $db->prepare($query);
    $statement->bindValue(":username", $username);
    $statement->bindValue(":email", $email);
    $statement->bindValue(":level", $level, PDO::PARAM_INT);
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    // Redirect to the admin page after saving changes
    header("Location: admin.php");
    exit();
}
// If the form is submitted and the command is to delete the user
else if ($_POST && isset($_POST["command"]) && $_POST["command"] === "delete") {
    // Check if the current user is the top user, and prevent deletion in that case
    if ((int) $user_id === $top["user_id"]) {
        $_SESSION["invalid"] = "Cannot delete this user!";
        header("Location: edituser.php?id={$user_id}");
        exit();
    }

    // SQL query to delete the user from the database
    $query = "DELETE FROM users WHERE user_id = :user_id";

    // Prepare and execute the SQL query
    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    // Redirect to the admin page after deleting the user
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

                <!-- Input field for editing the username with the current value pre-filled -->
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?= $user["username"] ?>" autofocus>

                <!-- Input field for editing the email with the current value pre-filled -->
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= $user["email"] ?>">

                <!-- Input field for editing the user's level with the current value pre-filled -->
                <label for="level">Level:</label>
                <input type="number" name="level" id="level" value="<?= $user["level"] ?>">

                <!-- Display any previous invalid input message -->
                <p class="error">
                    <?= $invalid ?>
                </p>

                <!-- Buttons for saving changes and deleting the user -->
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
