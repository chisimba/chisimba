<?php

// Get Header that goes into every skin
// This also starts the HTML tag
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

// Override Page Title if Set
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
        <title><?php echo $pageTitle; ?></title>
<?php
    // If Skin is not suppressed, output it
    if (!isset($pageSuppressSkin)) {
        echo $objSkin->putSimpleSkinCssLinks();
    }
    
    // Output any other javascript required
    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);
    
?>
    </head>
<?php
    // Start Body Tag
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
         // Create the bottom template area - footer
        echo '<div id="footer">';
        
        // If Footer message is given display that.
        // Else if User is logged in, create a log out link
        // Else show nothing
        if (isset($footerStr)) {
            echo $footerStr;
        } else if ($objUser->isLoggedIn()) {
            $this->loadClass('link', 'htmlelements');
            $link = new link ($this->uri(array('action'=>'logoff'), 'security'));
            $link->link=$objLanguage->languageText("word_logout");
            $str= $objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
            echo $str;
        } else {
            // nothing to display
        }
        echo '</div>';
    }
    if (!isset($pageSuppressContainer)) {
        echo '</div>';
    }
    $this->putMessages();
?>
    </body>
</html>