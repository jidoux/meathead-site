<!--
    This is the homepage of the site
    TODO (BUG): If you enter an invalid username/password for the first time using site, it will not show $bottomLoginMessage at all, due to my use of header and die on the line where it says "if ($validLogin) {"
    TODO (UPDATE): Add a thing which shows how many admins are logged in/using the site. This can be done with a db table storing session info like last activity timestamp, which can be updated with logout.
-->

<?php

    session_start();
    // setting session variables
    if (!isset($_SESSION["username"])) {
        $_SESSION["username"] = "guest";
        $_SESSION["password"] = "guest";
    }

    $bottomLoginMessage = "";

    //This does the logging out for any who click the logout button
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        setcookie('loggedout', '1', time() + 10, '/'); // 10 second cookie
        header("Location: index.php");
        die;
    }
    else if (isset($_COOKIE['loggedout'])) {
        $bottomLoginMessage = "Successfully logged out.";
        setcookie('loggedout', '', time() - 3600, '/'); // setting expiration time to past to delete the cookie
    }
    else if (isset($_COOKIE['invalidlogin'])) {
        $bottomLoginMessage = "Please enter a valid login";
        setcookie('invalidlogin', '', time() - 3600, '/'); // setting expiration time to past to delete the cookie
    }
    
    //this function does useful things to the inputted data
    function sanitizeData($data) {
        return htmlspecialchars(trim($data));
    }


    //this function returns the filename it was executed in
    function getPostback() {
        return $_SERVER['PHP_SELF'];
    }

    // This function returns a PDO object which accesses the project database
    // I know I shouldnt be pushing passwords but this site was taken down ages ago cuz I quit using it
    // I know how to make .env so in the future this will occur if I feel like continuing this
    function getPDO() {
        $dsn = 'mysql:host=sql110.infinityfree.com;port=3306;dbname=if0_37106593_meathead';
        $username = 'if0_37106593';
        $password = 'MxPT31i531';
        $pdo = new PDO($dsn, $username, $password);
        return $pdo;
    }

    function createStatsTable() {
        try {
            $sqlQuery = "SELECT COUNT(trait\$id) FROM trait_drop_times";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();
            $numDroppedTraits = $statement->fetchColumn();
            $sqlQuery = "SELECT SUM(times_dropped) AS total_burn_drops FROM traits WHERE is_burn = 1";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();
            $numBurnTraits = $statement->fetchColumn();
            if ($numBurnTraits == NULL) { $numBurnTraits = 0; }
            $sqlQuery = "SELECT SUM(times_dropped) AS total_scarce_drops FROM traits WHERE is_scarce = 1";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();
            $numScarceTraits = $statement->fetchColumn();
            if ($numScarceTraits == NULL) { $numScarceTraits = 0; }
            $sqlQuery = "SELECT COUNT(id) FROM kills_counter";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();
            $numMeatheadsKilled = $statement->fetchColumn();
            $numNormalTraits = $numDroppedTraits - $numBurnTraits - $numScarceTraits;
            $numNoTraitDrops = $numMeatheadsKilled - $numDroppedTraits;
            // ternary operator is used to deal with divide by 0 cases (unlikely/unnecessary due to this being all-time stats, but still used)
            $chanceOfTraitDropping = ($numMeatheadsKilled != 0) ? round(($numDroppedTraits / $numMeatheadsKilled) * 100, 2) : 0;
            $chanceOfNoTraitDropping = ($numMeatheadsKilled != 0) ? round((100 - $chanceOfTraitDropping), 2) : 0;
            $chanceOfNormalTraitDropping = ($numMeatheadsKilled != 0) ? round(($numNormalTraits / $numMeatheadsKilled) * 100, 2) : 0;
            $chanceOfBurnTraitDropping = ($numMeatheadsKilled != 0) ? round(($numBurnTraits / $numMeatheadsKilled) * 100, 2) : 0;
            $chanceOfScarceTraitDropping = ($numMeatheadsKilled != 0) ? round(($numScarceTraits / $numMeatheadsKilled) * 100, 2) : 0;
            $percentRegularTraits = ($numDroppedTraits != 0) ? round(($numNormalTraits / $numDroppedTraits) * 100, 2) : 0;
            $percentBurnTraits = ($numDroppedTraits != 0) ? round(($numBurnTraits / $numDroppedTraits) * 100, 2) : 0;
            $percentScarceTraits = ($numDroppedTraits != 0) ? round(($numScarceTraits / $numDroppedTraits) * 100, 2) : 0;

            $statsTable = "";
            $statsTable .= "
                <table class=\"table--all-time-stats\">
                    <caption>All-Time Stats</caption>
                    <thead>
                        <th>Normal Trait</th>
                        <th>Burn Trait</th>
                        <th>Scarce Trait</th>
                        <th>No Trait</th>
                        <th>Meatheads Killed</th>
                        <th>Total Dropped Traits</th>
                        <th>Trait drop chance</th>
                        <th>No trait drop chance</th>
                        <th>Normal trait drop chance</th>
                        <th>Burn trait drop chance</th>
                        <th>Scarce trait drop chance</th>
                        <th>% normal traits</th>
                        <th>% burn traits</th>
                        <th>% scarce traits</th>
                    </thead>
                    <tbody>
                    <tr><td>$numNormalTraits</td><td>$numBurnTraits</td><td>$numScarceTraits</td><td>$numNoTraitDrops</td>
                    <td>$numMeatheadsKilled</td><td>$numDroppedTraits</td><td>$chanceOfTraitDropping%</td><td>$chanceOfNoTraitDropping%</td><td>$chanceOfNormalTraitDropping%</td>
                    <td>$chanceOfBurnTraitDropping%</td><td>$chanceOfScarceTraitDropping%</td><td>$percentRegularTraits%</td><td>$percentBurnTraits%</td><td>$percentScarceTraits%</td></tr>
                    </tbody>
                </table>";
            return $statsTable;
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    function createDropsByDayTable() {
        try {
            $sqlQuery = "SELECT DATE(kill_date) as unique_day, COUNT(*) as count_per_day FROM kills_counter GROUP BY unique_day";
            $meatheadsKilledPerDay = getPDO()->prepare($sqlQuery);
            $meatheadsKilledPerDay->execute();
            $meatheadsKilledPerDayResults = $meatheadsKilledPerDay->fetchAll();
            $sqlQuery = "SELECT DATE(drop_date) as drop_day, COUNT(*) as total_traits_dropped FROM trait_drop_times GROUP BY drop_day ORDER BY drop_day";
            $sumTraitsDroppedPerDay = getPDO()->prepare($sqlQuery);
            $sumTraitsDroppedPerDay->execute();
            $sumTraitsDroppedPerDayResults = $sumTraitsDroppedPerDay->fetchAll();
            $sqlQuery = "SELECT DATE(tdt.drop_date) as drop_day, COUNT(*) as total_burn_traits_dropped FROM trait_drop_times tdt JOIN traits t ON tdt.trait\$id = t.id WHERE t.is_burn = 1 GROUP BY drop_day ORDER BY drop_day";
            $sumBurnTraitsDroppedPerDay = getPDO()->prepare($sqlQuery);
            $sumBurnTraitsDroppedPerDay->execute();
            $sumBurnTraitsDroppedPerDayResults = $sumBurnTraitsDroppedPerDay->fetchAll();
            $sqlQuery = "SELECT DATE(tdt.drop_date) as drop_day, COUNT(*) as total_scarce_traits_dropped FROM trait_drop_times tdt JOIN traits t ON tdt.trait\$id = t.id WHERE t.is_scarce = 1 GROUP BY drop_day ORDER BY drop_day";
            $sumScarceTraitsDroppedPerDay = getPDO()->prepare($sqlQuery);
            $sumScarceTraitsDroppedPerDay->execute();
            $sumScarceTraitsDroppedPerDayResults = $sumScarceTraitsDroppedPerDay->fetchAll();
    
            $table = '
                <table class="table--everyday-stats">
                    <caption>Stats by date</caption>
                    <thead>
                        <tr>
                            <th>Date:</th>
                            <th>Normal Trait</th>
                            <th>Burn Trait</th>
                            <th>Scarce Trait</th>
                            <th>No Trait</th>
                            <th>Meatheads Killed</th>
                            <th>Total Dropped Traits</th>
                            <th>Trait drop chance</th>
                            <th>No trait drop chance</th>
                            <th>Normal trait drop chance</th>
                            <th>Burn trait drop chance</th>
                            <th>Scarce trait drop chance</th>
                            <th>% normal traits</th>
                            <th>% burn traits</th>
                            <th>% scarce traits</th>
                        </tr>
                    </thead>
                    <tbody>';
    
            // combining the results based on each day to get per-day stats
            $dates = array_unique(array_merge(
                array_column($meatheadsKilledPerDayResults, 'unique_day'),
                array_column($sumTraitsDroppedPerDayResults, 'drop_day'),
                array_column($sumBurnTraitsDroppedPerDayResults, 'drop_day'),
                array_column($sumScarceTraitsDroppedPerDayResults, 'drop_day')
            ));
            sort($dates);
    
            foreach ($dates as $date) {
                $numMeatheadsKilled = 0;
                foreach ($meatheadsKilledPerDayResults as $row) {
                    if ($row['unique_day'] == $date) {
                        $numMeatheadsKilled = $row['count_per_day'];
                        break;
                    }
                }
                $numDroppedTraits = 0;
                foreach ($sumTraitsDroppedPerDayResults as $row) {
                    if ($row['drop_day'] == $date) {
                        $numDroppedTraits = $row['total_traits_dropped'];
                        break;
                    }
                }
                $numBurnTraits = 0;
                foreach ($sumBurnTraitsDroppedPerDayResults as $row) {
                    if ($row['drop_day'] == $date) {
                        $numBurnTraits = $row['total_burn_traits_dropped'];
                        break;
                    }
                }
                $numScarceTraits = 0;
                foreach ($sumScarceTraitsDroppedPerDayResults as $row) {
                    if ($row['drop_day'] == $date) {
                        $numScarceTraits = $row['total_scarce_traits_dropped'];
                        break;
                    }
                }
                $numNormalTraits = $numDroppedTraits - $numBurnTraits - $numScarceTraits;
                $numNoTraitDrops = $numMeatheadsKilled - $numDroppedTraits;
                // ternary operator is used to deal with divide by 0 cases, its ugly but it works
                $chanceOfTraitDropping = ($numMeatheadsKilled != 0) ? round(($numDroppedTraits / $numMeatheadsKilled) * 100, 2) : 0;
                $chanceOfNoTraitDropping = ($numMeatheadsKilled != 0) ? round((100 - $chanceOfTraitDropping), 2) : 0;
                $chanceOfNormalTraitDropping = ($numMeatheadsKilled != 0) ? round(($numNormalTraits / $numMeatheadsKilled) * 100, 2) : 0;
                $chanceOfBurnTraitDropping = ($numMeatheadsKilled != 0) ? round(($numBurnTraits / $numMeatheadsKilled) * 100, 2) : 0;
                $chanceOfScarceTraitDropping = ($numMeatheadsKilled != 0) ? round(($numScarceTraits / $numMeatheadsKilled) * 100, 2) : 0;
                $percentRegularTraits = ($numDroppedTraits != 0) ? round(($numNormalTraits / $numDroppedTraits) * 100, 2) : 0;
                $percentBurnTraits = ($numDroppedTraits != 0) ? round(($numBurnTraits / $numDroppedTraits) * 100, 2) : 0;
                $percentScarceTraits = ($numDroppedTraits != 0) ? round(($numScarceTraits / $numDroppedTraits) * 100, 2) : 0;
                
                $table .= "<tr><td>$date</td><td>$numNormalTraits</td><td>$numBurnTraits</td><td>$numScarceTraits</td><td>$numNoTraitDrops</td>
                    <td>$numMeatheadsKilled</td><td>$numDroppedTraits</td><td>$chanceOfTraitDropping%</td><td>$chanceOfNoTraitDropping%</td><td>$chanceOfNormalTraitDropping%</td>
                    <td>$chanceOfBurnTraitDropping%</td><td>$chanceOfScarceTraitDropping%</td><td>$percentRegularTraits%</td><td>$percentBurnTraits%</td><td>$percentScarceTraits%</td></tr>";
            }
    
            $table .= '
                    </tbody>
                </table>';
    
            return $table;
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    // this function will generate a table which will show the meathead kills today, normal traits today, burn traits today, scarce traits today, and empty meatheads today
    function showDailyInfo() {
        $dailyInfoTable = '';
        try {
            $sqlQuery = "SELECT COUNT(*) FROM trait_drop_times WHERE DATE(drop_date) = CURDATE()";
            $allTraitsDroppedToday = getPDO()->prepare($sqlQuery);
            $allTraitsDroppedToday->execute();
            $allTraitsDroppedToday = $allTraitsDroppedToday->fetchColumn();
            if ($allTraitsDroppedToday == NULL) { $allTraitsDroppedToday = 0; }
            $sqlQuery = "SELECT COUNT(*) FROM trait_drop_times JOIN traits ON trait_drop_times.trait\$id = traits.id WHERE traits.is_burn = 1 AND DATE(trait_drop_times.drop_date) = CURDATE()";
            $burnTraitsDroppedToday = getPDO()->prepare($sqlQuery);
            $burnTraitsDroppedToday->execute();
            $burnTraitsDroppedToday = $burnTraitsDroppedToday->fetchColumn();
            if ($burnTraitsDroppedToday == NULL) { $burnTraitsDroppedToday = 0; }
            $sqlQuery = "SELECT COUNT(*) FROM trait_drop_times JOIN traits ON trait_drop_times.trait\$id = traits.id WHERE traits.is_scarce = 1 AND DATE(trait_drop_times.drop_date) = CURDATE()";
            $scarceTraitsDroppedToday = getPDO()->prepare($sqlQuery);
            $scarceTraitsDroppedToday->execute();
            $scarceTraitsDroppedToday = $scarceTraitsDroppedToday->fetchColumn();
            if ($scarceTraitsDroppedToday == NULL) { $scarceTraitsDroppedToday = 0; }
            $sqlQuery = "SELECT COUNT(*) FROM trait_drop_times JOIN traits ON trait_drop_times.trait\$id = traits.id WHERE traits.is_burn = 0 AND traits.is_scarce = 0 AND DATE(trait_drop_times.drop_date) = CURDATE()";
            $normalTraitsDroppedToday = getPDO()->prepare($sqlQuery);
            $normalTraitsDroppedToday->execute();
            $normalTraitsDroppedToday = $normalTraitsDroppedToday->fetchColumn();
            if ($normalTraitsDroppedToday == NULL) { $normalTraitsDroppedToday = 0; }
            $sqlQuery = "SELECT COUNT(*) FROM kills_counter WHERE DATE(kill_date) = CURDATE()";
            $meatheadKillsToday = getPDO()->prepare($sqlQuery);
            $meatheadKillsToday->execute();
            $meatheadKillsToday = $meatheadKillsToday->fetchColumn();
            if ($meatheadKillsToday == NULL) { $meatheadKillsToday = 0; }
            $numEmptyMeatheadsToday = $meatheadKillsToday - $allTraitsDroppedToday;
            $dailyInfoTable .= "
                <table class=\"table--today-stats\">
                    <tbody>
                    <tr><td>Meathead Kills Today:</td><td>$meatheadKillsToday</td></tr>
                    <tr><td>Normal Traits Today:</td><td>$normalTraitsDroppedToday</td></tr>
                    <tr><td>Burn Traits Today:</td><td>$burnTraitsDroppedToday</td></tr>
                    <tr><td>Scarce Traits Today:</td><td>$scarceTraitsDroppedToday</td></tr>
                    <tr><td>Empty Meatheads Today:</td><td>$numEmptyMeatheadsToday</td></tr>
                    </tbody>
                </table>";
        }
        catch (PDOException $e) {
            throw $e;
        }
        return $dailyInfoTable;
    }

    // this function creates a table which shows all traits, times dropped, and buttons which admins can use to add or remove drops for it. Its what admins use to record trait drops.
    // this table will ONLY appear for admins. Normal users will not see it at all.
    function createTraitEntryTable() {
        $table = '';
        try {
            $sqlQuery = "SELECT id, name, times_dropped FROM traits";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();
            $table .= '
            <table class="table--trait-entry">
                <caption>Update data</caption>
                <tbody>';
                    $table .= "
                    <form method=\"POST\" action=\""; getPostback(); $table.= "\" autocomplete=\"off\">";
                    foreach ($statement as $row) {
                        $table.= ("<tr><td>$row[name]</td>
                        <td><button type=\"submit\" name=\"addTraitDrop\" value=\"$row[id]\" class=\"sidebar__button sidebar__button--add\">Add</button></td>
                        <td><button type=\"submit\" name=\"removeTraitDrop\" value=\"$row[id]\" class=\"sidebar__button sidebar__button--remove\">Remove</button></td>
                        </tr>");
                    }
                    $table .= '
                    </form>
                </tbody>
            </table>
            </p>Remove removes the most recent entry</p>';
        }
        catch (PDOException $e) {
            throw $e;
        }
        return $table;
    }

    // this function is in the admin sidebar, used for admins to modify the meathead kills per day
    function inputMeatheadKills() {
        $inputKillsForm = '';
        $inputKillsForm .= '
        <form method="POST" action="'; getPostback(); $inputKillsForm .= '" autocomplete="off">
            <label for="insertMeatheadsKilled">Num Killed in your past game(integer only): </label>
            <select id="insertMeatheadsKilled" name="insertMeatheadsKilled">';
            for ($i = 0; $i <= 50; $i++) {
                $inputKillsForm .= "<option value=$i>$i</option>";
            }
            $inputKillsForm .= '
            </select>
            <input type="submit" value="Add" class="button">
        </form>
        <form method="POST" action="'; getPostback(); $inputKillsForm .= '" autocomplete="off">
            <label for="removeMeatheadKill">How many meathead kills to remove(recent first): </label>
            <select id="removeMeatheadKill" name="removeMeatheadKill">';
            for ($i = 0; $i <= 50; $i++) {
                $inputKillsForm .= "<option value=$i>$i</option>";
            }
            $inputKillsForm .= '
            </select>
            <input type="submit" value="Remove" class="button">
        </form>';
        return $inputKillsForm;
    }

    // generates the sidebar for admins which allows them to add/remove meathead kills
    function generateAdminSidebar() {
        $sidebar = '';
        if ($_SESSION['username'] == 'admin') {
            $sidebar .= '
            <aside class="sidebar">';
            $sidebar .= inputMeatheadKills();
            $sidebar .= createTraitEntryTable();
            $sidebar .= '
            </aside>';
        }
        return $sidebar;
    }

    // This function generates the admin button only if an admin is actually logged in
    // this function call is currently commented out because I don't really want this page implemented yet - TODO determine if this should stay the case
    function generateAdminButton() {
        $adminButton = '';
        if ($_SESSION['username'] == 'admin') {
            $adminButton .= '
            <form method="POST" action="admin.php" autocomplete="off">
                <button class="button button--admin-page">Go to Admin Page (only visible to admins)</button>
            </form>
            ';
        }
        return $adminButton;
    }

    // creates logout button which goes on the top right of the screen, in the place of the login box
    function generateLogoutButton() {
        $logoutButton = '';
        if ($_SESSION['username'] == 'admin') {
            $logoutButton .= '
            <form method="POST" action="' . getPostback() . '" autocomplete="off">
                <button type="submit" name="logout" value="logout" class="button button--logout">Log Out</button>
            </form>
        ';
        }
        return $logoutButton;
    }

    // this function creates the login box which is on the top right of the screen
    function generateLoginBox($bottomLoginMessage) {
        $loginButton = '';
        if ($_SESSION['username'] != 'admin') {
            $loginButton .= '
            <div class="login">
            <p>Admin log-on</p>
            <form method="POST" action="' . getPostback() . '" autocomplete="off">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required placeholder="Username"><br><br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required placeholder="Password"><br>
                <button type="submit" class="button botton--login">Login</button>
            </form>'
            . $bottomLoginMessage . '
            </div>
        ';
        }
        return $loginButton;
    }

    //This function authenticates a login by checking the username, and then the password, against the database entries.
    function verifyLogin($username, $password) {
        try {
            $sqlQuery = "SELECT username, password FROM registration";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();
            while ($row = $statement->fetch()) {
                if ($row["username"] == $username) {
                    if (password_verify($password, $row["password"])) {
                        return true;
                    }
                }
            }
        }
        catch (PDOException $e) {
            throw $e;
        }
        return false;
    }

    // this function creates an associate array of name and times dropped of each trait, returned as a json encoded
    // array of values to be used in the javascript to generate the bar and pie graphs
    function getTraitTotals() {
        $values = array();
        try {
            $sqlQuery = "SELECT name, times_dropped FROM traits";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();

            while ($row = $statement->fetch()) {
                $values += [$row["name"] => $row["times_dropped"]];
            }
        }
        catch (PDOException $e) {
            throw $e;
        }
        return json_encode($values);
    }

    // this function creates an associate array of name and trait point values of each trait, returned as a json encoded
    // array of values to be used in the javascript to determine the color of each trait in graphs (i.e. 1 trait point traits are 1 color, 2 trait point traits are a different color)
    function getTraitPoints() {
        $values = array();
        try {
            $sqlQuery = "SELECT name, trait_points FROM traits";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();

            while ($row = $statement->fetch()) {
                $values += [$row["name"] => $row["trait_points"]];
            }
        }
        catch (PDOException $e) {
            throw $e;
        }
        return json_encode($values);
    }

    // this bit deals with the logging in
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = sanitizeData($_POST['username']);
        $password = sanitizeData($_POST['password']);
        $validLogin = verifyLogin($username, $password);
        if ($validLogin) {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            header("Location: index.php");
            die;
        }
        else {
            setcookie('invalidlogin', '1', time() + 10, '/');
        }
    }

    // this bit deals with adding a trait drop via the chart
    if (isset($_POST['addTraitDrop'])) {
        $traitIdToAdd = sanitizeData($_POST['addTraitDrop']);
        try {
            $sqlQuery = 'INSERT INTO trait_drop_times (trait$id) VALUES (?)';
            $statement = getPDO()->prepare($sqlQuery);
            $params = [$traitIdToAdd];
            if ($statement->execute($params)) {
                $sqlQuery2 = "UPDATE traits SET times_dropped = times_dropped + 1 WHERE id = $traitIdToAdd";
                $statement2 = getPDO()->prepare($sqlQuery2);
                if ($statement2->execute()) {
                    header("Location: index.php");
                    die;
                }
            }
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    // this bit deals with removing a trait drop via the chart
    if (isset($_POST['removeTraitDrop'])) {
        $traitIdToRemove = sanitizeData($_POST['removeTraitDrop']);
        try {
            $sqlQuery = "SELECT id FROM trait_drop_times WHERE trait\$id = $traitIdToRemove";
            $statement = getPDO()->prepare($sqlQuery);
            $statement->execute();
            if ($statement->fetch() != 0) {
                $sqlQuery2 = "DELETE FROM trait_drop_times WHERE id = (SELECT id FROM (SELECT id FROM trait_drop_times WHERE trait\$id = $traitIdToRemove ORDER BY drop_date DESC LIMIT 1) AS subquery)";
                $statement2 = getPDO()->prepare($sqlQuery2);
                if ($statement2->execute()) {
                    $sqlQuery3 = "UPDATE traits SET times_dropped = times_dropped - 1 WHERE id = $traitIdToRemove";
                    $statement3 = getPDO()->prepare($sqlQuery3);
                    if ($statement3->execute()) {
                        header("Location: index.php");
                        die;
                    }
                }
            }
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    // this deals with the form which will take for input the num meatheads killed after a game or whatever
    if (isset($_POST['insertMeatheadsKilled'])) {
        try {
            $numInsertions = sanitizeData($_POST['insertMeatheadsKilled']);
            $sqlQuery = "INSERT INTO kills_counter (kill_date) VALUES (DEFAULT)";
            for ($i = 0; $i < $numInsertions; $i++) {
                $statement = getPDO()->prepare($sqlQuery);
                $statement->execute();
            }
            header("Location: index.php");
            die;
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    if (isset($_POST['removeMeatheadKill'])) {
        try {
            $numInsertions = sanitizeData($_POST['removeMeatheadKill']);
            $sqlQuery = "DELETE FROM kills_counter ORDER BY kill_date DESC LIMIT 1";
            for ($i = 0; $i < $numInsertions; $i++) {
                $statement = getPDO()->prepare($sqlQuery);
                $statement->execute();
            }
            header("Location: index.php");
            die;
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.plot.ly/plotly-2.32.0.min.js" charset="utf-8"></script>
    <link rel="stylesheet" href="styles.css">
    <title>Meathead Drops</title>
</head>
<body>
    <header>
        <h1 class="site-description__title">Meathead Trait Drop Data</h1>
    </header>  
    <main>
        <div class="main__row--1">
            <div class="main__container--1">
                <?php //echo generateAdminButton(); TODO re-add this if its decided admin page should be implemented ?>
                <div class="main__container--2">
                    <?php echo createStatsTable(); ?>
                </div>
            </div>
            <?php echo showDailyInfo(); ?>
            <img src="meathead-pic-3.png" class="meathead-pic-3" alt="picture of a meathead with a thumbs down">
            <?php echo generateLogoutButton(); ?>
            <?php echo generateLoginBox($bottomLoginMessage); ?>
        </div>
        <div class="graphs">
            <div class="graphs__column-container">
                <div id="bar-graph-area-all-time-js"></div>
                <img src="meathead-pic-6.png" class="meathead-pic-6" alt="picture of a burn trait">
            </div>
            <div id="pie-chart-area-all-time-js"></div>
            <?php echo generateAdminSidebar(); ?>
        </div>
        <img src="meathead-pic-2.png" class="meathead-pic-2" alt="hunt showdown the meathead picture">
    </main>
    <script> // these variables are used in the plots.js file. Doing this now because plots.js needs the pie chart and bar graph area divs to be loaded first.
        var traitsAndTotals = <?php echo getTraitTotals() ?>;
        var traitPoints = <?php echo getTraitPoints() ?>;
    </script>
    <script src="plots.js"></script>
    <footer>
        <div class="footer__container--1">
        <?php echo createDropsByDayTable(); ?>
        </div>
        <div class="footer__column--1">
            <div class="footer__row--1">
                <div class="footer__column--2">
                    <img src="meathead-pic-4.png" class="meathead-pic-4" alt="picture of a normal meathead in the wild 1">
                    <img src="meathead-pic-3.png" class="meathead-pic-3-2" alt="picture of a meathead with a thumbs down">
                </div>
                <img src="meathead-pic-5.png" class="meathead-pic-5" alt="picture of a normal trait (silent killer)">
            </div>
            <img src="meathead-pic-1.png" class="meathead-pic-1" alt="picture of a normal meathead in the wild 2">
        </div>
    </footer>
</body>
</html>