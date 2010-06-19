<?php
/*
 * METALLIC
 *
 * This is the page template in the Metallic skin. Metallic is an attempt
 * to cover a significant component of the look and feel of Chisimba so that
 * it can serve as the basis for building other skins.
 *
 */
// Add navigation back to top of page.
define("PAGETOP", '<a name="pagetop"></a>');
define("GOTOTOP", '<a href="#pagetop">Top</a>'); // @todo change this to an icon

// Define the valid canvases for this skin as an array.
$validCanvases = array("_default", "aqua", "beach", "chisimba", "experim", "greyfloral", "modcanvas", "red", "blue", "yellow");

// Define the name of this skin.
$skinName = "canvas";

// Settings that are needed so that canvase-aware code can function
$this->setSession('skinName', 'metallic');
$this->setSession('isCanvas', TRUE);
$this->setSession('sourceSkin', 'metallic');
$this->setSession('layout', '_DEFAULT');

// Instantiate the canvas object.
$objCanvas = $this->getObject('canvaschooser', 'canvas');


// Set the skin base for the default.
$skinBase='skins/metallic/canvases/';
if (isset ($canvas)) {
    $this->setSession('canvasType', 'programmatic');
    $this->setSession('canvas', $canvas);
    $canvas = $skinBase . $canvas;
} elseif ($prefCanvas) {
    $canvas = $skinBase . $prefCanvas;
} else {
    // Get what canvas we should be showing
    $canvas = $objCanvas->getCanvas($validCanvases, $skinBase);
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

        <link rel="stylesheet" type="text/css" href="skins/_common2/base.css">
        ';
     }

     // Render the javascript unless it is suppressed.
    if (!isset($pageSuppressJavascript)) {
       echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);
    }

    // Render the CSS for the current skin unless it is suppressed.
    if (!isset($pageSuppressSkin)) {
       echo '
       <link rel="stylesheet" type="text/css" href="skins/metallic/stylesheet.css">
       <link rel="stylesheet" type="text/css" href="' . $canvas . '/stylesheet.css">
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

// --------------- BELONGS IN LAYOUT TEMPLATE

// Render the container & canvas elements unless it is suppressed.
if (!isset($pageSuppressContainer)) {
    echo "<div class='ChisimbaCanvas' id='_default'>\n"
      . "<div id='Canvas_Content'>\n"
      . "<div id='Canvas_BeforeContainer'></div>"
      . "<div id='container'>";
}

// Render the banner area unless it is suppressed
if (!isset($pageSuppressBanner)) {
    // Because the link to page top is in the footer, put the top here
    // only if the footer is not suppressed.
    if (!isset($suppressFooter)) {
        echo PAGETOP;
    }
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
        ?>
    </div>
    <?php
}

if (!isset($pageSuppressToolbar)) {
    echo "\n\n<div id='navigation'>\n\n" . $toolbar . "\n</div>\n\n";
}





// Render the laout content as supplied from the layout template
echo $this->getLayoutContent().'<br id="footerbr" />';






// If the footer is not suppressed, render it out.
if (!isset($suppressFooter)) {
    // Add the footer string if it is set
    if (isset($footerStr)) {
       $footerStr = $footerStr;
    } else if ($objUser->isLoggedIn()) {
        $this->loadClass('link', 'htmlelements');
        $link = new link ($this->URI(array('action'=>'logoff'),'security'));
        $link->link=$objLanguage->languageText("word_logout");
        $str=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
        $footerStr= $str;
    } else {
        $footerStr = $objLanguage->languageText("mod_security_poweredby", 'security', 'Powered by Chisimba');
    }
    // Do the rendering here.
    echo '<div id="footer">' . $footerStr;
    // Put in the link to the top of the page
    if (!isset($pageSuppressBanner)) {
        echo ' (' . GOTOTOP . ')';
    }
    echo '</div>';
}
// Render the container's closing div if the container is not suppressed
if (!isset($pageSuppressContainer)) {
    echo "</div><div id='Canvas_AfterContainer'></div>\n</div>\n</div>";
}



// Render any messages available.
$this->putMessages();

// Close up the body and HTML and finish up.
?>
</body>
</html>