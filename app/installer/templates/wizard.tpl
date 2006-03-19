<html>
<head>
	<title><?php echo $title ?></title>
	
</head>
<body style="background-color: WhiteSmoke; font-family: Tahoma; font-size: 12px;">
<form name="wizardform" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
<input type="hidden" name="sys_root" value="<?php echo $sys_root ?>" />
<p>
<?php echo $step_details ?>
</p>

<div style="font-family: Tahoma;color: red; font-size: 10px;">
<?php echo $error ?>
</div>

<input type="submit" name="cancel" value="Cancel">

<?php if ($enable_skip) {?>
	<input type="submit" name="skip" value="Skip Step">
<?php } ?>

<?php if (!$start) { ?>
	<input type="submit" name="previous" value="Previous">
<?php } ?>

<?php if (!$complete) { ?>
	<input type="submit" name="next" value="Next" accesskey="s">
<?php } ?>

</form>
</body>
</html>