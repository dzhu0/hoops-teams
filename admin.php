<?php

session_start();

require("admin/leveltwo.php");
require("admin/connect.php");

$query = "SELECT * FROM users ORDER BY level DESC";

$statement = $db->prepare($query);
$statement->execute();

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
                
                <?php while ($user = $statement->fetch()): ?>
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
                <?php endwhile ?>
            </table>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
