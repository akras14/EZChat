</head>
<body>
    <h1>Add New Room</h1>
    <!-- Displays validation errrors -->
    <?php echo validation_errors(); ?>

    <!-- Displays Custom Error Message -->
    <?php if (isset($message))  { echo $message;} ?>
    <div id="form">
    <!-- Prepare form header i.e. <form ...>-->
    <?php echo form_open('secure/createNewRoom'); ?>

      <b>New Room Name</b><br />
      <input type="text" name="roomname" value="" size="30" /><br/><br/>

         <div> <input type="submit" value="Create" />
         <input type="button" value="Cancel" onclick="window.location.href='<?php echo site_url("secure/index")?>'"/></div><br />
          <a href="<?php echo site_url('secure/logout'); ?>">Log Out</a>    
    <?php echo form_close();?>
    </div>
</body>
</html>
