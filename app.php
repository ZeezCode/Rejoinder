<?php
class App {
    public $dbconnect = null;

    function __construct($dbconnect) {
        $this->dbconnect = $dbconnect;
    }

    function getRandomString($length, $charSet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890") {
        $result = "";

        for ($i = 0; $i<$length; $i++) {
            $result .= $charSet[rand(0, strlen($charSet) - 1)];
        }

        return $result;
    }

    function getUserInfo($displayName) {
        $userInfo = null;
        $getUserSQL = sprintf("SELECT * FROM users WHERE display_name = '%s';",
            mysqli_real_escape_string($this->dbconnect, $displayName));
        $getUserQuery = mysqli_query($this->dbconnect, $getUserSQL);
        if (mysqli_num_rows($getUserQuery) != 0) {
            $userInfo = mysqli_fetch_assoc($getUserQuery);
        }
        return $userInfo;
    }

    function registerUser($displayName, $name, $email) {
        $tS = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $createUserSQL = sprintf("INSERT INTO users VALUES (%d, '%s', '%s', '%s', %d, %d, '%s', '%s');",
            mysqli_real_escape_string($this->dbconnect, 0), //uid
            mysqli_real_escape_string($this->dbconnect, $displayName), //display name
            mysqli_real_escape_string($this->dbconnect, $name), //name
            mysqli_real_escape_string($this->dbconnect, $email), //email
            mysqli_real_escape_string($this->dbconnect, $tS), //reg date
            mysqli_real_escape_string($this->dbconnect, $tS), //last seen
            mysqli_real_escape_string($this->dbconnect, $ip), //reg ip
            mysqli_real_escape_string($this->dbconnect, $ip)); //last ip
        return mysqli_query($this->dbconnect, $createUserSQL);
    }

    function updateUserActivity($displayName) {
        $updateUserSQL = sprintf("UPDATE users SET last_seen = %d, last_ip = '%s' WHERE display_name = '%s';",
            mysqli_real_escape_string($this->dbconnect, time()),
            mysqli_real_escape_string($this->dbconnect, $_SERVER['REMOTE_ADDR']),
            mysqli_real_escape_string($this->dbconnect, $displayName));
        mysqli_query($this->dbconnect, $updateUserSQL);
    }

    function userLoggedIn($displayName) {
        $token = $this->getRandomString(32);
        while ($this->getUserFromToken($token) != null) {
            $token = $this->getRandomString(32);
        }
        $updateUserSQL = sprintf("UPDATE users SET login_token = '%s', last_seen = %d, last_ip = '%s' WHERE display_name = '%s';",
            mysqli_real_escape_string($this->dbconnect, $token),
            mysqli_real_escape_string($this->dbconnect, time()),
            mysqli_real_escape_string($this->dbconnect, $_SERVER['REMOTE_ADDR']),
            mysqli_real_escape_string($this->dbconnect, $displayName));
        mysqli_query($this->dbconnect, $updateUserSQL);
    }

    function userLoggedOut($displayName) {
        $updateUserSQL = sprintf("UPDATE users SET login_token = null WHERE display_name = '%s';",
            mysqli_real_escape_string($this->dbconnect, $displayName));
        mysqli_query($this->dbconnect, $updateUserSQL);
    }

    function getUserInfoFromName($name) {
        $userInfo = null;
        $getUserSQL = sprintf("SELECT * FROM users WHERE name = '%s';",
            mysqli_real_escape_string($this->dbconnect, $name));
        $getUserQuery = mysqli_query($this->dbconnect, $getUserSQL);
        if (mysqli_num_rows($getUserQuery) != 0) {
            $userInfo = mysqli_fetch_assoc($getUserQuery);
        }
        return $userInfo;
    }

    function getPageHead($title) {
        return
        "<!DOCTYPE html>
        <html lang=\"en\">
            <head>
                <title>$title</title>
                <meta charset=\"UTF-8\">
                <link href=\"css/reset.css\" rel=\"stylesheet\" type=\"text/css\">
                <link href=\"css/global.css\" rel=\"stylesheet\" type=\"text/css\">
                <link href=\"https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css\" rel=\"stylesheet\" integrity=\"sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN\" crossorigin=\"anonymous\">
                <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js\"></script>
                <script src=\"https://code.jquery.com/ui/1.12.1/jquery-ui.min.js\" integrity=\"sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=\" crossorigin=\"anonymous\"></script>
                <script src=\"notify.min.js\"></script>
            </head>
            <body>";
    }

    function getUserFromToken($token) {
        $userInfo = null;
        $getUserSQL = sprintf("SELECT * FROM users WHERE login_token = '%s';",
            mysqli_real_escape_string($this->dbconnect, $token));
        $getUserQuery = mysqli_query($this->dbconnect, $getUserSQL);
        if (mysqli_num_rows($getUserQuery) != 0) {
            $userInfo = mysqli_fetch_assoc($getUserQuery);
        }
        return $userInfo;
    }

    function getUserFromUID($uid) {
        $userInfo = null;
        $getUserSQL = sprintf("SELECT * FROM users WHERE uid = %d;",
            mysqli_real_escape_string($this->dbconnect, $uid));
        $getUserQuery = mysqli_query($this->dbconnect, $getUserSQL);
        if (mysqli_num_rows($getUserQuery) != 0) {
            $userInfo = mysqli_fetch_assoc($getUserQuery);
        }
        return $userInfo;
    }

    function submitMessage($lobby, $token, $message) {
        $user = $this->getUserFromToken($token);
        $createMessageSQL = sprintf("INSERT INTO messages VALUES (%d, %d, '%s', '%s', %d, '%s');",
            mysqli_real_escape_string($this->dbconnect, 0),
            mysqli_real_escape_string($this->dbconnect, $user['uid']),
            mysqli_real_escape_string($this->dbconnect, $lobby),
            mysqli_real_escape_string($this->dbconnect, $message),
            mysqli_real_escape_string($this->dbconnect, time()),
            mysqli_real_escape_string($this->dbconnect, $_SERVER['REMOTE_ADDR']));
        $result = mysqli_query($this->dbconnect, $createMessageSQL);
        if (!$result) {
            return mysqli_error($this->dbconnect);
        } else return  "";
    }

    function userHasQuestionsDisabled($lobby) {
        return ($this->getUserInfoFromName($lobby)['questions_disabled'] == 1);
    }

    function setUserQuestionsDisabled($token, $disabled) {
        $updateUserSQL = sprintf("UPDATE users SET questions_disabled = %d WHERE login_token = '%s';",
            mysqli_real_escape_string($this->dbconnect, ($disabled ? 1 : 0)),
            mysqli_real_escape_string($this->dbconnect, $token));
        mysqli_query($this->dbconnect, $updateUserSQL);
    }
}