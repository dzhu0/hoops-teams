<?php

if (!$_GET || !isset($_GET["id"])) {
    header("Location: leagues.php");
    exit();
}

require("connect.php");

$league_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM leagues WHERE league_id = :league_id";

$statement = $db->prepare($query);
$statement->bindValue(":league_id", $league_id, PDO::PARAM_INT);
$statement->execute();

$league = $statement->fetch();

if (!$league) {
    header("Location: leagues.php");
    exit();
}

?>
