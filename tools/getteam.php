<?php

if (!$_GET || !isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

require("connect.php");

$team_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM teams WHERE team_id = :team_id";

$statement = $db->prepare($query);
$statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
$statement->execute();

$team = $statement->fetch();

if (!$team) {
    header("Location: index.php");
    exit();
}

?>
