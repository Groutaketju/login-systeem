<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("location: login.php");
    exit;
} else {
    header("location: ../page/controlpanel/controlpanel.php");
    exit;
}

