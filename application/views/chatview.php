<html>
<head>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css">

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
<script type="text/javascript">

var chatid = "<?php echo $chatid; ?>";
var username; 

</script>

<script type="text/javascript">
$(document).ready( function(){
    //Select Username Field

    function getUsername(){
        username = document.getElementById('username').value;
        $dialog.dialog('close');
        $("#chatline").select();
    }

    //Prompt user to enter a username
    var $dialog = $('<div></div>')
        .html('<input id="username" type="text" value="Guest" /><br />')
        .dialog({
            autoOpen: false,
                title: 'Select Nickname',
                resizable: false,
                open: function() {
                    $("input#username").select();
                    $("#username").keydown(function(event){
                        if(event.keyCode == 13){
                            getUsername();
                        }
                    })
                },
                modal: true,
                buttons: {
                    Ok: function() { getUsername();} 
                }
        });

    $dialog.dialog('open');


    //Global Variable used to store chat message
    var chatmessage = "";

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
                    var currentMessages = $(chatwindow).html();
                    //3. Add new Messages
                    $(chatwindow).html(currentMessages +  data.content);
                    scrollDown();
                }
            }, "json");
    }


    //Put Message into database
    function putChatMessage(){

        //1. Make sure chat contains some text
        if (chatmessage == "") return false;

        //2. Post chat message into the database via JQuery AJAX call
        $.post("<?php echo site_url('chat/ajax_call_insertMessage'); ?>", 
        {username: username, chatid: chatid, chatmessage: chatmessage},
        function (data){
            //Update Messages
            getChatMessages();
        }, "json");
    }

    //When user clicks the say it link
    $("input#sendmessage").click(function(){
        //1. Get Chat Message from the page
        chatmessage = $("input#chatline").val();
        //Clear Input chatline
        document.getElementById('chatline').value = '';    
        //Post chat message to the database 
        putChatMessage();
        //Stops browser from refreshing
        return false;

    });

    //When user click enter inside the chat line
    $("#chatline").keydown(function(event){
        //1. Get Chat Message from the page
        chatmessage = $("input#chatline").val();
        if(event.keyCode == 13){
            //Clear Input chatline
            document.getElementById('chatline').value = '';    
            //Post chat message to the database 
            putChatMessage();
            //Stops browser from refreshing
            return false;
        }
    });

    //Scroll to the bottom of chat window
    function scrollDown(){
        var chatWindow = document.getElementById('chatwindow');
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

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

/*Hides close button on popup dialog*/
.ui-dialog-titlebar-close{
    display: none;
}
</style>
</head>
<body>
<h1>EZ Chat!</h1>
<div id="chatwindow">
</div>
<div id="chatinput">
    <input id="chatline" name="chatline" size="50" type="text" value="" />
    <input type="button" value="Send" id="sendmessage" /> 
</div>
</body>
</html>

