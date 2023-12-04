<div class="container orange">
    <header>
        <h1><a href="index.php">HoopsTeams</a></h1>

        <nav>
            <ul>
                <li><a href="index.php">Teams</a></li>

                <?php if (isset($_SESSION["login"])): ?>
                    <li><a href="myteams.php">My Teams</a></li>
                    <li><a href="create.php">Create Team</a></li>

                    <?php if ($_SESSION["level"] > 1): ?>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif ?>

                    <li><a href="logout.php">Log Out</a></li>
                <?php else: ?>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="login.php">Log In</a></li>
                <?php endif ?>
            </ul>
        </nav>

        <?php if (isset($_SESSION["login"])): ?>
            <h2>Welcome,
                <?= $_SESSION["username"] ?>!
            </h2>
        <?php endif ?>
    </header>
</div>
