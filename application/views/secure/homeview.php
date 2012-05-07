<style type="text/css">
#roomList {
width: 300px;
max-height: 300px;
overflow: auto;
}
</style>
<html>
<head>
</head>
<body>
<a href="<?php echo site_url('secure/logout'); ?>">Log Out</a>&nbsp;&nbsp;&nbsp;
<a href="<?php echo site_url('secure/changeInfo'); ?>">Change Personal Information</a>
<h1>Home View</h1>
<p>
<?php /*Print Out HTML from backend with a list of rooms*/ echo $container ?>

</p>

<input type="button" value="Add New Room" onclick ="window.location.href='<?php echo site_url('secure/createNewRoom'); ?>'"></input>

</body>
</html>

