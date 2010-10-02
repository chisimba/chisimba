<?php  
//do the check to check if TII is accessable

    $objExtJS = $this->getObject('extjs','ext');
    $objExtJS->show();

    $fullUri = $this->uri(NULL);
$fullUri = explode("?",$fullUri);
$siteUri = $fullUri[0];

    $ext = $this->getJavaScriptFile('ColumnNodeUI.js', 'groupadmin');
    $ext .= $this->getJavaScriptFile('Ext.ux.grid.Search.js', 'groupadmin');
    $ext .= $this->getJavaScriptFile('users.js', 'groupadmin');
    $ext .= $this->getJavaScriptFile('interface.js', 'groupadmin');

    //setup the dynamicuri
    $this->appendArrayVar('headerParams', '
                    <script type="text/javascript">
                            var baseUri = "'.$siteUri.'";
                    </script>');

    // THIS IS CRAP and needs to be done properly -- Dkeats, 2010 10 03
    $ext .= "<style>

        #main-interface{
                padding:10px;
                margin:10px;
        }
        pre {
                font-size:11px;
        }

        .x-tab-panel-body .x-panel-body {
            padding:10px;
        }

        /* default loading indicator for ajax calls */
        .loading-indicator {
                font-size:8pt;
                background-image:url('../../resources/images/default/grid/loading.gif');
                background-repeat: no-repeat;
                background-position: left;
                padding-left:20px;
        }

        .new-tab {
            background-image:url(../feed-viewer/images/new_tab.gif) !important;
        }


        .tabs {
            background-image:url( ../desktop/images/tabs.gif ) !important;
        }

        p { width:650px; }


</style>";

$this->appendArrayVar('headerParams', $ext);

$middleColumn = '
    <div id="mainPanel"></div>
    <div id="combo"></div>
';

// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
// Get the admin menu for the left menu
$adminMenu = $this->getObject('adminmenu', 'toolbar');
// Set the left menu into the left CSS layout
$cssLayout->setLeftColumnContent($adminMenu->show());

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($middleColumn);

// Display the Layout
echo $cssLayout->show();
?>