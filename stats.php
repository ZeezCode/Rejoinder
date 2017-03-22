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

    $getUserCountInLastDaySQL = sprintf("SELECT COUNT(*) AS users_online FROM users WHERE UNIX_TIMESTAMP() - last_seen <= 86400;");
    $getUserCountInLastDayQuery = mysqli_query($dbconnect, $getUserCountInLastDaySQL);
    $userCount = mysqli_fetch_assoc($getUserCountInLastDayQuery)['users_online'];

    $getTopPosterUIDSQL = sprintf("SELECT COUNT(*) AS num_of_questions, uid FROM messages GROUP BY uid ORDER BY num_of_questions DESC;");
    $getTopPosterUIDQuery = mysqli_query($dbconnect, $getTopPosterUIDSQL);
    $result = mysqli_fetch_assoc($getTopPosterUIDQuery);
    $mostQuestionsCount = $result['num_of_questions'];
    $mostQuestionsName = $app->getUserFromUID($result['uid'])['display_name'];
    if (strlen($mostQuestionsName) > 8) {
        $mostQuestionsName = substr($mostQuestionsName, 0, 5) . "...";
    }

    $getTopLobbySQL = sprintf("SELECT COUNT(*) AS num_of_questions_asked, lid FROM messages GROUP BY lid ORDER BY num_of_questions_asked DESC");
    $getTopLobbyQuery = mysqli_query($dbconnect, $getTopLobbySQL);
    $topLobbyInfo = mysqli_fetch_assoc($getTopLobbyQuery);
    $topLobby = $app->getUserInfoFromName($topLobbyInfo['lid'])['display_name'];
    $topLobbyQuestions = $topLobbyInfo['num_of_questions_asked'];
    if (strlen($topLobby) > 8) {
        $topLobby = substr($topLobby, 0, 5) . "...";
    }

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
            <td>Users Online in Past Day</td>
            <td><?php echo $userCount; ?></td>
        </tr>
        <tr>
            <td>User Submitted Most Questions</td>
            <td><?php echo htmlentities($mostQuestionsName) . " (" . $mostQuestionsCount . ")"; ?></td>
        </tr>
        <tr>
            <td>Lobby with Most Questions</td>
            <td><?php echo htmlentities($topLobby) . " (" . $topLobbyQuestions . ")"; ?></td>
        </tr>
    </table>
</div>
<?php
    include 'components/footer.html';
?>
<script>
    $("body").css("background", "#FFFFFF");
</script>
