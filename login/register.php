<?php

// TODO - filter input superglobals - http://www.php.net/filter_input
//      - email verification

require_once 'dbconnect.php';
require_once '/pwhash/passwordLib.php';

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (!filter_input(INPUT_POST, "username")) {
        $username_err = "Vul een gebruikersnaam in";
    } else {
        $sql = "SELECT id FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
            $param_username = (filter_input(INPUT_POST, "username"));

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $username_err = "Deze naam is al in gebruik.";
                } else {
                    $username = (filter_input(INPUT_POST, "username"));
                }
            } else {
                echo "Oh daar ging wat verkeerd. probeer het nog eens";
            }
        }
        unset($stmt);
    }
    if (!filter_input(INPUT_POST, "password")) {
        $password_err = "Vul een wachtwoord in";
    } elseif (strlen(filter_input(INPUT_POST, "password")) < 6) {
        $password_err = "Wachtwoord moet minimaal 6 tekens zijn.";
    } else {
        $password = (filter_input(INPUT_POST, "password"));
    }

    if (!filter_input(INPUT_POST, "confirm_password")) {
        $confirm_password_err = "Bevestig uw wachtwoord.";
    } else {
        $confirm_password = (filter_input(INPUT_POST, "confirm_password"));
        if ($password != $confirm_password) {
            $confirm_password_err = "Wachtwoord komt niet overeen.";
        }
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $param_password, PDO::PARAM_STR);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_BCRYPT);

            if ($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Er is wat verkeerd gegaan, probeer het later opnieuw";
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
        <title>Account aanmaken</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="wrapper">
            <h2>Account aanmaken</h2>
            <p>Vul het onderstaande formulier in om een account aan te maken.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Username:<sup>*</sup></label>
                    <input type="text" name="username" value="<?php echo $username; ?>"><br>
                    <span class="help-block"><?php echo "\n" . $username_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password:<sup>*</sup></label>
                    <input type="password" name="password" value="<?php echo $password; ?>"><br>
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label>Confirm Password:<sup>*</sup></label>
                    <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                    <span class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="button-submit" value="Registreren">
                </div>
                <p>Heeft u al een account? <a href="login.php">Klik hier</a>.</p>
            </form>
        </div>
    </body>
</html>
