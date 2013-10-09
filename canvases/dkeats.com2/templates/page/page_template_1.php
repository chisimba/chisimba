<?php
// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

if (!isset($pageSuppressToolbar)) {
    // get toolbar object
    if ($objUser->isLoggedIn()) {
        $menu = $this->getObject('menu','toolbar');
        $toolbar = $menu->show();
        // get any header params or body onload parameters for objects on the toolbar
        $menu->getParams($headerParams, $bodyOnLoad);
    } else {
        $menu = $this->getObject('toolbardwk','dkeatscom');
        $toolbar = $menu->show();
    }

}
// Set the page title
if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}

// Set a variable to hold the current host
$curHost = $_SERVER['HTTP_HOST'];
?>
<head>
<link rel="icon" type="image/png" href="skins/dkeats.com/favicon.png" />
<title>
<?php echo $pageTitle; ?>
</title>
<meta name="keywords" content="Derek Keats, Education 3.0, Blog, Enterprise 2.0,
  Web 2.0, Free Software, Open Source, OER, Open Educational Resources,
  Free and Open Resources for Education, Open Content, Chisimba">
<meta name="description" content="Writing, audio, video about and by Professor
  Derek W. Keats on topics such as biology, education, Internet, web 2.0. Content
  is organized in content areas and blogs, with additional content mashed up from
  a variety of sites to which Derek has contributed.">
<meta name="author" content="Derek Wayne Keats">
<meta name="robots" content="index,follow" />
<meta name="verify-v1" content="Sgq12kwleFkdC8fc8MwVFLGHiwo6T9PxqGvnbRzQzII=" />
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

//Only show the dropdown on pages with toolbar to prevent it appearing in popups
if (!isset($pageSuppressToolbar)){
    ?>
	<script src="core_modules/htmlelements/resources/jquery/jquery.easing.1.3.js" type="text/javascript"></script>
	<script src="core_modules/htmlelements/resources/jquery/jquery.slideviewer.1.1.js" type="text/javascript"></script>
    <?php
}
// Supress Diqus verification if running on localhost
if ($curHost=="localhost") {
    echo "<script type=\"text/javascript\">var disqus_developer = 1;</script>";
}
?>

</script>
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
            <h1 id="sitename">
                <span><?php echo $objConfig->getsiteName();?></span>
            </h1>
            <?php if(!isset($pageSuppressSearch)) echo $objSkin->siteSearchBox();?>
        </div>
    </div>
    <?php
    if (!isset($pageSuppressToolbar)) {
        echo $toolbar;
    }
}

// Get content
echo $this->getLayoutContent();

// Render the footer
if (!isset($suppressFooter)) {
     // Create the bottom template area
    $this->footerNav = & $this->newObject('layer', 'htmlelements');
    $this->footerNav->id = 'footer';
    $this->footerNav->cssClass='';
    $this->footerNav->position='';
    $this->footerNav->str = "";
    /*if (isset($footerStr)) {
        $this->footerNav->str = $footerStr;
    } else if ($objUser->isLoggedIn()) {
        $this->loadClass('link', 'htmlelements');
        $link = new link ($this->URI(array('action'=>'logoff'),'security'));
        $link->link=$objLanguage->languageText("word_logout");
        $str=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
        $this->footerNav->str = $str;
    }*/
    echo $this->footerNav->show();
}

//Render the container closing div
if (!isset($pageSuppressContainer)) { ?>
	</div>
    <?php
}
$this->putMessages();

/*
<!---
Google analytics code

Please note that if you adapt this skin for your own
site, you must change the code from UA-1632289-2 to your
own code or it will not work.

--->
*/
$pageCode = $this->getParam('module','cms')
  . "::" . $this->getParam('action', NULL);
?>
</span>
</span>

<?php 
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

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-1632289-2");
pageTracker._initData();
pageTracker._trackPageview('<?php echo $pageCode?>');
</script>

<?php

// snapshot script
if ($curHost == "www.dkeats.com") {
    $act = $this->getparam('action', NULL);
    //die("Offline momentarily: back in < 2 minutes" . $act . "++++" . $disableapture);
    if ($act !== "edit"
      && $act !=="add"
      && $act !== "blogpost") {
        ?>
        <script type="text/javascript" src="http://shots.snap.com/ss/6123827ab5edd5046f8a8d2608b8c0f0/snap_shots.js"></script>
        <?php
    }
}

// Add the disqus plugin to the page template
//$objDq = $this->getObject('disquselems', 'disqus');
//echo $objDq->addWidget();
?>
</body>
</html>
