<?php
    if (!isset($_SESSION) || !isset($_SESSION['user'])) {
        die(0);
    }
?>
<div id="question_box">
    <div id="question_box_control">

    </div>
    <div id="question_box_list">
        <div class="question">
            <span class="sender_name">ZeePro90:</span><span class="question_timestamp">3/04/2017</span><br />
            <span class="question_text">Who am I</span>
        </div>

        <div class="question">
            <span class="sender_name">ZeePro90:</span><span class="question_timestamp">3/04/2017</span><br />
            <span class="question_text">Who are you</span>
        </div>
    </div>
</div>
