<?php

// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

$userObj = $this->newObject('user','security');
$module = $this->getParam('module', '');
$action = $this->getParam('action', '');

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
        <link rel="stylesheet" type="text/css" href="skins/kim_wits/stylesheet.css">';
    }
    
    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);

    //Custom background line for when user logged in
    if ($userObj->isLoggedIn()) {
        echo "<style> 
                #wrapper {
                    background: transparent url('skins/kim_wits/images/background2.gif') repeat-x top left;
                    height: 130px;
                    margin-top: 0px;
                }
              </style>";
    }

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
            
<?php
        if (!isset($pageSuppressSearch)) {
            echo $objSkin->siteSearchBox();
        }
?>
        </div>

<?php

        if (!isset($pageSuppressToolbar)) {
            echo $toolbar;
        }


    }
    
    // get content
    echo $this->getLayoutContent();
   
    if (!isset($suppressFooter)) {
         // Create the bottom template area
        $this->footerNav = & $this->newObject('layer', 'htmlelements');
        $this->footerNav->id = 'footer';
        $this->footerNav->cssClass='';
        $this->footerNav->position='';

        $str = '<div id="subfooter">';

        if (isset($footerStr)) {
            $str .= $footerStr . '</div>';
        } else if ($objUser->isLoggedIn()) {
            $this->loadClass('link', 'htmlelements');
            $link = new link ($this->URI(array('action'=>'logoff'),'security'));
            $link->link=$objLanguage->languageText("word_logout");
            $str .= $objLanguage->languageText("mod_context_loggedinas", 'context')
              .' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')'.'</div>';
        }
        $this->footerNav->str = $str;
        echo $this->footerNav->show();
    }
    if (!isset($pageSuppressContainer)) {
        echo '</div>';
    }
    $this->putMessages();
?>
    </body>
</html>
