<?php

// SQL query to select all columns from the 'leagues' table, ordered by 'league_name'
$query = "SELECT * FROM leagues ORDER BY league_name";

$statement = $db->prepare($query);
$statement->execute();

// Fetch all rows (leagues) from the executed statement and store them in the $leagues variable
$leagues = $statement->fetchAll();

?>
