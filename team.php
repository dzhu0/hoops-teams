<?php

session_start();

require("tools/getteam.php");
require("tools/getleaguename.php");

$invalid = "";

if (isset($_SESSION["invalid"])) {
    $invalid = $_SESSION["invalid"];
    unset($_SESSION["invalid"]);
}

if ($_POST && isset($_POST["command"]) && $_POST["command"] === "submit") {
    switch (true) {
        case !isset($_POST["content"]) || empty($_POST["content"]):
            $_SESSION["invalid"] = "Content is required!";
            break;
    }

    if (isset($_SESSION["invalid"])) {
        header("Location: team.php");
        exit();
    }

    $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "INSERT INTO comments (user_id, team_id, content)
              VALUES (:user_id, :team_id, :content)";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
    $statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
    $statement->bindValue(":content", $content);
    $statement->execute();

    header("Location: team.php?id={$team_id}");
    exit();
}

$query = "SELECT * FROM comments WHERE team_id = :team_id ORDER BY date DESC";

$statement = $db->prepare($query);
$statement->bindValue(":team_id", $team_id, PDO::PARAM_INT);
$statement->execute();

$comments = $statement->fetchAll();

function getUsername($db, $user_id): string
{
    $query = "SELECT username FROM users WHERE user_id = :user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    $statement->execute();

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

            <div class="form-link">
                <a href="editteam.php?id=<?= $team["team_id"] ?>">Edit</a>
            </div>

            <?php if (isset($_SESSION["signin"])): ?>
                <form method="post" class="form comment">
                    <h2>Comment</h2>

                    <label for="content">Content:</label>
                    <textarea name="content" id="content" rows="10"
                        placeholder="What do you think about this team?" autofocus></textarea>

                    <p class="error">
                        <?= $invalid ?>
                    </p>

                    <div class="btns">
                        <button name="command" value="submit">Submit</button>
                    </div>
                </form>
            <?php endif ?>

            <div class="comments">
                <h2>Comments</h2>
                <hr>
                <?php if (count($comments)): ?>
                    <?php foreach ($comments  as $comment): ?>
                        <div>
                            <h3>
                                <?= getUsername($db, $comment["user_id"]) ?>
                            </h3>

                            <p>
                                <?= date_format(date_create($comment["date"]), "F j, Y, g:i a") ?>
                            </p>
                            <p>
                                <?= $comment["content"] ?>
                            </p>

                            <?php if (isset($_SESSION["level"]) && $_SESSION["level"] >= 2): ?>
                                <a href="deletecomment.php?id=<?= $comment["comment_id"] ?>">Delete</a>
                            <?php endif ?>
                        </div>
                        <hr>
                    <?php endforeach ?>
                <?php else: ?>
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
