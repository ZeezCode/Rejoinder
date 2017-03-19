<?php
    session_start();
    require 'app_info.php';
    require 'app.php';

    $dbconnect = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (mysqli_connect_errno()) {
        die("Unable to retrieve Rejoinder statistics at this time. Please try again later");
    }
    $app = new App($dbconnect);

    $getUserCountSQL = sprintf("SELECT COUNT(*) AS total_users FROM users;");
    $getUserCountQuery = mysqli_query($dbconnect, $getUserCountSQL);
    $usersTotal = mysqli_fetch_assoc($getUserCountQuery)['total_users'];

    $getMsgCountSQL = sprintf("SELECT COUNT(*) AS total_messages FROM messages;");
    $getMsgCountQuery = mysqli_query($dbconnect, $getMsgCountSQL);
    $msgTotal = mysqli_fetch_assoc($getMsgCountQuery)['total_messages'];

    echo $app->getPageHead("STATS | Rejoinder");
    include 'components/header.php';
?>
<div id="stats_div">
    <table id="stats_table">
        <tr>
            <th>Category</th>
            <th>Statistic</th>
        </tr>
        <tr>
            <td>Total Users</td>
            <td><?php echo $usersTotal; ?></td>
        </tr>
        <tr>
            <td>Total Questions</td>
            <td><?php echo $msgTotal; ?></td>
        </tr>
        <tr>
            <td>Example Statistic</td>
            <td>23</td>
        </tr>
    </table>
</div>
<?php
    include 'components/footer.html';
?>
<script>
    $("body").css("background", "#FFFFFF");
</script>
