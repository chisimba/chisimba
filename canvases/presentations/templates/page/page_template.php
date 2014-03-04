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
$skinName = "presentations";

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
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader3-0.php');

// Render the head section of the page. Note that there can be no space or
// blank lines between the PHP closing tag and the HTML head tag. It must be
// exactly as below.
?><head>
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
    }

    // Render the CSS for the current skin unless it is suppressed.
    if (!isset($pageSuppressSkin)) {
       echo '

       <link rel="stylesheet" type="text/css" href="skins/' . $skinName . '/base.css">
       <link rel="stylesheet" type="text/css" href="skins/' . $skinName . '/basemod.css">
       <link rel="stylesheet" type="text/css" href="skins/' . $skinName . '/iehacks.css">
       <link rel="stylesheet" type="text/css" href="skins/' . $skinName . '/stylesheet.css">
       <link rel="stylesheet" type="text/css" href="' . $canvas . '/stylesheet.css">

        ';
       //<script type="text/javascript" src="skins/' . $skinName . '/js/jquery.equalHeightColumns.js" />
    }
    ?>
</head>
<body>
    <div id="page_shadows">
        <div id="page_margins">
            <div id="page">

                <div id="topnav">
                    <a  href="http://www.chisimba.com">Chisimba.com</a>
                    
                </div>

                <div id="header">
                    <div id="banner">
                       <img src="skins/presentations/images/webPresent_banner.png" width="100%" height="125"  alt="HOME"/>
                    </div>
                    
                </div>

                <!-- begin: main navigation #nav -->
                <div id="nav">
                    <div id="nav_main">
                        <div class="ddcolortabs">
                            <?php
                            $menu= $this->getObject('webpresenttoolbar2','webpresent');
                            echo $menu->show()
                            ?>
                        </div>


                    </div>
                </div>


                <div id="main" style="width: 950px">
                        <div id="contentwrapper" class="subcolumns">
                      <?php echo $this->getLayoutContent(); ?>
                        </div>

                </div>


            </div>
        </div>
    </div>

</body>
</html>