</head>
<body >
    <h1>Change Personal Info</h1>
    <!-- Displays validation errrors -->
    <?php echo validation_errors(); ?>

    <!-- Displays Custom Error Message -->
    <?php if (isset($message))  { echo $message;} ?>
    <div id="form">
    <!-- Prepare form header i.e. <form ...>-->
    <?php echo form_open('secure/changeInfo'); ?>

      <b>New Nickname</b><br />
      <input type="text" name="nickname" value="" size="30" /><br/><br/>
      <b>New Password</b><br />
      <input type="password" name="password" value="" size="30" /><br/>
      <b>Confirm New Password</b><br />
      <input type="password" name="passwordConfirm" value="" size="30" /><br /><br />
      <b>Current Password</b> (Required)<br />
      <input type="password" name="oldpassword" value="" size="30" /><br/>

         <div> <input type="submit" value="Update" />
         <input type="button" value="Cancel" onclick="window.location.href='<?php echo site_url("chat")?>'"/></div><br />
          <a href="<?php echo site_url('secure/logout'); ?>">Log Out</a>    
    <?php echo form_close();?>
    </div>
</body>
</html>
