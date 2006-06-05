<?php
$charset = "utf-8";
$mime = "text/xhtml";

if (!isset($pageLanguage)) {
    $languageClass =& $this->getObject('language', 'language');
    $languageCode =& $this->getObject('languagecode', 'language');
    $pageLanguage = $languageCode->getISO($languageClass->currentLanguage());
}

function fix_code($buffer)
{
    return (preg_replace("!\s*/>!", ">", $buffer));
}

if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml"))
{
    if(preg_match("/application\/xhtml\+xml;q=([01]|0\.\d{1,3}|1\.0)/i",$_SERVER["HTTP_ACCEPT"],$matches))
    {
       	$xhtml_q = $matches[1];
        if(preg_match("/text\/html;q=q=([01]|0\.\d{1,3}|1\.0)/i",$_SERVER["HTTP_ACCEPT"],$matches))
        {
            $html_q = $matches[1];
            if((float)$xhtml_q >= (float)$html_q)
            {
                $mime = "application/xhtml+xml";
			}
        }
    } else {
          $mime = "application/xhtml+xml";
      }
}

if($mime == "application/xhtml+xml")
{
    $prolog_type = "<?xml version=\"1.0\" encoding=\"$charset\" ?>\n<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"$pageLanguage\" lang=\"$pageLanguage\">\n";
} else {
	ob_start("fix_code");
        $prolog_type = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html lang=\"$pageLanguage\">\n";
}

header("Content-Type: $mime;charset=$charset");
header("Vary: Accept");
print $prolog_type;

?>
<head>
<title><?php echo $objConfig->getSiteName(); ?></title>
<?php

if (!isset($pageSuppressSkin)){
	if (isset($pageSimpleSkin)) {
	    echo $objSkin->putSimpleSkinCssLinks();
	}
	else {
    	//echo $objSkin->putSkinCssLinks();
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
       <script type="text/javascript" src="<?php echo $objConfig->getSiteRoot().$script?>"></script>
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
	<div id="container">
<?php } ?>
<?php if (!isset($pageSuppressBanner)) { ?>
   	<div id="top"><a onclick="location='<?php echo $objConfig->getSiteRoot(); ?>index.php'">
		<img src="<?php echo $objSkin->bannerImageBase(); ?>smallbanner.jpg" alt="banner" /></a>
	</div>
<?php }
// Add toolbar bar if not suppressed
    if (!isset($pageSuppressToolbar)) {
?>
	    <div id='toolbar'>
		<?
		    $menu=& $this->getObject('menu','toolbar');
			//echo $menu->show();
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
        $link->link=$objLanguage->languageText("word_logout");
        $str=$objLanguage->languageText("mod_context_loggedinas").' <strong>'.$this->objUser->fullname().'</strong>  ('.$link->show().')';
        $this->footerNav->str = $str;
    } else {
        $this->footerNav->str = '&nbsp';
    }


    //echo $this->footerNav->show();
}
?>

<?php if (!isset($pageSuppressContainer)) { ?>
	</div>
<?php } ?>
<?php
//$this->putMessages();

?>
</body>
</html>