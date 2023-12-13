<?php

function getLeagueName($db, $league_id)
{
    $query = "SELECT league_name FROM leagues WHERE league_id = :league_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
    $statement->execute();

    $league = $statement->fetch();

    return $league ? $league["league_name"] : "N/A";
}

?>
