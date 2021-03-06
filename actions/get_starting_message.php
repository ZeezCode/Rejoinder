<?php
    if (isset($_GET['id'])) {
        require '../app_info.php';
        require '../app.php';
        $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
        if (mysqli_connect_errno()) {
            echo "-1";
            die(0);
        }
        $app = new App($dbconnect);
        if ($app->getUserInfoFromName($_GET['id']) != null) {
            $getLatestMessageSQL = sprintf("SELECT mid FROM messages WHERE timestamp < %d ORDER BY mid DESC LIMIT 1;",
                mysqli_real_escape_string($dbconnect, time()));
            $getLatestMessageQuery = mysqli_query($dbconnect, $getLatestMessageSQL);
            if (mysqli_num_rows($getLatestMessageQuery) == 0) {
                echo "0";
            } else {
                echo mysqli_fetch_assoc($getLatestMessageQuery)['mid'];
            }
        } else {
            echo "-1";
        }
    }