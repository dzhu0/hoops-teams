<?php

if (!$_GET || !isset($_GET["id"])) {
    header("Location: admin.php");
    exit();
}

require("admin/connect.php");

$user_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

$query = "SELECT * FROM users WHERE user_id = :user_id";

$statement = $db->prepare($query);
$statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
$statement->execute();

$user = $statement->fetch();

if (!$user) {
    header("Location: admin.php");
    exit();
}

?>
