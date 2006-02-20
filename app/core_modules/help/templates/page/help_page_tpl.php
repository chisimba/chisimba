<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $objConfig->siteName(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="<?php echo $objSkin->getSkinUrl(); ?>kewl_css.php">
<head>
<?php
$objSkin->skinStartPage(); 
?>
<body class="help-popup" onLoad="window.focus();">
<?php echo $this->getLayoutContent(); ?>
</body>
</html>