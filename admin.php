<?php

session_start();

require("tools/leveltwo.php");
require("tools/connect.php");

$query = "SELECT * FROM users ORDER BY level DESC, username";

$statement = $db->prepare($query);
$statement->execute();

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
            <table>
                <caption>Users Table</caption>

                <tr>
                    <th>ID</th>
                    <th>Level</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th><a href="signup.php">Add User</a></th>
                </tr>
                
                <?php foreach ($users as $user): ?>
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
