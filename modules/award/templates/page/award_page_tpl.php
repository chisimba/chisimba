<?php

// get any header params or body onload parameters for objects on the toolbar
if (!isset($headerParams)) {
    $headerParams = array();
}
if (!isset($bodyOnLoad)) {
    $bodyOnLoad = array();
}
   
// Get the site breadcrumbs
$tools = $this->getObject('tools','toolbar');
$siteBreadcrumbs = $tools->siteNavigation();
if (!isset($pageSuppressToolbar)) {
   // get toolbar object
   $menu = $this->getObject('menu','toolbar');
   $toolbar = $menu->show();

   
   $menu->getParams($headerParams, $bodyOnLoad);
}


$useragent = $_SERVER['HTTP_USER_AGENT'];

$browser = $this->getObject('browser', 'skin');
if ($browser->isMSIE() || $browser->isSafari()) {
	$charset = "utf-8";
	$mime = "text/html";
} else {
	$charset = "utf-8";
	$mime = "application/xhtml+xml";
}

if (!isset($pageLanguage)) {
    $languageClass =& $this->getObject('language', 'language');
    $languageCode =& $this->getObject('languagecode', 'language');
    $pageLanguage = $languageCode->getISO($languageClass->currentLanguage());
}

function fix_code($buffer) {
    return (preg_replace("!\s*/>!", ">", $buffer));
}

if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")) {
    if (preg_match("/application\/xhtml\+xml;q=([01]|0\.\d{1,3}|1\.0)/i",$_SERVER["HTTP_ACCEPT"],$matches)) {
       	$xhtml_q = $matches[1];
        if (preg_match("/text\/html;q=q=([01]|0\.\d{1,3}|1\.0)/i",$_SERVER["HTTP_ACCEPT"],$matches)) {
            $html_q = $matches[1];
            if ((float)$xhtml_q >= (float)$html_q) {
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

if ($mime == "application/xhtml+xml") {
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
$pageTitle .= ($siteBreadcrumbs==''?'':' [ '.strip_tags($siteBreadcrumbs).' ] ');
?>
    <head>
    
        <title>
<?php
    echo $pageTitle;
    
?>
        </title>
<?php
    if (!isset($pageSuppressSkin)) {
        echo $objSkin->putSkinCssLinks();
        if (!isset($pageSuppressToolbar)) {
            echo '<!--[if lte IE 6]>
                <style type="text/css">
                    body { behavior:url("skins/_common/js/ADxMenu_prof.htc"); }
                </style>
            <![endif]-->
';
        }
    }

    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);
    echo "<link rel=\"shortcut icon\" href=\"".$objSkin->getSkinUrl()."images/award.ico\" />";

?>
    </head>
<?php
    if (isSet($bodyParams)) {
        echo '<body ' . $bodyParams . '>';
        echo ' <!--This site is powered by Chisimba-->';
    } else {
        echo '<body>';
        echo ' <!--This site is powered by Chisimba-->';
    }
    // echo Chisimba
    
    if (!isset($pageSuppressContainer)) {
        echo '<div id="container">';
    }
    if (!isset($pageSuppressBanner)) {
?>
        <div id="header">
            <h1 id="sitename">
                <span>
<?php
        echo '<a href="'.$objConfig->getSiteRoot().'">'.$objConfig->getsiteName().'</a>';
?>
                </span>
            </h1>
<?php
        if (!isset($pageSuppressSearch)) {

        	echo $objSkin->siteSearchBox();

        }
        if (!isset($pageSuppressToolbar)) {
            //$menu= $this->getObject('menu','toolbar');
		  echo $toolbar; //$menu->show();
		  
            /****************** LOGIN TAB *************************/
                if (!$this->objUser->isLoggedIn()) {
                    $this->loadClass('textinput','htmlelements');
                    $this->loadClass('button','htmlelements');
                    $objForm =  $this->newObject('form', 'htmlelements');
                    $objForm->setAction($this->objEngine->uri(array('action' => 'login'), 'security'));
                    $userNameLabel = $this->objLanguage->languageText("word_username").': ';
                    $userNameBox = new textinput('username',null,'text',10);
                    $userNameBox->setCss('text prelogin');
                    $passwordLabel = $this->objLanguage->languageText("word_password").': ';
                    $passwordBox = new textinput('password',null,'password',10);
                    $passwordBox->setCss('text prelogin');
                    $jsWarning = '<noscript><span class="error"><strong>'.$this->objLanguage->languageText('mod_security_javascriptwarning').'</strong></span><br /></noscript>';
                    $loginButton = new button('Submit',$this->objLanguage->languageText("word_login"));
                    $loginButton->setToSubmit();
                    $loginContent = $userNameLabel.$userNameBox->show().' '.$passwordLabel.$passwordBox->show().' '.$jsWarning.$loginButton->show();
                    $objForm->addToForm($loginContent);
                    $objForm->addRule('username',$this->objLanguage->languageText("mod_award_unrequired","award"),'required');
                    $objLoginTable = &$this->newObject('htmltable','htmlelements');
                    $objLoginTable->cellpadding=2;
                    $objLoginTable->startRow();
                    $objLoginTable->addCell($objForm->show(),null,null,'right');
                    $objLoginTable->endRow();
                    echo $objLoginTable->show();
                }
        }

        // For developers on localhost. Please leave in.
        // Comment this out for your local use.
        // Comment in for production use.
        //echo '['.KEWL_DB_DSN.']';
?>
        </div>

<?php
    }
    // get content
    echo $this->getLayoutContent();

    if (!isset($suppressFooter)) {
         // Create the bottom template area
        $this->footerNav =  $this->newObject('layer', 'htmlelements');
        $this->footerNav->id = 'footer';
        $this->footerNav->align = "center";
        $this->loadClass('link', 'htmlelements');
        $link = new link("http://chisimba.com/");
        $link->link = "Chisimba";
        $footerStr = $this->objLanguage->languageText("mod_award_awardpowered","award");
        $this->footerNav->str = "Award&#xA9; $footerStr ".$link->show();
        if ($objUser->isLoggedIn()) {
            $link = new link($this->uri(array('action'=>'logoff'),'security'));
            $link->link = $objLanguage->languageText("word_logout");
            $str = $objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
            $this->footerNav->str .= " - $str";
        }
        echo $this->footerNav->show();
    }
    if (!isset($pageSuppressContainer)) {
	   echo '</div>';
    }
    $this->putMessages();

$sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$analyticsCode = $sysConfig->getValue('google_analytics_code', 'award');
?>

<!--script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?=$analyticsCode?>");
pageTracker._trackPageview();
} catch(err) {}</script-->

    </body>
</html>