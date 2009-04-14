<?php

// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader2-0.php');

?>
    <head>
	
        <title>
<?php
    echo $pageTitle;
?>
        </title>
<?php
    if (!isset($pageSuppressSkin)) {
        echo '<link rel="stylesheet" type="text/css" href="skins/_common2/base.css">
				  <link rel="stylesheet" type="text/css" href="skins/refractions/stylesheet.css">';
    }
    
    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);

?>

    </head>
<?php
    if (isset($bodyParams)) {
        echo '<body '.$bodyParams.'>';
    } else {
        echo '<body>';
    }
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
        
?>
        </div>
	<div id="navigation">
	<?php
	if (!isset($pageSuppressToolbar)) {
            echo $toolbar;
        }
		?>
		<div>
<?php
    }
    
    // get content
    echo $this->getLayoutContent().'<br id="footerbr" />';
    
    if (!isset($suppressFooter)) {
         $footerStr = "";
        if (isset($footerStr)) {
           $footerStr = $footerStr;
        } else if ($objUser->isLoggedIn()) {
            $this->loadClass('link', 'htmlelements');
            $link = new link ($this->URI(array('action'=>'logoff'),'security'));
            $link->link=$objLanguage->languageText("word_logout");
            $str=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
            $footerStr= $str;
        }
        echo '<div id="footer">'.$footerStr.'</div>';
    }
    if (!isset($pageSuppressContainer)) {
        echo '</div>';
    }
    $this->putMessages();
?>
    </body>
</html>