<?php
    session_start();
    require 'app_info.php';
    require 'app.php';

    if (!isset($_GET['id'])) {
        header('Location: error.php?e=1');
        die(0);
    }

    if (!isset($_SESSION['user'])) {
        header('Location: index.php?goto=lobby.php?id=' . $_GET['id']);
        die(0);
    }

    $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        die("Connection error: " . mysqli_connect_error());
    }

    $app = new App($dbconnect);

    $app->updateUserActivity($_SESSION['user']['display_name']);

    if (!$app->userHasName($_GET['id'])) { //User does not exist
        header('Location: error.php?e=2');
        die(0);
    }

    echo $app->getPageHead($_GET['id'] . "'s Lobby");
    include 'components/header.php';

    if ($_SESSION['user']['name'] == $_GET['id']) { //User is owner of lobby
        include 'components/lobby_streamer_pov.php';
    } else {
        include 'components/lobby_viewer_pov.php';
    }

    include 'components/footer.html';
