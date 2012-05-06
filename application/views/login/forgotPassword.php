</head>
<body>
    <div id="form">
    <h1>Password Recovery</h1>
    <!-- Displays validation errrors -->
    <?php echo validation_errors(); ?>

    <!-- Displays Custom Error Message -->
    <?php if (isset($message))  { echo $message;} ?>

    <!-- Prepare form header i.e. <form ...>-->
    <?php echo form_open('login/forgot_password'); ?>

        <b>Please Enter Your Email</b><br />
        <input type="text" name="email" value="" size="30" /><br />
        <div><input type="submit" value="Submit" />
        <input type="button" value="Cancel" onclick="window.location.href='<?php echo site_url("login")?>'"/>
        </div><br />
    </form>
    </div>
</body>
</html>
