<?php

// Check if there are no GET parameters or if 'id' is not set in the GET parameters
if (!$_GET || !isset($_GET["id"])) {

    // Redirect the user to the leagues.php page
    header("Location: leagues.php");
    exit();
}

// Include a file for establishing a database connection
require("connect.php");

// Filter and sanitize the 'id' parameter from the GET request
$league_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

// SQL query to retrieve league information based on the provided 'id'
$query = "SELECT * FROM leagues WHERE league_id = :league_id";

$statement = $db->prepare($query);

// Bind the sanitized 'id' parameter to the prepared statement
$statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
$statement->execute();

// Fetch the result (league information) from the executed statement
$league = $statement->fetch();

// Check if a league with the specified 'id' was not found
if (!$league) {

    // Redirect the user to the leagues.php page
    header("Location: leagues.php");
    exit();
}

?>
