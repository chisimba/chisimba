<?php
$useragent = $_SERVER['HTTP_USER_AGENT'];

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

//Force the mimetype to be text/html so that the scriptaculous library will work
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
<!-- BEGIN: INSERT THE SCRIPTACULOUS LIBRARY FOR BANNER ROLL UP -->
<script src="core_modules/htmlelements/resources/script.aculos.us/lib/prototype.js" type="text/javascript"></script>
<script src="core_modules/htmlelements/resources/script.aculos.us/src/scriptaculous.js" type="text/javascript"></script>
<script src="core_modules/htmlelements/resources/script.aculos.us/src/unittest.js" type="text/javascript"></script>
<!-- END:  INSERT THE SCRIPTACULOUS LIBRARY FOR BANNER ROLL UP -->
<?php

//Insert the CSS link if we are not suppressing skins
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

//Load an array of javascript references if the array is set
if (isset($jsLoad)) {
    foreach ($jsLoad as $script) {
		?>
        <script type="text/javascript" src="<?php echo $objConfig->getSiteRoot().$script?>"></script>
    	<?php 
	}
}

//Insert the array of header params if set as array or string
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

//Add any body OnLoad events to the body tag if set
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

//Add the body params to the body tag
if (isSet($bodyParams)) {
    echo "<body " . $bodyParams . ">";
} else {
    echo '<body>';
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

// ***************  SECTION FOR WORKING WITH THE BANNER ******************** //
/*
* 
* Added in Madiba wide skin, a method to scroll up the banner 
* using a parameter
* 
*/ 	
if (!isset($pageSuppressBanner)) {
	//Create the icons for the banner roll up / down
	$icon = $this->getObject('geticon', 'htmlelements');
   	$icon->setIcon('up');
	$scrollUpLink = "<a href=\"#\" onclick=\"Effect.SlideUp('header',{queue:{scope:'myscope', position:'end',limit: 1}});\">"
	  . $icon->show()."</a>";
   	$icon->setIcon('down');
   	$scrollUpLink .="<a href=\"#\" onclick=\"Effect.SlideDown('header',{queue:{scope:'myscope',position:'end', limit: 1}});\">"
   	  . $icon->show()."</a>";
    ?>
	<div id="header">
		<h1 id="sitename"><span><?php echo $objConfig->getsiteName();?></span></h1>
		<?php 
		if ($this->objUser->isLoggedIn()) { ?>
		    <div id="search">
			    <form action="">
   			        <label for="searchsite">Site Search:</label>
				    <input id="searchsite" name="query" type="text" />
				    <input type="submit" value="Go" class="f-submit" />
			    </form>
		    </div>
		    <?php
        }
}
if (!isset($pageSuppressToolbar)) {
    $menu=& $this->getObject('menu','toolbar');
	echo $menu->show();
}
?>
</div>
<?php
// *************** END OF BANNER SECTION *****************************//


// get content
echo $this->getLayoutContent();

//Add the scroll up/down links
if (isset($scrollUpLink)) {
	?>
	<div id="scrollme" style="float: left;"><?php echo $scrollUpLink; ?></div>
	<?php
}
?>
<?php
// ****************** SECTION FOR WORKING WITH THE FOOTER ************//
if (!isset($suppressFooter)) {
     // Create the bottom template area
    $this->footerNav = & $this->newObject('layer', 'htmlelements');
    $this->footerNav->id = 'footer';
    $this->footerNav->cssClass='';
    $this->footerNav->position='';
    if (isset($footerStr)) {
        $this->footerNav->str = $footerStr;
    } else if ($this->objUser->isLoggedIn()) {

        $this->loadClass('link', 'htmlelements');
        $link = new link ($this->URI(array('action'=>'logoff'),'security'));
        $link->link=$objLanguage->languageText("word_logout");
        $str=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$this->objUser->fullname().'</strong>  ('.$link->show().')';
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