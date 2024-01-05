<?php

// Define a function to retrieve the league name based on the league ID
function getLeagueName($db, $league_id)
{
    // SQL query to select the league name from the 'leagues' table for a given 'league_id'
    $query = "SELECT league_name FROM leagues WHERE league_id = :league_id";

    $statement = $db->prepare($query);

    // Bind the 'league_id' parameter to the prepared statement
    $statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
    $statement->execute();

    // Fetch the result (league information) from the executed statement
    $league = $statement->fetch();

    // Return the league name if found, otherwise return "N/A"
    return $league ? $league["league_name"] : "N/A";
}

?>
