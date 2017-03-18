<?php
    session_start();
    if (!isset($_GET['e']) || !is_numeric($_GET['e'])) {
        header('Location: index.php');
    }
    require 'app_info.php';
    require 'app.php';

    $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        die("Connection error");
    }

    $app = new App($dbconnect);

    echo $app->getPageHead("ERROR | Rejoinder");
    include 'components/header.php';
    $error = "Unknown error";
    if ($_GET['e'] == 1) {
        $error = "No lobby ID was supplied!";
    } else if ($_GET['e'] == 2) {
        $error = "The given lobby ID does not exist!";
    }
?>
<div style="text-align: center">
    <span style="color: red; font-family: 'open sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 48px;"><?php echo $error; ?></span>
</div>
<?php
    include 'components/footer.html';
