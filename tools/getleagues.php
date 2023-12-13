<?php

$query = "SELECT * FROM leagues ORDER BY league_name";

$statement = $db->prepare($query);
$statement->execute();

$leagues = $statement->fetchAll();

?>
