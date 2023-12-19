<?php

session_start();

require("tools/leveltwo.php");

if (!$_GET || !isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

require("tools/connect.php");

$comment_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

$query = "DELETE FROM comments WHERE comment_id = :comment_id";

$statement = $db->prepare($query);
$statement->bindValue(":comment_id", $comment_id, PDO::PARAM_INT);
$statement->execute();

if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit();
} else {
    header("Location: index.php");
    exit();
}

?>
