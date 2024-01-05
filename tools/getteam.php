<?php

// Check if there are no GET parameters or if 'id' is not set in the GET parameters
if (!$_GET || !isset($_GET["id"])) {

    // Redirect the user to the index.php page
    header("Location: index.php");
    exit();
}

// Include a file for establishing a database connection
require("connect.php");

// Filter and sanitize the 'id' parameter from the GET request
$team_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

// SQL query to retrieve team information based on the provided 'team_id'
$query = "SELECT * FROM teams WHERE team_id = :team_id";

$statement = $db->prepare($query);

// Bind the sanitized 'team_id' parameter to the prepared statement
$statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
$statement->execute();

// Fetch the result (team information) from the executed statement
$team = $statement->fetch();

// Check if a team with the specified 'team_id' was not found
if (!$team) {

    // Redirect the user to the index.php page
    header("Location: index.php");
    exit();
}

?>
