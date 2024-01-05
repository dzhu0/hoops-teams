<?php

// Check if $leagues is set and assign it to $temp if it is
if (isset($leagues)) {
    $temp = $leagues;
}

// Include external file to get leagues data
require("tools/getleagues.php");

// Assign the retrieved leagues data to $s_leagues
$s_leagues = $leagues;

// Restore the original value of $leagues if $temp was set
if (isset($temp)) {
    $leagues = $temp;
}

// Check if there are GET parameters
if ($_GET) {
    // Check if 'search_id' is set in GET and assign its value to $search_id
    if (isset($_GET['search_id'])) {
        $search_id = $_GET['search_id'];
    }
    // Check if 'search_keys' is set in GET and assign its value to $search_keys
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
                    <!-- Display My Teams and Create Team links if user is signed in -->
                    <li><a href="myteams.php">My Teams</a></li>
                    <li><a href="createteam.php">Create Team</a></li>
                <?php endif ?>

                <li><a href="leagues.php">Leagues</a></li>

                <?php if (isset($_SESSION["signin"])): ?>
                    <!-- Display My Leagues, Create League, Admin, and Sign Out links if user is signed in -->
                    <li><a href="myleagues.php">My Leagues</a></li>
                    <li><a href="createleague.php">Create League</a></li>

                    <?php if ($_SESSION["level"] > 1): ?>
                        <!-- Display Admin link if user has admin level greater than 1 -->
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif ?>

                    <li><a href="signout.php">Sign Out</a></li>
                <?php else: ?>
                    <!-- Display Sign Up and Sign In links if user is not signed in -->
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="signin.php">Sign In</a></li>
                <?php endif ?>
            </ul>
        </nav>

        <?php if (isset($_SESSION["signin"])): ?>
            <!-- Display welcome message if user is signed in -->
            <h2>Welcome,
                <?= $_SESSION["username"] ?>!
            </h2>
        <?php endif ?>

        <!-- Form for searching teams -->
        <form method="get" action="searchteams.php" class="form-horizontal form-search">
            <select name="search_id" id="search_id">
                <option value="0">All Leagues</option>

                <?php if (count($s_leagues)): ?>
                    <!-- Display league options based on retrieved data -->
                    <?php foreach ($s_leagues as $s_league): ?>
                        <option value="<?= $s_league["league_id"] ?>" <?php if (isset($search_id) && (int) $search_id === $s_league["league_id"]): ?>selected<?php endif ?>>
                            <?= $s_league["league_name"] ?>
                        </option>
                    <?php endforeach ?>
                <?php endif ?>
            </select>

            <!-- Input field for entering search keywords -->
            <input type="text" name="search_keys" id="search_keys" placeholder="Keywords..." <?php if (isset($search_keys)): ?>value="<?= $search_keys ?>" <?php endif ?>>

            <button>Search</button>
        </form>
    </header>
</div>
