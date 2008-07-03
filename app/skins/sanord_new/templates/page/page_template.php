<?php

// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

  // get toolbar object
   //$menu = $this->getObject('menu','toolbar');
   $menu = $this->getObject('fsiumenu','fsiu');
   $toolbar = $menu->show();
   
   // get any header params or body onload parameters for objects on the toolbar
   //$menu->getParams(&$headerParams, &$bodyOnLoad);



if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}
?>
    <head>
	<link rel="icon" 
      type="image/png" 
      href="skins/fsiu/favicon.png" />
        <title>
<?php 
    echo $pageTitle; 
?>
        </title>
<?php
    if (!isset($pageSuppressSkin)) {
        echo $objSkin->putSkinCssLinks();
        
        // Not Needed, Using own type of toolbar
        /*
        if (!isset($pageSuppressToolbar)) {
            echo '<!--[if lte IE 6]>
                <style type="text/css">
                    body { behavior:url("skins/_common/js/ADxMenu_prof.htc"); }
                </style>
            <![endif]-->
';
        }*/
        
        echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);
        
        echo '<link rel="stylesheet" type="text/css"  media="screen" href="skins/fsiu/screen.css" />
        <link rel="alternate stylesheet" title="medium" type="text/css" media="screen" href="skins/fsiu/size_medium.css" />
		<link rel="alternate stylesheet" title="large" type="text/css" media="screen" href="skins/fsiu/size_large.css" />
		
<script type="text/javascript">
//<![CDATA[
/* Nothing here needs to be modified */
this.setCSS = function(style)
{
                    var i, cacheobj;
                    for(i=0;(cacheobj=document.getElementsByTagName("link")[i]);i++)
                    {
                                         if(cacheobj.getAttribute("rel").indexOf("style") != -1 && cacheobj.getAttribute("title"))
                                         {
                                                             cacheobj.disabled=true;
                                                             if(cacheobj.getAttribute("title")==style)
                                                                                 cacheobj.disabled=false;
                                         }
                    }
}
this.SwatchCSS = function(style)
{
                    if(document.getElementById)
                    {
                                         setCSS(style);
                                         setCookie("SwatchCSS", style);
                    }
}
this.getCookie = function(Name)
{
                    var replace = new RegExp(Name+"=[^;]+", "i");
                    if(document.cookie.match(replace))
                                         return document.cookie.match(replace)[0].split("=")[1]
                                         return null
}
this.setCookie= function(name, value)
{
                    var expires = new Date();
                    var expires_str=expires.setDate(expires.getDate()+parseInt(365));
                    document.cookie = name+"="+value+"; expires="+expires.toGMTString()+"; path=/";
}
var currentCSS=getCookie("SwatchCSS");
if(document.getElementById && currentCSS!=null)  setCSS(currentCSS);
//]]>
</script>
';
    }

        //This is adding yahoo libs for tabs
        echo $this->getJavascriptFile('yahoo/yahoo.js', 'yahoolib')."\n";
        echo $this->getJavascriptFile('event/event.js', 'yahoolib')."\n";
        echo  $this->getJavascriptFile('dom/dom.js', 'yahoolib')."\n";
        echo $this->getJavascriptFile('element/element-beta.js', 'yahoolib')."\n";
        echo $this->getJavascriptFile('tabview/tabview.js', 'yahoolib')."\n";
   

?>
    </head>
<?php
    if (isSet($bodyParams)) {
        echo '<body ' . $bodyParams . ' style="background-color: WhiteSmoke; font-family: Tahoma; font-size: 12px;">';
    } else {
        echo '<body style="background-color: WhiteSmoke; font-family: Tahoma; font-size: 12px;">';
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
	 
   


        
        </div>
	<div style="float:right;margin:0 0 ; /**background-color: red;**/ " id="search">
                <?echo $objSkin->siteSearchBox(); ?>

        </div>	
<?php
        //if (!isset($pageSuppressToolbar)) {
            //$menu= $this->getObject('menu','toolbar');
		  echo '<div id="menuwrapper">'.$toolbar.'</div>';
        //}
    }
    // get content
    echo '<div id="contentlayout">'.$this->getLayoutContent().'</div>';

    if (!isset($suppressFooter)) {
         // Create the bottom template area
        $this->footerNav = & $this->newObject('layer', 'htmlelements');
        $this->footerNav->id = 'footer';
        $this->footerNav->cssClass='';
        $this->footerNav->position='';
        $str = '<img id="ics_logo" src="skins/fsiu/newimages/FSIUFOOTER_icons.gif" alt="ICS logo" title="ICS" align="MIDDLE" />';
        if (isset($footerStr)) {
            $this->footerNav->str = $footerStr.$str;
        } else if ($objUser->isLoggedIn()) {
            $this->loadClass('link', 'htmlelements');
            $link = new link ($this->URI(array('action'=>'logoff'),'security'));
            $link->link=$objLanguage->languageText("word_logout");
            $str1=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
            $this->footerNav->str = $str1.'<br />'.$str;
        } else {
            $this->footerNav->str = $str;
        }
        echo $this->footerNav->show();
    }
    if (!isset($pageSuppressContainer)) {
	   echo '</div>';
    }   
    //$this->putMessages();
?>
    </body>
</html>
