<?php

// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

$userObj = $this->newObject('user','security');
$module = $this->getParam('module', '');
$action = $this->getParam('action', '');
$this->loadClass('link', 'htmlelements');

$objectStories = $this->getObject("dbstories", "stories");
$sqlFilter = "1 = 1 GROUP BY CATEGORY ";
$categories = $objectStories->fetchStories($sqlFilter);
$topics = '<ul>';
foreach ($categories as $value) {
  $link = new link($this->uri(array('action'=>'viewtopic', 'category'=>$value['category'])));
  $link->link = $value['category'];
  $topics .= '<li>' . $link->show() . '</li>';
}
$topics .= '</ul>';

if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}
$toolbar = "";
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
        //echo '<link rel="stylesheet" type="text/css" href="skins/kim_wits/stylesheet.css">';
    }
    
    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);
    echo $objSkin->putSkinCssLinks();
 

?>


    </head>
<?php

$bodyOnLoad[] = "jQuery('img').width('200px');";

    if (isset($bodyParams)) {
        echo '<body '.$bodyParams.'>';
    } else {
        echo '<body>';
    }
        echo '<div id="outercontainer">';
        echo '<div id="container">';
    if (!isset($pageSuppressBanner)) {
?>
        <div id="header">
        <div id="jukskeitopics">
        <?php echo $topics ?>
        </div>
        </div>      
<?php
}



    echo '<div id="contentarea">';
    echo '<div id="topbar">';
    if (!isset($pageSuppressToolbar)) {
      echo $toolbar;
    }
    if (!isset($pageSuppressSearch)) {
      //large search
      echo $objSkin->siteSearchBox(FALSE);
    }
    echo '<div class="clearfloat"></div>';
    echo '</div>';
    
    // get content
    echo $this->getLayoutContent();
    echo '<div class="clearfloat"></div>';
    if (!isset($suppressFooter)) {
        $footerStr = '<span id="footerLeft"><a href="#">ABOUT</a></span>';
        $footerStr .= '<span id="footerMiddle"><a href="#">STAFF</a></span>';
        $footerStr .= '<span id="footerRight"><a href="#">WITS JOURNALISM</a></span><div class="clearfloat"></div>';
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
        
        
    $this->putMessages();
    echo '</div></div></div>';//outercontainer div close
?>
    </body>
</html>
