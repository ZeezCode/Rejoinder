<?php
    if (!isset($_SESSION) || !isset($_SESSION['user'])) {
        die(0);
    }
?>
<span id="lobby_title" style="display: none;"><?php echo $_GET['id']; ?></span>
<div id="question_box">
    <div id="question_box_control">
        <?php
            $takingQuestions = !$app->userHasQuestionsDisabled($_SESSION['user']['name']);
        ?>
        <input type="checkbox" id="taking_question" style="-webkit-appearance: none; background-color: <?php echo ($takingQuestions ? "#8CFE72" : "#FF0000"); ?>;" <?php echo ($takingQuestions ? "checked" : ""); ?>>
        <select id="select_count">
            <option value="1">1 Question per Refresh</option>
            <option value="2">2 Questions per Refresh</option>
            <option value="3">3 Questions per Refresh</option>
            <option value="4">4 Questions per Refresh</option>
            <option value="5" selected>5 Questions per Refresh</option>
            <option value="6">6 Questions per Refresh</option>
            <option value="7">7 Questions per Refresh</option>
            <option value="8">8 Questions per Refresh</option>
            <option value="9">9 Questions per Refresh</option>
            <option value="10">10 Questions per Refresh</option>
        </select>
    </div>
    <div id="question_box_list">
    </div>
</div>

<script>
    function timestampToTime(timestamp) {
        var date = new Date(timestamp * 1000);
        var hour = date.getHours(), minute = date.getMinutes(), period = "AM";
        if (hour > 12) {
            hour = date.getHours() - 12;
            period = "PM";
        }
        if (minute < 10) {
            minute = "0" + date.getMinutes();
        }
        return hour + ":" + minute + " " + period;
    }

    function getQuestions(lastQuestion, lid, count, first) {
        $.ajax({
            type: "GET",
            url: "actions/get_message.php",
            data: {lastQuestion:lastQuestion, lid:lid, count:count, first:first},
            dataType: 'json',
            success: function(data) {
                data.forEach(function(obj) {
                    var newQuestion = "<div class=\"question\">"
                        + "<span class=\"sender_name\">" + obj['user'] + ":</span><span class=\"question_timestamp\">" + timestampToTime(obj['timestamp']) + "</span><br />"
                        + "<span class=\"question_text\">" + obj['message'] + "</span><span onclick=\"removeQuestion(this)\" class=\"remove_button\">X</span>";
                    $("#question_box_list").prepend($(newQuestion).animate({
                        backgroundColor: '#6441A5',
                        color: "#FFFFFF",
                    }, 1000));
                    if (+obj['mid'] > +lastQuestion) {
                        lastQuestion = obj['mid'];
                    }
                });
                startQuestionRequestTimer(lastQuestion);
            }
        });
    }

    function startQuestionRequestTimer(lastQuestion) {
        setTimeout(function() {
            console.log("requesting...");
            getQuestions(lastQuestion, $('#lobby_title').text(), 5, "false");
        }, 10 * (1000)); //10 seconds
    }

    function removeQuestion(button) {
        $(button).parent().fadeOut(500, function() {
            $(this).remove();
        });
    }

    $(':checkbox').change(function() {
        if (this.checked)
            $(this).css('background-color', '#8CFE72');
        else
            $(this).css('background-color', '#FF0000');
        $.notify("You are " + (this.checked ? "now" : "no longer") + " accepting questions!", "info");
    });

    $.ajax({
        type: "GET",
        url: "actions/get_starting_message.php",
        data: {id:$('#lobby_title').text()},
        dataType: 'text',
        success: function(data) {
            if (data!="-1") {
                startQuestionRequestTimer(data);
            } else {
                $("#question_box_list").prepend("<span style=\"color: red;\">An error occurred while attempting to fetch last question ID. Please try again later.</span>");
            }
        }
    });
</script>
