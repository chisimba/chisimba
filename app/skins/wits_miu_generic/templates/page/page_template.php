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
        //echo '<!--<link rel="stylesheet" type="text/css" href="skins/_common/base.css">-->';
        echo '<link rel="stylesheet" type="text/css" href="skins/kim_wits/stylesheet.css">';
    }
    
    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);
    echo $objSkin->putSkinCssLinks();

    /*echo '<!--[if lte IE 6]>
                <script type="text/javascript" src="skins/_common/js/WCH.js"></script>
                <script type="text/javascript" src="skins/_common/js/ADxMenu.js"></script>


                <style type="text/css">
                    body { behavior:url("skins/_common/js/ADxMenu_prof.htc"); }
                </style>
            <![endif]-->
         ';
    */

    //Custom background line for when user logged in
    if ($userObj->isLoggedIn()) {
        echo "<style> 
                #wrapper {
                    background: #e3e3e3 url('skins/kim_wits/images/background2.png') repeat-x top left;
                    height: 130px;
                    margin-top: 0px;
                }
              </style>";
    }


    /*
     * TODO: Remove these custom module hacks by fixing up the module layout it's self
     * Fixing style issues with cms min-height for content display area
     *
     * The proper way to do this would be to have CMS print out a container div with a custom ID
     * that will be used to specifiy a min-height for CMS
     */
    
    if ($module == 'cmsadmin') {
        echo "<style>

                #contentcontent {
                    min-height: 700px;
                }

              </style>";

        if ($action == 'addcontent') {
            echo "
<style>
#threecolumn #contentcontent {
    width: 550px;
    overflow:auto;
}

#twocolumn #contentcontent {
    width: 750px;
    overflow:auto;
}
</style>
";
        }  

    }  

?>

        <!--[if IE 6]>
        <style type="text/css" media="screen">
        @import "skins/kim_wits/msiefixes.css";
        </style>
        <![endif]-->


    </head>
<?php

$bodyOnLoad[] = "jQuery('img').width('200px');";

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
            //use compact search form in skin otherwise on smaller resolutions the banner gets covered 
            echo $objSkin->siteSearchBox(TRUE);
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
    // Insert the apture module code for see www.apture.com
    $objModule = $this->getObject('modules','modulecatalogue');
    // See if the apture module is registered and set a param
    $isRegistered = $objModule->checkIfRegistered('apture');
    if ($isRegistered){
        if (!isset($suppressApture)) {
            $objApture = $this->getObject('apturecode','apture');
            echo $objApture->getAptureScript();
        }
    }
?>
    </body>
</html>
