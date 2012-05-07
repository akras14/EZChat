<html>
<head>
</head>
<body>
<h1>Home View</h1>
<p>
<?php /*Print Out HTML from backend with a list of rooms*/ echo $container ?>

</p>
<a href="<?php echo site_url('secure/createNewRoom'); ?>">Create New Room</a>&nbsp;&nbsp;&nbsp;
<a href="<?php echo site_url('secure/logout'); ?>">Log Out</a>&nbsp;&nbsp;&nbsp;
<a href="<?php echo site_url('secure/changeInfo'); ?>">Change Personal Information</a>

</body>
</html>

