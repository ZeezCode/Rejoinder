<?php
    $myLobbyURL = "<a href='lobby.php?id=" . $_SESSION['user']['name'] . "'>My Lobby</a> - ";
    $logInOrOut = "<a href='reset_session.php'>Log Out</a>";
    if (!isset($_SESSION) || !isset($_SESSION['user'])) {
        $myLobbyURL = "";
        $logInOrOut = "<a href='index.php'>Log In</a>";
    }
?>
<div id="header">
    <span id="header_links"><?php echo $myLobbyURL . $logInOrOut; ?></span>
</div>
