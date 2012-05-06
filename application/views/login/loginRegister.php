</head>
<body>
<div id="main">
<div id="logo"></div>
<div id="content">
<div id="rightBar"></div>
<div id="leftBar"></div>
    <h1>Register New Account</h1>

    <?php echo validation_errors(); ?>
    <div id="form">
    <!-- Displays Custom Error Message -->
    <?php if (isset($message))  { echo $message;} ?>

    <?php echo form_open('login/register');?>

      <b>Nickname</b><br />
      <input type="text" name="nickname" value="" size="30" /><br />

      <b>Email</b><br />
      <input type="text" name="email" value="" size="30" /><br />

      <b>Password</b><br />
      <input type="password" name="password" value="" size="30" /><br />

      <b>Confirm Password:</b><br />
      <input type="password" name="passwordConfirm" value="" size="30" /><br />

      <input type="submit" value="Submit" />
      <input type="button" value="Cancel" onclick="window.location.href='<?php echo site_url("login")?>'"/>

    <?php echo form_close();?>
    </div>
</div>
</div>
</body>
</html>
