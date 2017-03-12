<?php
    if (isset($_SERVER['HTTP_REFERER'])) {
        if (isset($_GET['lastQuestion']) && isset($_GET['lid']) && isset($_GET['count']) && isset($_GET['first'])) {
            session_start();
            require '../app_info.php';
            require '../app.php';
            $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            if (mysqli_connect_errno()) {
                die(1);
                //die("Connection error: " . mysqli_connect_error());
            }
            $app = new App($dbconnect);

            if (!is_numeric($_GET['count'])) {
                $_GET['count'] = 5;
            } else {
                if ($_GET['count'] > 10 || $_GET['count'] < 1) {
                    $_GET['count'] = 5;
                }
            }

            if (is_numeric($_GET['lastQuestion'])) {
                $getMessagesSQL = sprintf("SELECT * FROM messages WHERE mid > %d AND lid = '%s' ORDER BY mid ASC LIMIT %d;",
                    mysqli_real_escape_string($dbconnect, $_GET['lastQuestion']),
                    mysqli_real_escape_string($dbconnect, $_GET['lid']),
                    mysqli_real_escape_string($dbconnect, $_GET['count']));
                $getMessagesQuery = mysqli_query($dbconnect, $getMessagesSQL);
                $result = array();
                while ($msg = mysqli_fetch_assoc($getMessagesQuery)) {
                    $publicMsg = array();
                    array_push($result, array(
                        'user' => $app->getUserFromUID($msg['uid'])['display_name'],
                        'message' => $msg['message'],
                        'mid' => $msg['mid'],
                        'timestamp' => $msg['timestamp']
                    ));
                }
                echo json_encode($result);
            }
        }
    }