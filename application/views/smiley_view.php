<html>
<head>
<title>Smileys</title>

<?php echo smiley_js(); ?>

</head>
<body>

<form name="blog">
<textarea name="comments" id="comments" cols="40" rows="4"></textarea>
</form>

<p>Click to insert a smiley!</p>

<?php echo $smiley_table; ?>

</body>
</html>

