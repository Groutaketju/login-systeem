<?php

// TODO - filter input superglobal - http://www.php.net/filter_input
//      - email verification

require_once 'dbconnect.php';
require_once '/pwhash/passwordLib.php';

$username = $password = "";
$username_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!filter_input(INPUT_POST, "username")) {
        $username_err = 'Vul een gebruikersnaam in.';
    } else {
        $username = (filter_input(INPUT_POST, "username"));
    }

    if (!filter_input(INPUT_POST, "password")) {
        $password_err = 'Vul een wachtwoord in';
    } else {
        $password = (filter_input(INPUT_POST, "password"));
    }

    if (empty($username_err) && empty($password_err)) {
        $sql = "SELECT username, password FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
            $param_username = (filter_input(INPUT_POST, "username"));

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $hashed_password = $row['password'];

                        if (password_verify($password, $hashed_password)) {

                            session_start();

                            $_SESSION['username'] = $username;
                            header("location: welcome.php");
                        } else {
                            $password_err = 'Het wachtwoord is onjuist';
                        }
                    }
                } else {
                    $username_err = 'Geen account gevonden met deze naam';
                }
            } else {
                echo "Oeps er is iets verkeerd gegaan.";
            }
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="wrapper">
            <h2>Login</h2>
            <p>Vul uw gegevens in om in te loggen.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>"
                    <label>Username:<sup>*</sup></label>
                    <input type="text" name="username" value="<?php echo $username; ?>"> <br>
                    <span class="help-block"><?php echo $username_err; ?> </span>
                </div>
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password:<sup>*</sup></label>
                    <input type="password" name="password"><br>
                    <span class="help-block"><?php echo $password_err; ?> </span>
                </div>
                <div class="form-group">
                    <input type="submit" class="button-submit" value="Inloggen">
                </div>
                <p>Heeft u nog geen account? <a href="register.php">Klik hier</a>.</p>
            </form>
        </div>
    </body>
</html>
