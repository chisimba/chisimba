<?php

$objUser = $this->getObject('user', 'security');

$browser = $this->getObject('browser', 'skin');
if($browser->isMSIE() || $browser->isSafari())
{
	$charset = "utf-8";
	$mime = "text/html";
}
else {
	$charset = "utf-8";
	$mime = "application/xhtml+xml";
}

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

if (isset($pageSuppressXML)) {
	$mime = "text/html";
}

// Turned Off for Demonstration
$mime = "text/html";

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


if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}
?>
<head>
<title><?php echo $pageTitle; ?></title>
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

if ($objUser->isLoggedIn()) {
    $module = 'tabeisa_postlogin';
} else {
    $module = 'prelogin';
}


if (isSet($bodyParams)) {
    echo "<body " . $bodyParams . ">";
}


 	if (!isset($pageSuppressContainer)) {
 	    echo '<div id="container">';
 	}

 	if (!isset($pageSuppressBanner)) {

?>


			<div id="header" align="center">
            <table width="735"  border="0" cellspacing="0" cellpadding="0" id="headertable">
  <tr align="center" valign="top">
    <td bgcolor="#000000"><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" /><a href="index.php?module=<?php echo $module; ?>"><img src="skins/tabeisa/images/transparent.gif" name="homeimage" width="60" height="20" border="0" id="homeimage" /></a><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" /><a href="index.php?module=splashscreen"><img src="skins/tabeisa/images/transparent.gif" name="flashhomeimage" width="90" height="20" border="0" id="flashhomeimage" /></a><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" /><a href="index.php?module=tabeisa_about"><img src="skins/tabeisa/images/transparent.gif" name="aboutimage" width="67" height="20" border="0" id="aboutimage" /></a><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" /><a href="index.php?module=tabeisa_services"><img src="skins/tabeisa/images/transparent.gif" name="servicesimage" width="68" height="20" border="0" id="servicesimage" /></a><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" /><a href="index.php?module=tabeisa_contact"><img src="skins/tabeisa/images/transparent.gif" name="contactimage" width="58" height="20" border="0" id="contactimage" /></a><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" /><?php 
    
    if ($objUser->isAdmin()) {
        echo '<a href="index.php?module=toolbar"><img src="skins/tabeisa/images/transparent.gif" name="adminimage" width="67" height="20" border="0" id="adminimage" /></a><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" />';
    }
    
    if ($objUser->isLoggedIn()) {
        echo '<a href="javascript: if(confirm(\'Are you sure you want to logout?\')) {document.location= \'index.php?module=security&amp;action=logoff\'};"><img src="skins/tabeisa/images/transparent.gif" name="logoutimage" width="67" height="20" border="0" id="logoutimage" /></a><img src="skins/tabeisa/images/topspacer.gif" width="1" height="20" />';
    }
    
    ?></td>
  </tr>
  <tr>
  <td class="bannertd">
  <?php $tabeisaBanner = $this->getObject('tabeisabanner', 'tabeisa_about');
    echo $tabeisaBanner->getBannerImg($this->getParam('module', '_default'));?></td>
  </tr>
</table>
			</div>
<?php  } else {

?>
<style type="text/css">
body, html { background-color: #fff;}
</style>
<?

}

    // get content
    echo $this->getLayoutContent();
?>

<?php
if (!isset($suppressFooter)) {
     // Create the bottom template area
    $this->footerNav = & $this->newObject('layer', 'htmlelements');
    $this->footerNav->id = 'footer';
    $this->footerNav->cssClass='';
    $this->footerNav->position='';
    if (isset($footerStr)) {
        $this->footerNav->str = $footerStr;
    } else if ($objUser->isLoggedIn()) {

        $this->loadClass('link', 'htmlelements');
        $link = new link ($this->URI(array('action'=>'logoff'),'security'));
        $link->link=$objLanguage->languageText("word_logout");
        $str=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
        $this->footerNav->str = $str;
    }


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
