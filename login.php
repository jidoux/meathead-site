<!--
    This file manages the login, and lets users use the site if and only if they successfully register or log into their account.
-->

<?php

    session_start();

    $bottomMessage = "";
    //This causes people who just created an account to have to log in afterwards.
    if (isset($_GET['registered'])) {
        $bottomMessage = "Account successfully created. Please log in.";
    }
    else if (isset($_GET['loggedout'])) {
        $bottomMessage = "Successfully logged out.";
    }   
    //This automatically redirects anyone already logged in as admin to the home page of the site.
    else if ($_SESSION['username'] == 'admin') { // this used to just check if username was set, but we set it by default in index now
        header("Location: index.php");
        die;
    }

    //This bit deals with the button that redirects users to the registration page
    if (isset($_POST['goToRegistration'])) {
        header("Location: register.php");
        die;
    }

    function sanitizeData($data) {
        return htmlspecialchars(trim($data));
    }

    //this function returns the filename it was executed in
    function getPostback() {
        return $_SERVER['PHP_SELF'];
    }

    //This function returns a PDO object which accesses the project database, which is needed to check the username & password for entries in the DB
    // I know I shouldnt be pushing passwords but this site was taken down ages ago cuz I quit using it
    // I know how to make .env so in the future this will occur if I feel like continuing this
    function getPDO() {
        $dsn = 'mysql:host=sql110.infinityfree.com;port=3306;dbname=if0_37106593_meathead';
        $username = 'if0_37106593';
        $password = 'MxPT31i531';
        $pdo = new PDO($dsn, $username, $password);
        return $pdo;
    }

    //This function authenticates a login by checking the username, and then the password, against the database entries.
    function verifyLogin($username, $password) {
        try {
            $sqlQuery = "SELECT id, username, password FROM registration";
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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = sanitizeData($_POST['username']);
        $password = sanitizeData($_POST['password']);
        $validLogin = verifyLogin($username, $password);
        if ($validLogin) {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            // if ($_SESSION['username'] == 'admin') { //This is something I had in at one point that directed signed in admins to the admin page, later removed it.
            //     header("Location: admin.php");
            //     die;
            // }
            header("Location: index.php");
            die;
        }
        else {
            $bottomMessage = "Please enter a valid login";
        }
    }
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> <!-- Used raw css styling on homepage and it was brutal, next time im using some framework but for now just using this for other pages -->
    <title>Please Log in</title>
</head>
<body class="w3-sans-serif w3-panel w3-center" style="scale:1.4; margin-left:500px; margin-right:500px; margin-top:50px">
    <h1>Log In:</h2>
    <div class="w3-bottombar w3-topbar w3-leftbar w3-rightbar w3-border-green w3-light-gray">
        <form method="POST" action="<?php getPostback();?>">
            <div class="w3-panel">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required class="w3-light-gray"><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required class="w3-light-gray"><br>
        </div>
        <button type="submit" class="w3-btn w3-green w3-text-black w3-round-large" style="width:287px; margin-bottom:10px">Login</button>
    </form>
    <form method="POST" action="index.php">
        <button class="w3-btn w3-green w3-text-black w3-round-large" style="width:287px">Go to the web page here</button>
    </form>
    <?php echo "<p class=\"w3-center\">$bottomMessage</p>"; ?>
    </div>

</body>
</html>