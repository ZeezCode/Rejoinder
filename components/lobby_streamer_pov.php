<?php
    if (!isset($_SESSION) || !isset($_SESSION['user'])) {
        die(0);
    }
?>
<span id="lobby_title" style="display: none;"><?php echo $_GET['id']; ?></span>
<div id="question_box">
    <div id="question_box_control">

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
