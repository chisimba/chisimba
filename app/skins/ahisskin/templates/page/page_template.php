<?php

// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}

// Add Toolbar if not suppressed
if (!isset($pageSuppressToolbar)) {
    
    // Get Toolbar Object
    $menu = $this->getObject('menu','toolbar');
    $toolbar = $menu->show();
    
    // get any header params or body onload parameters for objects on the toolbar
    $menu->getParams($headerParams, $bodyOnLoad);
}

?>
    <head>
        <title>
<?php
    echo $pageTitle;
?>
        </title>
<?php
    if (!isset($pageSuppressSkin)) {
        echo '<link rel="stylesheet" type="text/css" href="skins/_common/base.css">
        <link rel="stylesheet" type="text/css" href="skins/ahisskin/stylesheet.css">';
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
                
<?php
        echo '<a href="'.$objConfig->getSiteRoot().'"><span>'.$objConfig->getsiteName().'</span></a>';
?>
                
            </h1>
<?php
        if (!isset($pageSuppressSearch)) {
            echo '<div id="search"> </div>';
        }
        if (!isset($pageSuppressToolbar)) {
            echo $toolbar;
        }
?>
        </div>

<?php
    }
    
    // get content
    echo '<div id="middlearea">'.$this->getLayoutContent().'</div>';
    
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
    if (!isset($pageSuppressContainer)) {
        echo '</div>';
    }
    $this->putMessages();
?>
    </body>
</html>
