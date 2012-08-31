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
<?php if (!$complete) { ?>
	<input type="submit" name="next" value="Next" tabindex="1">
<?php } ?>
<?php if ($enable_skip) {?>
	<input type="submit" name="skip" value="Skip Step" tabindex="3" >
<?php } ?>

<?php if (!$start) { ?>
	<input type="submit" name="previous" value="Previous" tabindex="2">
<?php } ?>

<input type="submit" name="cancel" value="Cancel" tabindex="4">

</form>
</body>
</html>