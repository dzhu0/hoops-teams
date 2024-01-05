<?php

// Check if there are no GET parameters or if 'id' is not set in the GET parameters
if (!$_GET || !isset($_GET["id"])) {

    // Redirect the user to the admin.php page
    header("Location: admin.php");
    exit();
}

// Include a file for establishing a database connection
require("connect.php");

// Filter and sanitize the 'id' parameter from the GET request
$user_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

// SQL query to retrieve user information based on the provided 'user_id'
$query = "SELECT * FROM users WHERE user_id = :user_id";

$statement = $db->prepare($query);

// Bind the sanitized 'user_id' parameter to the prepared statement
$statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
$statement->execute();

// Fetch the result (user information) from the executed statement
$user = $statement->fetch();

// Check if a user with the specified 'user_id' was not found
if (!$user) {

    // Redirect the user to the admin.php page
    header("Location: admin.php");
    exit();
}

?>
