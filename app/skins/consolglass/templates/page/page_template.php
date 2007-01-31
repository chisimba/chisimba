<?php
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
if($ie == TRUE)
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


if (isSet($bodyParams)) {
    echo "<body " . $bodyParams . ">";
} else {
    echo '<body>';
}


 	if (!isset($pageSuppressContainer)) {
 	    echo '
<div class="dialog">
    <div class="hd"><div class="c"></div></div>
    <div class="bd">
        <div class="c">
            <div class="s">';
 	}

    if (!isset($pageSuppressBanner)) {
?>


    <div id="header">
    <a href="index.php"><img src="skins/consolglass/images/consol_glass_logo.gif" /></a>

        <?php
         if (!isset($pageSuppressToolbar)) {
            $menu=& $this->getObject('menu','toolbar');
            echo $menu->show();
         }
         ?>
    </div>

<?php  }


    // get content
    echo $this->getLayoutContent();



echo '<div style="clear:both;"></div>
            </div>
        </div>
    </div>
    <div class="ft"><div class="c"></div></div>
</div>';


 
 $this->putMessages();
?>
</body>
</html>
