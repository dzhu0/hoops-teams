<?php

// Start a session to manage user data across requests
session_start();

// Include the script to check if the user has admin level
require("tools/leveltwo.php");

// Include the script to connect to the database
require("tools/connect.php");

// Query to select all users from the database, ordered by level and username
$query = "SELECT * FROM users ORDER BY level DESC, username";

// Prepare and execute the SQL query
$statement = $db->prepare($query);
$statement->execute();

// Fetch all users
$users = $statement->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>Admin Page</title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <!-- Display a table with user information -->
            <table>
                <caption>Users Table</caption>

                <!-- Table headers -->
                <tr>
                    <th>ID</th>
                    <th>Level</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th><a href="signup.php">Add User</a></th>
                </tr>

                <?php foreach ($users as $user): ?>
                    <!-- Table rows with user data -->
                    <tr>
                        <td>
                            <?= $user["user_id"] ?>
                        </td>
                        <td>
                            <?= $user["level"] ?>
                        </td>
                        <td>
                            <?= $user["username"] ?>
                        </td>
                        <td>
                            <?= $user["email"] ?>
                        </td>
                        <td><a href="edituser.php?id=<?= $user["user_id"] ?>">Edit</a></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
