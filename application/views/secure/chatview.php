<html>
<head>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">

//Get Room ID
var chatid = "<?php echo $chatid; ?>";
//Get User Nickname
var username = "<?php echo $nickname; ?>";

</script>

<script type="text/javascript">
$(document).ready( function(){
    //Global variables
    //Reference Input Box for chat message
    var chatline = document.getElementById('chatline');

    //Get chat messages from database
    function getChatMessages(){
        var jqxhr = $.post("<?php echo site_url('chat/ajax_call_getMessages'); ?>", {chatid: chatid},
            function (data){
                //Check to see if data was received okay
                if (data.status == 'ok')
                {
                    //Add new Messages
                    $(chatwindow).html(data.content);
                    scrollDown();
                }

            }, "json")
            //.error is for method $.post().error when we get error it refreshes page
            //This is probably caused by a user in different tab loging out    
            .error(function () { console.log("errror"); location.reload();});
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
        //Focus back to Input area
        chatline.focus();
        //Stops browser from refreshing
        return false;

    });

    //When user click enter inside the chat line
    $("#chatline").keydown(function(event){
        //1. Get Chat Message from the page
        chatmessage = $("input#chatline").val();
        if(event.keyCode == 13){
            //Clear Input chatline
            chatline.value = '';    
            //Post chat message to the database 
            putChatMessage();
            //Focus back to input area
            chatline.focus();
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

    //Points cursor to the input field
    chatline.focus();
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
<a href="<?php echo site_url('chat/logout'); ?>">Log Out</a>&nbsp;&nbsp;&nbsp;
<a href="<?php echo site_url('chat/changeInfo'); ?>">Change Personal Information</a>
<h1>EZ Chat!</h1>
<div id="chatwindow">
</div>
<div id="chatinput">
    <input id="chatline" name="chatline" size="50" type="text" value="" />
    <input type="button" value="Send" id="sendmessage" /> 
</div>
</body>
</html>

