<?php

// Get Header that goes into every skin
require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

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
        echo $objSkin->putSkinCssLinks();

    }
    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);

    ?>
    
    <link rel="stylesheet" type="text/css"  href="skins/_common/base.css" />
    <link rel="stylesheet" type="text/css"  href="skins/wits_conferences/base.css" />
    <link rel="stylesheet" type="text/css"  href="skins/wits_conferences/basemod.css" />
    <link rel="stylesheet" type="text/css"  href="skins/wits_conferences/iehacks.css" />
    <link rel="stylesheet" type="text/css"  href="skins/wits_conferences/stylesheet.css" />
</head>
<body>
    <div id="page_shadows">
        <div id="page_margins">
            <div id="page">

                <div id="topnav">
                    <a  href="http://www.wits.ac.za">Wits Portal</a>
                    
                </div>

                <div id="header">
                    <div id="banner">
                       <img src="skins/wits_conferences/images/banner.png" width="100%" height="125"  alt="HOME"/>
                    </div>
                    
                </div>

                <!-- begin: main navigation #nav -->
                <div id="nav">
                    <div id="nav_main">
                        <div class="ddcolortabs">
                            <?php
                            $menu= $this->getObject('conferencetoolbar','witsconferences');
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
