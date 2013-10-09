<?php
/*
 * dkeats.com2 canvase page template
 *
 * This is the page template in the dkeats.com2 skin
 * 
 * Notes: 
 *   1. There is no headerwrapper in this skin
 *
 */
// Add navigation back to top of page.
define("PAGETOP", '<a name="pagetop"></a>');
define("GOTOTOP", '<a href="#pagetop">Top</a>'); // @todo change this to an icon

// Initialise the variable holding preferred canvas
$prefCanvas=FALSE;

// Initialise the layout settings
$setCanvas = FALSE;

// Define the name of this skin.
$skinName = "ab";

// Define the valid canvases for this skin as an array.
$validCanvases = array_map('basename', glob('skins/' . $skinName . '/canvases/*', GLOB_ONLYDIR));

// Settings that are needed so that canvase-aware code can function.
$this->setSession('skinName', $skinName);
$_SESSION['skinName'] = $skinName;
$_SESSION['isCanvas'] = TRUE;
$_SESSION['sourceSkin'] = $skinName;
$_SESSION['layout'] = '_DEFAULT';

// Instantiate the canvas object.
$objCanvas = $this->getObject('canvaschooser', 'canvas');

// Set the skin base for the default.
$skinBase='skins/' . $skinName . '/canvases/';
if (isset ($canvas)) {
    $_SESSION['canvasType'] = 'programmatic';
    $_SESSION['canvas'] = $canvas;
    $canvas = $skinBase . $canvas;
} elseif ($prefCanvas) {
    $canvas = $skinBase . $prefCanvas;
} else {
    // Get what canvas we should be showing
    $canvas = $objCanvas->getCanvas($validCanvases, $skinBase);
}

// Check if there is a settings file and load it
if (!isset($pageSuppressSkin)) {
    $canvasName = $objCanvas->getCanvasName($canvas);
    $settingsFile = $objSkin->getSkinLocation().'canvases/' . $canvasName . '/settings.php';
    if(file_exists($settingsFile)) {
        require_once $settingsFile;
    }
}

// Get Header that goes into every skin.
$siteRootPath = $objConfig->getsiteRootPath();
require($siteRootPath . 'skins/_common/templates/skinpageheader3-0.php');


// Set up the open graph stuff
if (!isset($og_title)) {
    $og_title = $pageTitle;
}
if (!isset($og_image)) {
    $og_image = $helperJs = 'skins/' . $skinName . '/default.png';
}
if (!isset($og_content)) {
    $og_content = 'AdoraBella is based in Johannesburg, South Africa, and 
        delivers the finest quality human hair extensions and care 
        products to customers in the broader Gauteng area.';
} else {
    $og_content = strip_tags($og_content);
}


$publicNav = '
    <ul id="ab_navmenu">
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php?module=slate&page=aboutus">About us</a></li>
        <li><a href="index.php?module=slate&page=abouthair">About hair</a></li>
        <li><a href="index.php?module=slate&page=products">Products</a></li>
        <li><a href="index.php?module=slate&page=wedding">Wedding packages</a></li>
        <li><a href="index.php?module=gallery">Photo gallery</a></li>
        <li><a href="index.php?module=slate&page=maintain">Hair maintenance</a></li>
        <li><a href="index.php?module=faq">FAQ</a></li>
        <li><a href="index.php?module=slate&page=contact">Contact</a></li>
    </ul>
'



// Render the head section of the page. Note that there can be no space or
// blank lines between the PHP closing tag and the HTML head tag. It must be
// exactly as below.
?><head>
    <meta property="og:title" content="<?php echo $og_title; ?>" />
    <meta property="og:image" content="<?php echo $og_image; ?>" />
    <meta property="og:description" content="<?php echo $og_content; ?>" />
    <title>
        <?php echo $pageTitle; ?>
    </title>
    <?php
    // Get the skin version 2 base CSS for all skins.
    if (!isset($pageSuppressSkin)) {
        echo '

        <link rel="stylesheet" type="text/css" href="skins/_common2/css/basecss.php">
        <link rel="icon" type="image/png" href="skins/' . $skinName . '/favicon.png" />
        ';
     }


    // Render the javascript unless it is suppressed.
    if (!isset($pageSuppressJavascript)) {
        echo $objSkin->putJavaScript($mime, $headerParams);
        // Easing ..... move this to use the included one.....
        //$bbJs = 'skins/' . $skinName . '/javascript/vendor/jquery.easing.1.3.js';
       //echo "\n<script type='text/javascript' src='" . $bbJs . "'></script>\n\n";
        // Easing ..... move this to use the included one.....
        //$bbJs = 'skins/' . $skinName . '/javascript/vendor/jquery.mobile.customized.min.js';
        //echo "\n<script type='text/javascript' src='" . $bbJs . "'></script>\n\n";
        // Load camera image slider
        $bbJs = 'skins/' . $skinName . '/javascript/vendor/camera.min.js';
        echo "\n<script type='text/javascript' src='" . $bbJs . "'></script>\n\n";

        
        
        // Load the helper JS from the current skin
        $helperJs = 'skins/' . $skinName . '/javascript/skinhelper.js';
        echo "\n<script type='text/javascript' src='" . $helperJs . "'></script>\n\n";
    }

    // Render the CSS for the current skin unless it is suppressed.
    if (!isset($pageSuppressSkin)) {
       echo '
       <link rel="stylesheet" type="text/css" href="skins/' . $skinName . '/stylesheet.css">
       <link rel="stylesheet" type="text/css" href="' . $canvas . '/stylesheet.css">
       <link rel="stylesheet" type="text/css" href="skins/' . $skinName . '/javascript/vendor/camera.css">

        ';
    }
    ?>
</head>

<?php
// Render body parameters if they are set, otherwise render a plain body tag
if (isset($bodyParams)) {
    echo '<body '.$bodyParams.'>';
} else {
    echo '<body>';
}

// Render the container & canvas elements unless it is suppressed.
if (!isset($pageSuppressContainer)) { ?>
    <div id='OutermostWrapper'>
        <div class='ChisimbaCanvas' id='_default'>
            <div id='Canvas_Content'>
                <div id='Canvas_BeforeContainer'></div>
                <div id='container'>
<?php
}

// Render the banner area unless it is suppressed
if (!isset($pageSuppressBanner)) {
    // Because the link to page top is in the footer, put the top here
    // only if the footer is not suppressed.
    if (!isset($suppressFooter)) {
        echo PAGETOP;
    }
    echo "\n\n<div class='Canvas_Content_Head_Before'>" 
      . getIcons($skinName, $canvas);
    if (!isset($pageSuppressSearch)) {
        //echo $objSkin->siteSearchBox();
    }
    echo "</div>\n\n"
    ?>
    <div class="Canvas_Content_Head">
        <div class="Canvas_Content_Head_Header" id="header">
            <a href="<?php echo $objConfig->getSiteRoot(); ?>">
                <img src='skins/<?php echo $skinName; ?>/images/banner.jpg' />
            </a>
        </div>
        <?php
}

if (!isset($pageSuppressBanner)) {
    echo "</div>";
    if (!isset($pageSuppressToolbar)) {
        ///$simulate = $this->getParam('simulate', NULL);
        if ($this->objUser->isLoggedIn()) {
            if ($this->objUser->isAdmin()) {
                echo "\n\n<div id='navigation'>\n\n" . $toolbar . "\n</div>\n\n";
            }
        }
        
    }
    echo '<div class="Canvas_Content_Head_After">' . $publicNav . '</div>';
}



// Render the layout content as supplied from the layout template
echo "<div class='Canvas_Content_Body_Before'></div>\n"
   . "<div id='Canvas_Content_Body'>\n"
   . $this->getLayoutContent()
   . "</div>\n<div class='Canvas_Content_Body_After'></div>\n"
   .'<br id="footerbr" />';


// If the footer is not suppressed, render it out.
if (!isset($suppressFooter)) {
    $footerInfo = '<br /><br /><div class="footerinfo">AdoraBelle (Pty) Ltd is based in 
        Johannesburg, South Africa, and delivers products to customers in 
        the broader Gauteng area, as well as Cape Town.</div><div class="minute">
        Powered by <a href="http://chisimba.com">Chisimba</a></div>';
        
    // Add the footer string if it is set
    if (isset($footerStr)) {
       $footerStr = $footerStr;
    } else if ($objUser->isLoggedIn()) {
        $this->loadClass('link', 'htmlelements');
        $link = new link ($this->URI(array('action'=>'logoff'),'security'));
        $link->link=$objLanguage->languageText("word_logout");
        $str=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
        $footerStr = $str;
    } else {
        $footerStr = NULL;
    }
    // Do the rendering here.
    echo "<div class='Canvas_Content_Footer_Before'></div>"
      . "<div class='Canvas_Content_Footer'><div id='footer'>"
      . $footerStr;
    // Put in the link to the top of the page
    if (!isset($pageSuppressBanner)) {
        echo ' (' . GOTOTOP . ')';
    }
    echo $footerInfo . "</div>\n</div>\n<div class='Canvas_Content_Footer_After'></div>";
}
// Render the container's closing div if the container is not suppressed
if (!isset($pageSuppressContainer)) {
    echo "</div><div class='Canvas_AfterContainer'></div>\n</div>\n</div></div>";
}



// Render any messages available.
$this->putMessages();

/**
 *
 * Throw in my social networking icons. Probably could make this a
 * Chisimba object but for now it is just for my own skin.
 *
 * @param stgring $skinName The name of the skin to look in
 * @param string $canvas The name of the canvas to look in inside the skin
 * @return string The rendered icons
 * 
 */
function getIcons($skinName, $canvas)
{
    $available=array(
        'facebook' => 'http://www.facebook.com/brazilianandindianhairweaves',
        'twitter' => 'http://twitter.com/AdorabelleHair',
    );

    $ret ="";
    foreach ($available as $img=>$link) {
        $img = '<img class="social_icon" src="' . $canvas . '/resources/img/' . $img . '.png" alt="' . $img . '">';
        $ret .= '<a href="' . $link . '" target="_blank">' . $img . "</a>";
    }
    return "<div class='social_icon_container'>" . $ret . "</div>";
}
?>
</body>
</html>