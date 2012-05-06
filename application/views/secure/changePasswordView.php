</head>
<body>
<div id="main">
<div id="logo"></div>
<div id="content">
<div id="rightBar"></div>
<div id="leftBar"></div>
    <h1>EasyTV Personal Data</h1>
    <!-- Displays validation errrors -->
    <?php echo validation_errors(); ?>

    <!-- Displays Custom Error Message -->
    <?php if (isset($message))  { echo $message;} ?>
    <div id="form">
    <!-- Prepare form header i.e. <form ...>-->
    <?php echo form_open('chat/changeInfo'); ?>

      <b>New Password</b><br />
      <input type="password" name="password" value="" size="30" /><br/>
      <b>Confirm Password</b><br />
      <input type="password" name="passwordConfirm" value="" size="30" /><br />

         <div> <input type="submit" value="Update" />
         <input type="button" value="Cancel" onclick="window.location.href='<?php echo site_url("chat")?>'"/></div><br />
          <a href="<?php echo site_url('chat/logout'); ?>">Log Out</a>    
    <?php echo form_close();?>
    </div>
</div>
</div>
</body>
</html>
