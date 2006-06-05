<?php header("Content-type: text/html; charset=utf-8"); ?>
<?

if (!isset($pageLanguage)) {
    $languageClass =& $this->getObject('language', 'language');
    $languageCode =& $this->getObject('languagecode', 'language');
    $pageLanguage = $languageCode->getISO($languageClass->currentLanguage());
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang='<?php echo $pageLanguage; ?>'>
<META HTTP-EQUIV='CONTENT-TYPE' CONTENT='text/html; charset=UTF-8'>
<head>
<title><?php echo $objConfig->getsiteName(); ?></title>
<?php

if (!isset($pageSuppressSkin)){
	if (isset($pageSimpleSkin)) {
	    echo $objSkin->putSimpleSkinCssLinks();
	}
	else {
    	echo '<link rel="stylesheet" type="text/css" href="skins/echo/main.css" media="screen" />
				<link rel="stylesheet" type="text/css" href="skins/echo/print.css" media="print" />
				<!--[if lte IE 6]>
					<link rel="stylesheet" type="text/css" href="skins/echo/ie6_or_less.css" />
				<![endif]-->
				<script type="text/javascript" src="skins/echo/js/common.js"></script>';
	}
}

if (isset($jsLoad)) {
    foreach ($jsLoad as $script) {
?>
       <script type="text/javascript" src="<?php echo $objConfig->getsiteRoot().$script?>"></script>
    <?php }
} ?>
<?php

if (isset($headerParams)) {

    if (is_array($headerParams)) {
        foreach ($headerParams as $headerParam)
        {
            echo $headerParam."\n\n";
        }
    } else {
        echo $headerParams;
    }

}


?>
</head>
<?php

if (isSet($bodyParams)) {
    echo "<body " . $bodyParams . ">";
} else {
    echo "<body id=\"type-f\">";
}
?>
<?php
	// Add instant messaging
	if (!isset($pageSuppressIM)) {
	    $objModules=&$this->getObject('modules','modulelist');
	    $this->objUser =& $this->getObject('user', 'security');
	    if ((!isset($_SESSION['disable_im']))&&($objModules->checkIfRegistered('instantmessaging')) && ($this->objUser->isLoggedIn())) { ?>
			<iframe id="IM" width="0" height="0" src="<?php echo $this->uri(array('action'=>'view'), 'instantmessaging'); ?>"></iframe>
   		<?php }
 	}
?>

<?php if (!isset($pageSuppressContainer)) { ?>
	<!--div id="container"-->
<?php } ?>
<?php if (!isset($pageSuppressBanner)) { ?>
   	
<?php }
// Add toolbar bar if not suppressed
    if (!isset($pageSuppressToolbar)) {
?>

		<div id="wrap">

			<div id="header">
				<div id="site-name"><?php echo $objConfig->getsiteName();?></div>
				<div id="search">
					<form action="">
					<label for="searchsite">Site Search:</label>
					<input id="searchsite" name="searchsite" type="text" />
					<input type="submit" value="Go" class="f-submit" />
					</form>
				</div>
		
				<?php
				 if (!isset($pageSuppressToolbar)) {
				 	$menu=& $this->getObject('menu','toolbar');
					echo $menu->show();
				 }
				 ?>
			</div>	 
				
		
			
<?  }

    // get content
    echo $this->getLayoutContent();
?>

<?php
if (!isset($suppressFooter)) {
     // Create the bottom template area
    $this->footerNav = & $this->getObject('layer', 'htmlelements');
    $this->footerNav->id = 'footer';
    $this->footerNav->cssClass='';
    $this->footerNav->position='';
    if (isset($footerStr)) {
        $this->footerNav->str = $footerStr;
    } else if ($this->objUser->isLoggedIn()) {

        $this->loadClass('link', 'htmlelements');
        $link = new link ($this->URI(array('action'=>'logoff'),'security'));
        $link->link=$objLanguage->languageText("word_logout",'security');
        $str=$objLanguage->languageText("mod_context_loggedinas",'context').' <strong>'.$this->objUser->fullname().'</strong>  ('.$link->show().')';
        $this->footerNav->str = $str;
    } else {
        $this->footerNav->str = '&nbsp';
    }


   // echo $this->footerNav->show();
}
?>

<?php if (!isset($pageSuppressContainer)) { ?>
	<!--/div-->
<?php } ?>
<?php
//$this->putMessages();

?>
</div>
</body>
</html>