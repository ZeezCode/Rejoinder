<?php
    session_start();
    require 'app_info.php';
    require 'app.php';

    $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        die("Connection error");
    }

    $app = new App($dbconnect);
    $app->userLoggedOut($_SESSION['user']['display_name']);

    session_unset();
    header('Location: index.php');