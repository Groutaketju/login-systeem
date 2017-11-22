<?php

/* Database gegevens */
define('DB_SERVER', '127.0.0.1:3307');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'usbw');
define('DB_NAME', 'awb');

/* MySQL connectie */
try{
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    //Set PDO error mode
    $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Kan geen connectie maken. " . $e -> getMessage());
}
