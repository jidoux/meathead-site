<!--
    This file is only visible to admins and provides them with CRUD functionality
    I apologize in advance because this file is really poorly organized and just not clean at all
    (changes to db or future additions could screw up stuff here), but it works well nonetheless.
    // TODO add a back
-->

<?php

    session_start();
    
    //This automatically redirects anyone whos username is not admin to the home page
    if ($_SESSION['username'] != 'admin') {
        header("Location: index.php");
        die;
    }

    //This does the logging out for any who click the logout button
    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        setcookie('loggedout', '1', time() + 10, '/'); // 10 second cookie
        header("Location: index.php");
        die;
    }

    //This bit deals with the button that redirects users to the home page
    if (isset($_POST['goToIndex'])) {
        header("Location: index.php");
        die;
    }

    function sanitizeData($data) {
        return htmlspecialchars(trim($data));
    }

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

    //This function returns a table which lets the user choose what crud operation they want to do. Be sure to set $_POST['crudOperation'] to NULL when you're done.
    function getCrudChoice() {
        $crudChoice = '';
        $crudChoice .= '
        <form method="POST" action="'; getPostback(); $crudChoice .= '" autocomplete="off">
            <button type="submit" name="crudOperation" value="create" id="create" class="w3-btn w3-green w3-text-black w3-round-large">Create</button>
            <button type="submit" name="crudOperation" value="read" id="read" class="w3-btn w3-green w3-text-black w3-round-large">Read</button>
            <button type="submit" name="crudOperation" value="update" id="update" class="w3-btn w3-green w3-text-black w3-round-large">Update</button>
            <button type="submit" name="crudOperation" value="delete" id="delete" class="w3-btn w3-green w3-text-black w3-round-large">Delete</button>
        </form>
        ';
        return $crudChoice;
    }

    //This function lets admins choose which table they wish to perform these CRUD operations on.
    //I have seperate operations for each CRUD functionality to remember the previously selected operation. Suboptimal but effective.
    function chooseTableCreate() {
        $tableChoice = '';
        $tableChoice .= '
        <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
            <button type="submit" name="tableChoiceCreate" value="kills_counter" id="kills_counter" class="w3-btn w3-green w3-text-black w3-round-large">Kills Counter</button>
            <button type="submit" name="tableChoiceCreate" value="traits" id="traits" class="w3-btn w3-green w3-text-black w3-round-large">Traits</button>
            <button type="submit" name="tableChoiceCreate" value="trait_drop_times" id="trait_drop_times" class="w3-btn w3-green w3-text-black w3-round-large">Trait Drop Times</button>
        </form>
        ';
        return $tableChoice;
    }

    function chooseTableRead() {
        $tableChoice = '';
        $tableChoice .= '
        <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
            <button type="submit" name="tableChoiceRead" value="registration" id="registration" class="w3-btn w3-green w3-text-black w3-round-large">Registration</button>
            <button type="submit" name="tableChoiceRead" value="kills_counter" id="kills_counter" class="w3-btn w3-green w3-text-black w3-round-large">Kills Counter</button>
            <button type="submit" name="tableChoiceRead" value="traits" id="traits" class="w3-btn w3-green w3-text-black w3-round-large">Traits</button>
            <button type="submit" name="tableChoiceRead" value="trait_drop_times" id="trait_drop_times" class="w3-btn w3-green w3-text-black w3-round-large">Trait Drop Times</button>
        </form>
        ';
        return $tableChoice;
    }

    function chooseTableUpdate() {
        $tableChoice = '';
        $tableChoice .= '
        <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
            <button type="submit" name="tableChoiceUpdate" value="kills_counter" id="kills_counter" class="w3-btn w3-green w3-text-black w3-round-large">Kills Counter</button>
            <button type="submit" name="tableChoiceUpdate" value="traits" id="traits" class="w3-btn w3-green w3-text-black w3-round-large">Traits</button>
            <button type="submit" name="tableChoiceUpdate" value="trait_drop_times" id="trait_drop_times" class="w3-btn w3-green w3-text-black w3-round-large">Trait Drop Times</button>
        </form>
        ';
        return $tableChoice;
    }

    function chooseTableDelete() {
        $tableChoice = '';
        $tableChoice .= '
        <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
            <button type="submit" name="tableChoiceDelete" value="kills_counter" id="kills_counter" class="w3-btn w3-green w3-text-black w3-round-large">Kills Counter</button>
            <button type="submit" name="tableChoiceDelete" value="traits" id="traits" class="w3-btn w3-green w3-text-black w3-round-large">Traits</button>
            <button type="submit" name="tableChoiceDelete" value="trait_drop_times" id="trait_drop_times" class="w3-btn w3-green w3-text-black w3-round-large">Trait Drop Times</button>
        </form>
        ';
        return $tableChoice;
    }

    //This function determines what form to make for the CREATE functionality.
    function determineFormForInsertion($table) {
        $tableChoice = '';
        switch ($table) { // todo ensure this switch works
            case 'kills_counter':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="insertKillsCounter">Num Killed (integer only): </label>
                    <input type="text" name="insertKillsCounter" id="insertKillsCounter" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Insert</button>
                </form>
                ';
                break;
            case 'traits':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="insertName">Name:</label>
                    <input type="text" name="insertName" id="insertName" required class="w3-light-gray"><br><br>
                    <label for="insertTimesDropped">Times Dropped (integer only): </label>
                    <input type="text" name="insertTimesDropped" id="insertTimesDropped" required class="w3-light-gray"><br><br>
                    <label for="insertTraitPoints">Trait Points (can be NULL): </label>
                    <input type="text" name="insertTraitPoints" id="insertTraitPoints" required class="w3-light-gray"><br><br>
                    <label for="insertIsBurn">Is a burn trait (enter 0 or 1): </label>
                    <input type="text" name="insertIsBurn" id="insertIsBurn" required class="w3-light-gray"><br><br>
                    <label for="insertIsScarce">Is a scarce trait (enter 0 or 1): </label>
                    <input type="text" name="insertIsScarce" id="insertisScarce" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Insert</button>
                </form>
                ';
                break;
            case 'trait_drop_times':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="insertTraitDropCounter">Num dropped (integer only): </label>
                    <input type="text" name="insertTraitDropCounter" id="insertTraitDropCounter" required class="w3-light-gray"><br><br>
                    <label for="insertTrait$Id">Trait$id:</label>
                    <input type="text" name="insertTrait$Id" id="insertTrait$Id" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Insert</button>
                </form>
                ';
                break;
        }

    return $tableChoice;
}

function printTable($tableValue) { // another name for this could be readTable since thats what it does
    try {
        $query = "SELECT * FROM $tableValue ORDER BY ID";
        $statement = getPDO()->prepare($query);
        $statement->execute();
        $table = '';
        $table .= "
        <table class=\"w3-table-all\">
            <caption style=\"font-size:25px\">Every entry in table \"$tableValue\"</caption>
            <thead>";
            switch ($tableValue) {
                case 'registration':
                    $table .= '
                    <th>id</th>
                    <th>username</th>
                    <th>password</th>
                    ';
                    foreach ($statement as $row) {
                        $table.= "<tr><td>$row[id]</td><td>$row[username]</td><td>$row[password]</td></tr>";
                    }
                    break;
                case 'kills_counter':
                    $table .= '
                    <th>id</th>
                    <th>kill_date</th>
                    ';
                    foreach ($statement as $row) {
                        $table.= "<tr><td>$row[id]</td><td>$row[kill_date]</td></tr>";
                    }
                    break;
                case 'traits':
                    $table .= '
                    <th>id</th>
                    <th>name</th>
                    <th>times_dropped</th>
                    <th>trait_points</th>
                    <th>is_burn</th>
                    <th>is_scarce</th>
                    </thead>
                    <tbody>';
                    foreach ($statement as $row) {
                        $table.= ("<tr><td>$row[id]</td><td>$row[name]</td><td>$row[times_dropped]</td><td>$row[trait_points]</td><td>$row[is_burn]</td><td>$row[is_scarce]</td></tr>");
                    }
                    break;
                case 'trait_drop_times':
                    $table .= '
                    <th>id</th>
                    <th>trait$id</th>
                    <th>drop_date</th>
                    </thead>
                    <tbody>';
                    foreach ($statement as $row) {
                        $table.= ("<tr><td>$row[id]</td><td>" . $row['trait$id'] . "</td><td>$row[drop_date]</td></tr>");
                    }
                    break;
                }
            $table .= '</tbody></table>';
            return $table;
    }
    catch (PDOException $e) {
        throw $e;
    }
}

   //This function determines what form to make for the UPDATE functionality.
    function determineFormForUpdating($table) {
        $tableChoice = '';
        switch ($table) {
            case 'kills_counter':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="rowToUpdateKillsCounter">Row ID (number):</label>
                    <input type="text" name="rowToUpdateKillsCounter" id="rowToUpdateKillsCounter" required class="w3-light-gray"><br><br>
                    <label for="updateKillDate">Kill date (you must use the exact format or else everything will break): </label>
                    <input type="text" name="updateKillDate" id="updateKillDate" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Update</button>
                </form>
                ';
                break;
            case 'traits':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="rowToUpdateTraits">Row ID (number):</label>
                    <input type="text" name="rowToUpdateTraits" id="rowToUpdateTraits" required class="w3-light-gray"><br><br>
                    <label for="updateName">Name:</label>
                    <input type="text" name="updateName" id="updateName" required class="w3-light-gray"><br><br>
                    <label for="updateTimesDropped">Times Dropped (Must be an int): </label>
                    <input type="text" name="updateTimesDropped" id="updateTimesDropped" required class="w3-light-gray"><br><br>
                    <label for="updateTraitPoints">Trait Points (can be NULL): </label>
                    <input type="text" name="updateTraitPoints" id="updateTraitPoints" required class="w3-light-gray"><br><br>
                    <label for="updateIsBurn">Is a burn trait (enter 0 or 1): </label>
                    <input type="text" name="updateIsBurn" id="updateIsBurn" required class="w3-light-gray"><br><br>
                    <label for="updateIsScarce">Is a scarce trait (enter 0 or 1): </label>
                    <input type="text" name="updateIsScarce" id="updateIsScarce" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Update</button>
                </form>
                ';
                break;
            case 'trait_drop_times':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="rowToUpdateTraitDropTimes">Row ID (number):</label>
                    <input type="text" name="rowToUpdateTraitDropTimes" id="rowToUpdateTraitDropTimes" required class="w3-light-gray"><br><br>
                    <label for="updateTrait$Id">Trait$Id: </label>
                    <input type="text" name="updateTrait$Id" id="updateTrait$Id" required class="w3-light-gray"><br><br>
                    <label for="updateDropTime">Drop date (you must use the exact format or else everything will break): </label>
                    <input type="text" name="updateDropTime" id="updateDropTime" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Update</button>
                </form>
                ';
                break;
        }
        return $tableChoice;
    }

    //This function determines what form to make for the DELETE functionality.
    function determineRowForDeletion($table) {
        $tableChoice = '';
        switch ($table) {
            case 'kills_counter':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="rowToDeleteKillsCounter">Enter an id number to delete:</label>
                    <input type="text" name="rowToDeleteKillsCounter" id="rowToDeleteKillsCounter" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Delete</button>
                </form>
                ';
                break;
            case 'traits':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="rowToDeleteTraits">Enter an id number to delete:</label>
                    <input type="text" name="rowToDeleteTraits" id="rowToDeleteTraits" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Delete</button>
                </form>
                <p>Note that since trait_drop_times uses the traid ids of this table as a foreign key, deleting traits from this table will delete every entry with that trait id from trait_drop_times table</p>
                ';
                break;
            case 'trait_drop_times':
                $tableChoice .= '
                <form method="POST" action="'; getPostback(); $tableChoice .= '" autocomplete="off">
                    <label for="rowToDeleteTraitDropTimes">Enter an id number to delete:</label>
                    <input type="text" name="rowToDeleteTraitDropTimes" id="rowToDeleteTraitDropTimes" required class="w3-light-gray"><br><br>
                    <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large">Delete</button>
                </form>
                ';
                break;
        }
        return $tableChoice;
    }

    //This is the "driver" function of this file, which just runs everything in the appropriate process.
    function runCrud() {
        if (($_SERVER["REQUEST_METHOD"] == 'GET') || (!isset($_POST['crudOperation']))) {
            if (isset($_POST['tableChoiceCreate'])) {
                echo "<p>Fill out this information to be INSERTED</p>";
            }
            else if (isset($_POST['tableChoiceUpdate'])) {
                echo "<p>FIll out this information to be UPDATED</p>";
            }
            else if (isset($_POST['tableChoiceDelete'])) {
                echo "<p>FIll out this information to be DELETED</p>";
            }
            else {
                echo "<p>Select CRUD functionality to perform</p>";
            }
            echo getCrudChoice();
            echo "<p></p>";
        }
        else if (($_SERVER["REQUEST_METHOD"] == 'POST') && (isset($_POST['crudOperation']))) {
            $crudChoice = $_POST['crudOperation'];
            echo ("<p>Select Table to do operation " . strtoupper($crudChoice) . " on</p>");
            switch ($crudChoice) {
                case 'create':
                    echo chooseTableCreate();
                    break;
                case 'read':
                    echo chooseTableRead();
                    break;
                case 'update':
                    echo chooseTableUpdate();
                    break;
                case 'delete':
                    echo chooseTableDelete();
                    break;
                default:
                    echo 'Unknown error! this really should not be possible!'; // if this happens some condition was screwed up :/
            }
            echo "<p>Please be careful. All operations are extremely dangerous except READ. You can literally delete all the data here or change everything.</p>";
        }
        //I should have used another file for this script probably, but whatever. This checks if each operation choice is set and determines the operation to run.
        if ((isset($_POST['tableChoiceCreate']))) {
            $table = $_POST['tableChoiceCreate'];
            echo determineFormForInsertion($table);
        }
        if ((isset($_POST['tableChoiceRead']))) {
            $table = $_POST['tableChoiceRead'];
            echo printTable($table);
        }
        if ((isset($_POST['tableChoiceUpdate']))) {
            $table = $_POST['tableChoiceUpdate'];
            echo determineFormForUpdating($table);
        }
        if ((isset($_POST['tableChoiceDelete']))) {
            $table = $_POST['tableChoiceDelete'];
            echo determineRowForDeletion($table);
        }

        //This is all part of the CREATE and runs the insertion itself
        if (isset($_POST['insertUsername'])) { //This ensures that it will be inserted into the registration table (since username is only set when registration is being inserted into)
            $insertUsername = sanitizeData($_POST['insertUsername']);
            $insertPassword = sanitizeData($_POST['insertPassword']);
            try {
                $sqlQuery = "INSERT INTO registration (username, password) VALUES (?, ?)";
                $statement = getPDO()->prepare($sqlQuery);
                $params = [$insertUsername, $insertPassword];
                if ($statement->execute($params)) {
                    return "Information successfully inserted";
                }
                else {
                    return "Information unsuccessfully inserted";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        if (isset($_POST['insertKillsCounter'])) { //This ensures that it will be inserted into the num_killed table (since insertKillsCounter is only set when kills_counter is being inserted into)
            try {
                $numInsertions = sanitizeData($_POST['insertKillsCounter']);
                $sqlQuery = "INSERT INTO kills_counter (kill_date) VALUES (DEFAULT);";
                for ($i = 0; $i < $numInsertions; $i++) {
                    $statement = getPDO()->prepare($sqlQuery);
                    $statement->execute();
                    // There is no (information successfully inserted) thing here, this may be an error on my part but whatever. TODO add this
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        if (isset($_POST['insertName'])) { //This ensures that it will be inserted into the traits table (since insertName is only set when this table is being inserted into)
            $insertName = sanitizeData($_POST['insertName']);
            $insertTimesDropped = sanitizeData($_POST['insertTimesDropped']);
            $insertTraitPoints = sanitizeData($_POST['insertTraitPoints']);
            $insertIsBurn = sanitizeData($_POST['insertIsBurn']);
            $insertIsScarce = sanitizeData($_POST['insertIsScarce']);
            try {
                $sqlQuery = "INSERT INTO traits (name, times_dropped, trait_points, is_burn, is_scarce) VALUES (?, ?, ?, ?, ?)";
                $statement = getPDO()->prepare($sqlQuery);
                $params = [$insertName, $insertTimesDropped, $insertTraitPoints, $insertIsBurn, $insertIsScarce];
                if ($statement->execute($params)) {
                    return "Information successfully inserted";
                }
                else {
                    return "Information unsuccessfully inserted";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        // todo why is this not else if
        // todo ensure this works
        if (isset($_POST['insertTrait$Id'])) { //This ensures that it will be inserted into the trait_drop_times table (since insertTrait$Id is used for this table)
            $insertTraitId = sanitizeData($_POST['insertTrait$Id']);
            try {
                $numInsertions = sanitizeData($_POST['insertTraitDropCounter']);
                $sqlQuery = 'INSERT INTO trait_drop_times (trait$id) VALUES (?)';
                for ($i = 0; $i < $numInsertions; $i++) {
                    $statement = getPDO()->prepare($sqlQuery);
                    $params = [$insertTraitId];
                    if ($statement->execute($params)) {
                        $sqlQuery2 = "UPDATE traits SET times_dropped = times_dropped + 1 WHERE id = $insertTraitId";
                        $statement2 = getPDO()->prepare($sqlQuery2);
                        $statement2->execute();
                    }
                    // There is no (information successfully inserted) thing here, this may be an error on my part but whatever. TODO this is an error on my part, re add these for all
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        //This is all part of the UPDATE and runs the updating itself
        if (isset($_POST['rowToUpdateRegistration'])) { //This ensures that it will be updated for the registration table
            $rowToUpdate = sanitizeData($_POST['rowToUpdateRegistration']);
            $updateUsername = sanitizeData($_POST['updateUsername']);
            $updatePassword = sanitizeData($_POST['updatePassword']);
            try {
                $sqlQuery = "UPDATE registration SET username = '$updateUsername', password = '$updatePassword' WHERE id = $rowToUpdate";
                $statement = getPDO()->prepare($sqlQuery);
                if ($statement->execute()) {
                    return "Row id $rowToUpdate succesfully updated!";
                }
                else {
                    return "Update failed!";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        if (isset($_POST['rowToUpdateKillsCounter'])) { //This ensures that it will be updated for the kills_counter table
            $rowToUpdate = sanitizeData($_POST['rowToUpdateKillsCounter']);
            $updateKillDate = sanitizeData($_POST['updateKillDate']);
            try {
                $sqlQuery = "UPDATE kills_counter SET kill_date = '$updateKillDate' WHERE id = $rowToUpdate";
                $statement = getPDO()->prepare($sqlQuery);
                if ($statement->execute()) {
                    return "Row id $rowToUpdate succesfully updated!";
                }
                else {
                    return "Update failed!";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        // todo updating the times_dropped value maybe should update the trait_drop_times table but idk
        // or maybe just make it so that people cannot update the times dropped value, i think that maybe better
        // TODO detemrine if you shold even have the option to update times dropped here, im thinking no tbh
        if (isset($_POST['rowToUpdateTraits'])) { //This ensures that it will be updated for the traits table
            $rowToUpdate = sanitizeData($_POST['rowToUpdateTraits']);
            $updateName = sanitizeData($_POST['updateName']);
            $updateTimesDropped = sanitizeData($_POST['updateTimesDropped']);
            $updateTraitPoints = sanitizeData($_POST['updateTraitPoints']);
            $updateIsBurn = sanitizeData($_POST['updateIsBurn']);
            $updateIsScarce = sanitizeData($_POST['updateIsScarce']);
            try {
                $sqlQuery = "UPDATE traits SET name = '$updateName', times_dropped = '$updateTimesDropped', trait_points = '$updateTraitPoints', is_burn = '$updateIsBurn', is_scarce = '$updateIsScarce' WHERE id = $rowToUpdate";
                $statement = getPDO()->prepare($sqlQuery);
                if ($statement->execute()) {
                    return "Row id $rowToUpdate succesfully updated!";
                }
                else {
                    return "Update failed!";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        // todo ensure this is correct
        // todo this will be very hard but you will need to update traits when doing this also
        if (isset($_POST['rowToUpdateTraitDropTimes'])) { //This ensures that it will be updated for the traits table
            $rowToUpdate = sanitizeData($_POST['rowToUpdateTraitDropTimes']);
            $updateDropTime = sanitizeData($_POST['updateDropTime']);
            $updateTraitId = sanitizeData($_POST['updateTrait$Id']);
            try {
                $sqlQuery = "UPDATE trait_drop_times SET " . 'trait$id' . " = $updateTraitId, drop_date = '$updateDropTime' WHERE id = $rowToUpdate";
                $statement = getPDO()->prepare($sqlQuery);
                if ($statement->execute()) {
                    return "Row id $rowToUpdate succesfully updated!";
                }
                else {
                    return "Update failed!";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }

        //This is all part of the DELETE and runs the deletion itself
        if (isset($_POST['rowToDeleteRegistration'])) { //This ensures that it will be properly deleted from the registration table
            $rowToDelete = sanitizeData($_POST['rowToDeleteRegistration']);
            try {
                $sqlQuery = "DELETE FROM registration WHERE id = $rowToDelete";
                $statement = getPDO()->prepare($sqlQuery);
                if ($statement->execute()) {
                    return "Row successfully deleted";
                }
                else {
                    return "Row failed to be deleted";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }

        
        if (isset($_POST['rowToDeleteKillsCounter'])) { //This ensures that it will be properly deleted from the meatheads table
            $rowToDelete = sanitizeData($_POST['rowToDeleteKillsCounter']);
            try {
                $sqlQuery = "DELETE FROM kills_counter WHERE id = $rowToDelete";
                $statement = getPDO()->prepare($sqlQuery);
                if ($statement->execute()) {
                    return "Row successfully deleted";
                }
                else {
                    return "Row failed to be deleted";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }

        if (isset($_POST['rowToDeleteTraits'])) { //This ensures that it will be properly deleted from the traits table
            $rowToDelete = sanitizeData($_POST['rowToDeleteTraits']);
            try {
                $sqlQuery = "DELETE FROM trait_drop_times WHERE trait\$id = $rowToDelete";
                $statement = getPDO()->prepare($sqlQuery);
                if ($statement->execute()) {
                    $sqlQuery2 = "DELETE FROM traits WHERE id = $rowToDelete";
                    $statement2 = getPDO()->prepare($sqlQuery2);
                    if ($statement2->execute()) {
                        return "Row successfully deleted";
                    }
                }
                else {
                    return "Row failed to be deleted";
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }

        if (isset($_POST['rowToDeleteTraitDropTimes'])) { //This ensures that it will be properly deleted from the trait_drop_times table
            $rowToDelete = sanitizeData($_POST['rowToDeleteTraitDropTimes']);
            try {
                $sqlQuery = "SELECT trait\$id FROM trait_drop_times WHERE id = $rowToDelete";
                $statement = getPDO()->prepare($sqlQuery);
                $statement->execute();
                $result = $statement->fetch();
                if ($result) {
                    $idToUpdate = $result['trait$id'];
                    $sqlQuery2 = "DELETE FROM trait_drop_times WHERE id = $rowToDelete";
                    $statement2 = getPDO()->prepare($sqlQuery2);
                    $statement2->execute();
                    $sqlQuery3 = "UPDATE traits SET times_dropped = times_dropped - 1 WHERE id = $idToUpdate";
                    $statement3 = getPDO()->prepare($sqlQuery3);
                    if ($statement3->execute()) {
                        return "Row successfully deleted";
                    }
                    else {
                        return "Row failed to be deleted";
                    }
                }
            }
            catch (PDOException $e) {
                throw $e;
            }
        }
        // working version
        // if (isset($_POST['rowToDeleteTraitDropTimes'])) {
        //     $rowToDelete = sanitizeData($_POST['rowToDeleteTraitDropTimes']);
        //     try {
        //         // Fetch trait$id first
        //         $sqlQuery = 'SELECT trait$id FROM trait_drop_times WHERE id = :rowToDelete';
        //         $statement = getPDO()->prepare($sqlQuery);
        //         $statement->bindParam(':rowToDelete', $rowToDelete, PDO::PARAM_INT);
        //         $statement->execute();
        //         $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        //         if ($result) {
        //             $idToUpdate = $result['trait$id'];
        
        //             // Now delete the row
        //             $sqlQuery2 = "DELETE FROM trait_drop_times WHERE id = :rowToDelete";
        //             $statement2 = getPDO()->prepare($sqlQuery2);
        //             $statement2->bindParam(':rowToDelete', $rowToDelete, PDO::PARAM_INT);
        //             if ($statement2->execute()) {
        //                 // Update times_dropped
        //                 $sqlQuery3 = "UPDATE traits SET times_dropped = times_dropped - 1 WHERE id = :idToUpdate";
        //                 $statement3 = getPDO()->prepare($sqlQuery3);
        //                 $statement3->bindParam(':idToUpdate', $idToUpdate, PDO::PARAM_INT);
        //                 if ($statement3->execute()) {
        //                     return "Row successfully deleted and times_dropped updated.";
        //                 } else {
        //                     return "Row deleted but failed to update times_dropped.";
        //                 }
        //             } else {
        //                 return "Row failed to be deleted.";
        //             }
        //         } else {
        //             return "No row found with id $rowToDelete.";
        //         }
        //     } catch (PDOException $e) {
        //         throw $e;
        //     }
        // }

    }
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> <!-- Used raw css styling on homepage and it was brutal, next time im using some framework but for now just using this for other pages -->
    <title>Admin functionality</title>
</head>
<body class="w3-sans-serif w3-panel w3-center">
    <header class="w3-container" style="padding-top:28px">
        <form method="POST" action="<?php getPostback(); ?>" autocomplete="off">
            <button type="submit" name="goToIndex" value="goToIndex" class="w3-btn w3-green w3-text-black w3-round-large" style="position:absolute; top:10px; left:600px; right:600px">Return to the main site here</button>
        </form>
        <form method="POST" action="<?php getPostback();?>" autocomplete="off">
            <button type="submit" name="logout" value="logout" class="w3-btn w3-green w3-text-black w3-round-large" style="position:absolute; top:10px; right:10px">Log Out</button>
        </form>
    </header>
    <main>
        <h1>Welcome to the admin page</h1>
        <div class="w3-bottombar w3-topbar w3-leftbar w3-rightbar w3-border-green w3-light-gray" style="margin-left:400px; margin-right:400px; margin-top:25px; margin-bottom:25px">
            <?php echo runCrud(); ?>
        </div>
    </main>
</body>
</html>