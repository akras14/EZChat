</head>
<body>
    <h1>EZ Chat</h1>
    <!-- Displays validation errrors -->
    <?php echo validation_errors(); ?>

    <!-- Displays Custom Error Message -->
    <?php if (isset($message))  { echo $message;} ?>
    <div id="form">
    <!-- Prepare form header i.e. <form ...>-->
    <?php echo form_open('login'); ?>

        <b>Email</b><br />
        <input type="text" name="email" value="" size="30" /><br />
        <b>Password</b><br />
        <input type="password" name="password" value="" size="30" /><br />
        <div><input type="submit" value="Submit" /></div><br />
        <a href="<?php echo site_url('login/register'); ?>">Register</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo site_url('login/forgot_password'); ?>">Forgot Password</a>
    </form>
    </div>
</body>
</html>
