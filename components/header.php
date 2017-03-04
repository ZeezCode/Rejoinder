<?php
    if (!isset($_SESSION) || !isset($_SESSION['user'])) {
        die(0);
    }
?>
<div id="header">
    <span id="header_links"><a href="lobby.php?id=<?php echo $_SESSION['user']['name']; ?>"> My Lobby</a> - <a href="reset_session.php">Log Out</a></span>
</div>