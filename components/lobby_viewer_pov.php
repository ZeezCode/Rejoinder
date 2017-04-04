<?php
    if (!isset($_SESSION) || !isset($_SESSION['user'])) {
        die(0);
    }
?>
<div class="secondary_header_wrapper">
    <span class="secondary_header_text">Current Lobby: <?php echo $_GET['id']; ?></span>
</div>
<form action="javascript:;" onsubmit="sendMessage(this)">
    <input type="hidden" name="lobby" value="<?php echo $_GET['id']; ?>">
    <input type="hidden" name="token" value="<?php echo $_SESSION['user']['login_token']; ?>">
    <table id="send_form">
        <tr>
            <td>Sending as <?php echo $_SESSION['user']['display_name']; ?></td>
        </tr>
        <tr>
            <td><input type="text" name="typed_msg" id="typed_msg" placeholder="ENTER message here..." maxlength="128"></td>
        </tr>
        <tr>
            <td><input type="submit" value="Submit"></td>
        </tr>
    </table>
</form>

<script>
    function sendMessage(form) {
        console.log("Sending...");
        $.ajax({
            type: "GET",
            url: "actions/send_message.php",
            data: {lobby:form.lobby.value, token:form.token.value, message:form.typed_msg.value},
            dataType: 'text',
            success: function(data) {
                console.log(data);
                $.notify(data, (data.toLowerCase().includes("failed") || data.toLowerCase().includes("disabled") ? "error" : "info"));
            }
        });
        form.typed_msg.value = "";
    }
</script>
