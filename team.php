<?php

// Start a session to manage user data across requests
session_start();

// Include the script to get team information
require("tools/getteam.php");

// Include the script to get league names
require("tools/getleaguename.php");

// Initialize variable to store error messages for comments
$invalid = "";

// Check if there is a previous invalid session
if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

// Process form submission when the user submits a comment
if ($_POST && isset($_POST["command"]) && $_POST["command"] === "submit") {
    // Validate the content field
    switch (true) {
        case !isset($_POST["content"]) || empty($_POST["content"]):
            $_SESSION["invalid"] = "Content is required!";
            break;
    }

    // Redirect to the team page if there are validation errors
    if (isset($_SESSION["invalid"])) {
        header("Location: team.php");
        exit();
    }

    // Sanitize and retrieve the content of the comment
    $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Insert the comment into the database
    $query = "INSERT INTO comments (user_id, team_id, content) VALUES (:user_id, :team_id, :content)";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
    $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
    $statement->bindValue(":content", $content);
    $statement->execute();

    // Redirect to the team page after submitting the comment
    header("Location: team.php?id={$team_id}");
    exit();
}

// Query to retrieve comments for the team from the database
$query = "SELECT * FROM comments WHERE team_id = :team_id ORDER BY date DESC";

$statement = $db->prepare($query);
$statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
$statement->execute();

// Fetch all comments for the team
$comments = $statement->fetchAll();

// Function to get the username associated with a user_id
function getUsername($db, $user_id): string
{
    $query = "SELECT username FROM users WHERE user_id = :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

    // Fetch and return the username
    $user = $statement->fetch();

    return $user["username"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("markup/head.php") ?>
    <title>
        <?= $team["team_name"] ?>
    </title>
</head>

<body>
    <?php include("markup/header.php") ?>

    <div class="container">
        <main>
            <!-- Display team information -->
            <div class="team">
                <?php if ($team["logo"]): ?>
                    <img src="<?= $team["logo"] ?>" alt="<?= $team["team_name"] ?>" class="logo big">
                <?php else: ?>
                    <div class="no-logo">
                        No Logo
                    </div>
                <?php endif ?>
                <h2>
                    <?= $team["team_name"] ?>
                </h2>
                <div>
                    <p><b>Arena:</b>
                        <?= $team["arena"] ?>
                    </p>
                    <p><b>Home City:</b>
                        <?= $team["home_city"] ?>
                    </p>
                    <p><b>Head Coach:</b>
                        <?= $team["head_coach"] ?>
                    </p>
                    <p><b>League Name:</b>
                        <?= getLeagueName($db, $team["league_id"]) ?>
                    </p>
                </div>
            </div>

            <!-- Display link to edit team -->
            <div class="form-link">
                <a href="editteam.php?id=<?= $team["team_id"] ?>">Edit</a>
            </div>

            <!-- Display comment form if the user is signed in -->
            <?php if (isset($_SESSION["signin"])): ?>
                <form method="post" class="form comment">
                    <h2>Comment</h2>

                    <label for="content">Content:</label>
                    <textarea name="content" id="content" rows="10" placeholder="What do you think about this team?" autofocus></textarea>

                    <!-- Display error message if there is any -->
                    <p class="error">
                        <?= $invalid ?>
                    </p>

                    <!-- Submit comment button -->
                    <div class="btns">
                        <button name="command" value="submit">Submit</button>
                    </div>
                </form>
            <?php endif ?>

            <!-- Display comments section -->
            <div class="comments">
                <h2>Comments</h2>
                <hr>

                <!-- Display comments if available -->
                <?php if (count($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div>
                            <h3>
                                <!-- Display username associated with the comment -->
                                <?= getUsername($db, $comment["user_id"]) ?>
                            </h3>

                            <p>
                                <!-- Display date and time of the comment -->
                                <?= date_format(date_create($comment["date"]), "F j, Y, g:i a") ?>
                            </p>
                            <p>
                                <!-- Display content of the comment -->
                                <?= $comment["content"] ?>
                            </p>

                            <!-- Display delete link for comments if the user is an admin -->
                            <?php if (isset($_SESSION["level"]) && $_SESSION["level"] >= 2): ?>
                                <a href="deletecomment.php?id=<?= $comment["comment_id"] ?>">Delete</a>
                            <?php endif ?>
                        </div>
                        <hr>
                    <?php endforeach ?>
                <?php else: ?>
                    <!-- Display message if no comments are available -->
                    <div>
                        <p>No comments yet.</p>
                    </div>
                    <hr>
                <?php endif ?>
            </div>
        </main>
    </div>

    <?php include("markup/footer.php") ?>
</body>

</html>
