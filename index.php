<?php
    session_start();
    require 'app_info.php';
    require 'app.php';

    $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        die("Connection error: " . mysqli_connect_error());
    }

    $app = new App($dbconnect);

    if (isset($_GET['goto'])) {
        $_SESSION['goto'] = $_GET['goto'];
    }

    if (isset($_SESSION['user'])) { //User is already logged in
        $app->updateUserActivity($_SESSION['user']['display_name']);
        header('Location: lobby.php?id=' . $_SESSION['user']['name']);
    } else if (isset($_GET['login'])) { //User clicked login button, redirect to Twitch
        $sendToURL = "https://api.twitch.tv/kraken/oauth2/authorize"
                        ."?response_type=code"
                        ."&client_id=" . TWITCH_API_KEY
                        ."&redirect_uri=" . TWITCH_REDIRECT_URL
                        ."&scope=channel_read+user_read"
                        ."&state=" . session_id();
        header('Location: ' . $sendToURL);
    } else if (isset($_GET['code']) && isset($_GET['scope']) && isset($_GET['state'])) { //User authorized log in
        $url = 'https://api.twitch.tv/kraken/oauth2/token';
        $data = array('client_id' => TWITCH_API_KEY, 'client_secret' => TWITCH_API_SECRET, 'grant_type' => 'authorization_code',
            'redirect_uri' => TWITCH_REDIRECT_URL, 'code' => $_GET['code'], 'state' => session_id());

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context));
        if ($result === false) {
            die("Failed to retrieve OAuth Token from Twitch API.");
        }

        $profileInfo = json_decode(file_get_contents("https://api.twitch.tv/kraken/user?oauth_token=" . $result->access_token));

        if (isset($profileInfo->display_name)) { //API successfully returned user info
            if ($app->getUserInfo($profileInfo->display_name) == null) { //User does not exist
                if (!$app->registerUser($profileInfo->display_name, $profileInfo->name, $profileInfo->email)) { //Failed to register user
                    //Show error
                }
            }
            //User is definitely registered
            $app->userLoggedIn($profileInfo->display_name);
            $_SESSION['user'] = $app->getUserInfo($profileInfo->display_name);
            if (isset($_SESSION['goto'])) {
                $url = $_SESSION['goto'];
                unset($_SESSION['goto']);
                header('Location: ' . $url);
            }
            else
                header('Location: lobby.php?id=' . $profileInfo->name);
        }

    } else { //User has not logged in, show login button
        echo $app->getPageHead("Log In to Rejoinder");
        include 'components/login_form.html';
        include 'components/footer.html';
    }