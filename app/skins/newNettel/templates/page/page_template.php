<?php

// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

if (!isset($pageSuppressToolbar)) {
   // get toolbar object
   $menu = $this->newObject('menu','toolbar');
   $toolbar = $menu->show();

   // get any header params or body onload parameters for objects on the toolbar
   $menu->getParams(&$headerParams, &$bodyOnLoad);
}


if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}
?><head>
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

echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);


?>
</head>
<?php


if (isSet($bodyParams)) {
    echo "<body " . $bodyParams . ">";
} else {
    echo '<body class="'.$this->getParam('module', 'cms').'">';
}


 	if (!isset($pageSuppressContainer)) {
 	    echo '<div id="container">';
 	}

 	if (!isset($pageSuppressBanner)) {
?>

			<div id="headerwrapper">
				<div id="header">
					<h1 id="sitename"><span><?php echo $objConfig->getsiteName();?></span></h1>
			
				</div>

			</div>
<div id="search">

<?php if(!isset($pageSuppressSearch)){
    echo $objSkin->siteSearchBox();
 } ?>
</div>
<?php
					 if (!isset($pageSuppressToolbar)) {
						//$menu=& $this->getObject('menu','toolbar');
						//echo $menu->show();
						echo $toolbar;
					 }
					 ?><?php  }

    // get content
    echo $this->getLayoutContent();

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
?>

<?php if (!isset($pageSuppressContainer)) { ?>
	 </div>
<?php } ?>
<?php
 $this->putMessages();
?>
</body>
</html>
