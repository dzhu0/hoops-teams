<?php

// Include a file containing database connection information
require("dbinfo.php");

// Attempt to create a new PDO (PHP Data Objects) instance for database connection
try {
    // Create a PDO instance using the provided database DSN, username, and password
    $db = new PDO(DB_DSN, DB_USER, DB_PASS);
} catch (PDOException $e) {
    // If an exception (error) occurs during the connection attempt,
    // print an error message with details from the exception
    print "Error: " . $e->getMessage();
    die();
}

?>
