<?php

// Start a session to manage user data across requests
session_start();

// Include file to check if the user has level two access
require("tools/leveltwo.php");

// Check if no GET parameters or comment ID is provided
if (!$_GET || !isset($_GET["id"])) {
    // Redirect to the index page if no valid comment ID is provided
    header("Location: index.php");
    exit();
}

// Include file to establish a database connection
require("tools/connect.php");

// Filter and sanitize the comment ID from the GET parameters
$comment_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

// SQL query to delete a comment from the database based on its comment ID
$query = "DELETE FROM comments WHERE comment_id = :comment_id";

// Prepare and execute the SQL query with the provided comment ID
$statement = $db->prepare($query);
$statement->bindValue(":comment_id", $comment_id, PDO::PARAM_INT);
$statement->execute();

// Check if there is a referring page (HTTP_REFERER) and redirect back to it
if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit();
} else {
    // If no referring page, redirect to the index page
    header("Location: index.php");
    exit();
}

?>
