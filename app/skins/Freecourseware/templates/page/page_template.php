<?php



//----- BEGIN section to cater for MS Internet Explorer mime problems
$useragent = $_SERVER['HTTP_USER_AGENT'];
function isMSIE($useragent)
{
		if(eregi("msie", $useragent) && !eregi("opera",$useragent))
		{
			return TRUE;
		}
		if(eregi("microsoft internet explorer", $useragent))
		{
			return TRUE;
		}
}
$ie = isMSIE($useragent);
if($ie == TRUE) {
	$charset = "utf-8";
	$mime = "text/html";
} else {
	$charset = "utf-8";
	$mime = "application/xhtml+xml";
}
//----- END section to cater for MS Internet Explorer mime problems



if (!isset($pageLanguage)) {
    $languageClass =& $this->getObject('language', 'language');
    $languageCode =& $this->getObject('languagecode', 'language');
    $pageLanguage = $languageCode->getISO($languageClass->currentLanguage());
}

function fix_code($buffer)
{
    return (preg_replace("!\s*/>!", ">", $buffer));
}

if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    if(preg_match("/application\/xhtml\+xml;q=([01]|0\.\d{1,3}|1\.0)/i",$_SERVER["HTTP_ACCEPT"],$matches)) {
       	$xhtml_q = $matches[1];
        if(preg_match("/text\/html;q=q=([01]|0\.\d{1,3}|1\.0)/i",$_SERVER["HTTP_ACCEPT"],$matches)) {
            $html_q = $matches[1];
            if((float)$xhtml_q >= (float)$html_q) {
                $mime = "application/xhtml+xml";
			}
        }
    } else {
          $mime = "application/xhtml+xml";
    }
}

if (isset($pageSuppressXML)) {
	$mime = "text/html";
}

if($mime == "application/xhtml+xml") {
	$prolog_type = "<?xml version=\"1.0\" encoding=\"$charset\" ?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"$pageLanguage\" lang=\"$pageLanguage\">\n";
} else {
	ob_start("fix_code");
        $prolog_type = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html lang=\"$pageLanguage\">\n";
}
header("Content-Type: $mime;charset=$charset");
header("Vary: Accept");
print $prolog_type;


if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}
?><head>
<title></title>
<?php

if (!isset($pageSuppressSkin)){
    echo $objSkin->putSkinCssLinks();
    if (!isset($pageSuppressToolbar)) {
        echo '
        <!--[if lte IE 6]>
        <style type="text/css">
            body { behavior:url("skins/_common/js/ADxMenu_prof.htc"); }
        </style>
        <![endif]-->
        ';
    }
}

if (isset($jsLoad)) {
    foreach ($jsLoad as $script) {
	?>
    	<script type="text/javascript" src="<?php echo $objConfig->getSiteRoot().$script?>"></script>
    <?php }
}
?>
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

if (isset($bodyOnLoad)) {
    echo '<script type="text/javascript" language="javascript">
    window.onload = function () {'."\n\n";

    foreach ($bodyOnLoad as $bodyParam)
    {
        echo $bodyParam."\n\n";
    }

    echo '}</script>';
}

?>
</head>

<?php
if (isSet($bodyParams)) {
    echo "<body " . $bodyParams . ">";
} else {
    echo "<body>\n";
}

	// Add instant messaging
	if (!isset($pageSuppressIM)) {
	    $objModules=&$this->getObject('modules','modulecatalogue');
	    $this->objUser =& $this->getObject('user', 'security');
	    if ((!isset($_SESSION['disable_im']))&&($objModules->checkIfRegistered('instantmessaging')) && ($this->objUser->isLoggedIn())) { ?>
			
<iframe id="IM" width="0" height="0" src="<?php echo $this->uri(array('action'=>'view'), 'instantmessaging'); ?>"></iframe>
   		<?php }
 	}

 	if (!isset($pageSuppressContainer)) {
 	    echo '<div id="container">';
 	}

 	if (!isset($pageSuppressBanner)) {

// this is place to add link
?>
	
	<div id="header">
		<h1 id="sitename"><span></span></h1> 
		<table border="0" width="100%">
	<tr><td>
	

<ul class="freecoursewarenav">
<a href="index.php?module=cms" class="freebutton">Home</a>
<a href="index.php?module=blog&amp;action=siteblog" class="freebutton">Blog</a>
<a href="index.php?module=wiki" class="freebutton">Wiki</a>
</ul>
</td>
	<?php if ($objUser->isLoggedIn()) {?>
<td align="right" ><b>Logged in as:</b>

	<?php	
		$this->objUser =& $this->getObject('user', 'security');
		echo $this->objUser->fullName();
	?>

		<input type="submit" value="Logout" class="f-submit" onclick="javascript:window.location='index.php?module=security&amp;action=logoff'" />
</td>

		<?php } ?>
		<?php if (!$objUser->isLoggedIn()) {?><td align="right" >	
		
		<?php
			$formAction = $this->objEngine->uri(array('action' => 'login'), 'security');
		?>		
<form class="login" name="login_form" id="form1" method="post" action='<?php echo $formAction ?>'><p><input name="username" value="Username" type="text" id="username" class="text prelogin" onclick="clearfocus();" />&nbsp;<input name="password" type="password" id="password" class="text prelogin" />&nbsp;<input type="checkbox" name="useLdap" value="yes" class="transparentbgnb" title="Network Id" />&nbsp;<input name="Submit" type="submit" class="button" onclick="KEWL_validateForm('username','','R','password','','R');return document.KEWL_returnValue" value="Login"/></p></form>
</td><?php } ?></tr></table>
		

		
				<div id="search">
					<form action="">
					<label for="searchsite">Site search:</label>
					<input id="searchsite" name="query" type="text" />
					<input type="submit" value="Go" class="f-submit" />
					</form>
				</div>
				
				
		
	
			
		
				
				<?php
				 if (!isset($pageSuppressToolbar)) {
				 	$menu=& $this->getObject('menu','toolbar');
				//echo $menu->show();
				 }
				 ?><div style="clear:both;"></div>
			</div>
<?php
}
    // get content
    echo $this->getLayoutContent()
?>

<?php
if (!isset($suppressFooter)) {
     // Create the bottom template area
    $this->footerNav = & $this->newObject('layer', 'htmlelements');
    $this->footerNav->id = 'footer';
    $this->footerNav->cssClass='';
    $this->footerNav->position='';
    $url = htmlentities("index.php?module=cms&action=showsection&id=init_4222_1168883716");
    //$copyRight = "Copyright &copy; 2007 Freecourseware";
    //$privacyPolicy = "<a href=\"$url\">Privacy policy</a>";
    
    //$this->footerNav->str = $copyRight . "<br />" . $privacyPolicy;
    echo $this->footerNav->show();
}
?>

<?php if (!isset($pageSuppressContainer)) { ?>
	 </div>
<?php } ?>
<?php
 $this->putMessages();
?>

</body>
</html>

