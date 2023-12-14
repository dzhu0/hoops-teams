<?php

if (isset($leagues)) {
    $temp = $leagues;
}

require("tools/getleagues.php");

$s_leagues = $leagues;

if (isset($temp)) {
    $leagues = $temp;
}

if ($_GET) {
    if (isset($_GET['search_id'])) {
        $search_id = $_GET['search_id'];
    }
    if (isset($_GET['search_keys'])) {
        $search_keys = $_GET['search_keys'];
    }
}

?>

<div class="container orange">
    <header>
        <h1><a href="index.php">HoopsTeams</a></h1>

        <nav>
            <ul>
                <li><a href="index.php">Teams</a></li>

                <?php if (isset($_SESSION["signin"])): ?>
                    <li><a href="myteams.php">My Teams</a></li>
                    <li><a href="createteam.php">Create Team</a></li>
                <?php endif ?>

                <li><a href="leagues.php">Leagues</a></li>

                <?php if (isset($_SESSION["signin"])): ?>
                    <li><a href="myleagues.php">My Leagues</a></li>
                    <li><a href="createleague.php">Create League</a></li>

                    <?php if ($_SESSION["level"] > 1): ?>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif ?>

                    <li><a href="signout.php">Sign Out</a></li>
                <?php else: ?>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="signin.php">Sign In</a></li>
                <?php endif ?>
            </ul>
        </nav>

        <?php if (isset($_SESSION["signin"])): ?>
            <h2>Welcome,
                <?= $_SESSION["username"] ?>!
            </h2>
        <?php endif ?>

        <form method="get" action="searchteams.php" class="form-horizontal form-search">
            <select name="search_id" id="search_id">
                <option value="0">All Leagues</option>

                <?php if (count($s_leagues)): ?>
                    <?php foreach ($s_leagues as $s_league): ?>
                        <option value="<?= $s_league["league_id"] ?>" <?php if (isset($search_id) && (int) $search_id === $s_league["league_id"]): ?>selected<?php endif ?>>
                            <?= $s_league["league_name"] ?>
                        </option>
                    <?php endforeach ?>
                <?php endif ?>
            </select>

            <input type="text" name="search_keys" id="search_keys" placeholder="Keywords..." <?php if (isset($search_keys)): ?>value="<?= $search_keys ?>" <?php endif ?>>

            <button>Search</button>
        </form>
    </header>
</div>
