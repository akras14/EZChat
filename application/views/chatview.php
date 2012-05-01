<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">

var chatid = "<?php echo $chatid; ?>";
var userid = "<?php echo $userid; ?>";

</script>

<script type="text/javascript">
$(document).ready( function(){

    //Put Message into database
    function putChatMessage(){
        //1. Get Chat Message from the page
        var chatmessage = $("input#chatline").val();

        //2. Make sure chat contains some text
        if (chatmessage == "") return false;

        //3. Post chat message into the database via JQuery AJAX call
        $.post("<?php echo site_url('chat/ajax_call_insertMessage'); ?>", 
        {userid: userid, chatid: chatid, chatmessage: chatmessage},
        function (data){
            //Clear Chat Field
            document.getElementById('chatline').value = '';
            getChatMessages(); 
        }, "json");

    }

    //When user clicks the say it link
    $("a#sendmessage").click(function(){
        //Post chat message to the database 
        putChatMessage();
        //Stops browser from refreshing
        return false;

    });

    //When user click enter inside the chat line
    $("#chatline").keyup(function(event){
        if(event.keyCode == 13){
            $("a#sendmessage").click();
        }
    });

    //Scroll to the bottom of chat window
    function scrollDown(){
        var chatWindow = document.getElementById('chatwindow');
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    //Get chat messages from database
    function getChatMessages(){
        $.post("<?php echo site_url('chat/ajax_call_getMessages'); ?>", {chatid: chatid},
            function (data){

                //Check to see if data was received okay
                if (data.status == 'ok')
                {
                    //Post the datat to the screen
                    //1. Get chatwindow element
                    var chatwindow = document.getElementById('chatwindow');
                    //2. Save Current Messages
                    //var currentMessages = chatwindow.html();
                    //3. Add new Messages
                    //chatwindow.html(currentMessages + data.content);
                    $(chatwindow).html(data.content);
                    scrollDown();
                }
            }, "json");
    }

    //1. Check for messages as soon as window loads
    getChatMessages();
    
    //2. Continue to check for new messages every second
    window.setInterval(function(){
        getChatMessages();
    }, 1000);

});
</script>

<style type="text/css">
#chatwindow {
    width: 500px;
    height: 500px;
    border-style:solid;
    padding: 10px;
    overflow: auto;
}

#chatinput {
    margin-top: 10px;
}
</style>
</head>
<body>
<h1>EZ Chat!</h1>
<div id="chatwindow">
</div>
<div id="chatinput">
    <input id="chatline" name="chatline" size="50" type="text" value="" />
    <a id="sendmessage" href="" >Say It</a>
</div>
</body>
</html>

