<?php
    if (isset($_SERVER['HTTP_REFERER'])) {
        if (isset($_GET['token'])) {
            require '../app_info.php';
            require '../app.php';
            $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            if (mysqli_connect_errno()) {
                //die(1);
            }
            $app = new App($dbconnect);
            $user = $app->getUserFromToken($_GET['token']);
            if ($user != null) {
                $app->setUserQuestionsDisabled($_GET['token'], !$user['questions_disabled']);
                echo "0";
                die(0);
            }
        }
    }
    echo "1";