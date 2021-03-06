<?php
    if (isset($_SERVER['HTTP_REFERER'])) {
        if (isset($_GET['lobby']) && isset($_GET['token']) && isset($_GET['message'])) {
            $token = $_GET['token'];
            if (empty($token) || $token == null) {
                $token = "X";
            }

            require '../app_info.php';
            require '../app.php';

            $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            if (mysqli_connect_errno()) {
                die("Connection error");
            }

            $app = new App($dbconnect);
            $streamerInfo = $app->getUserInfoFromName($_GET['lobby']);
            if ($app->getUserFromToken($_GET['token']) != null && $streamerInfo != null) {
                if (!$streamerInfo['questions_disabled']) {
                    $app->submitMessage($_GET['lobby'], $token, $_GET['message']);
                    echo "Successfully sent message!";
                }
                else {
                    echo "Streamer has question submission disabled!";
                }
            } else {
                echo "Failed to send message!";
            }
        }
    }
