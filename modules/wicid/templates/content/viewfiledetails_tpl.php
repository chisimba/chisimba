<?php

    $extbase = '<script language="JavaScript" src="'.$this->getResourceUri('js/ext-3.0.0/adapter/ext/ext-base.js').'" type="text/javascript"></script>';
    $extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('js/ext-3.0.0/ext-all.js').'" type="text/javascript"></script>';
    $extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('js/ext-3.0.0/resources/css/ext-all.css').'"/>';
    $details = '<script language="JavaScript" src="'.$this->getResourceUri('js/filedetails.js').'" type="text/javascript"></script>';
    $maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/main.css').'"/>';
    $buttoncss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/buttons.css').'"/>';

    $this->appendArrayVar('headerParams', $extallcss);
    $this->appendArrayVar('headerParams', $extbase);
    $this->appendArrayVar('headerParams', $extalljs);
    $this->appendArrayVar('headerParams', $maincss);
    $this->appendArrayVar('headerParams', $buttoncss);
    $this->appendArrayVar('headerParams', $details);

    // objects
    $objIcon= $this->newObject('geticon','htmlelements');
    $myarray = array("1"=>"public", "2"=>"private");
    
    $fileinfo = $this->objUploads->getFileName($id);
    $filename = $fileinfo['filename'];
    $filetype = $fileinfo['filetype'];
    $dateUploaded = $fileinfo['date_uploaded'];
    $date = date_create($dateUploaded);
    $permissions = $myarray[$fileinfo['shared']];
    $owner = $this->objUser->fullname($fileinfo['userid']);


    // Create an instance of the css layout class
    $cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
    $cssLayout->setNumColumns(2);
    $postLoginMenu  = $this->newObject('postloginmenu','toolbar');
    $cssLayout->setLeftColumnContent($postLoginMenu->show());
    $rightSideColumn =  '<div id ="mainContent">';
    $content = '<div id="heading"><h1>'.$this->objUtils->showPageHeading($action).'</h1></div>';
    $content .= '<div id="buttons"></div>';
    $content .= '<div id="summary"></div>';
    $rightSideColumn .= $content;
    $cssLayout->setMiddleColumnContent($rightSideColumn);

    echo $cssLayout->show();



    $mainjs = "<script type='text/javascript'>
        Ext.onReady(function() {
            Ext.QuickTips.init();

            var summaryData=['".$owner."','".$filename."','".$filetype."','".date_format($date, "d/m/Y")."','".$permissions."'];
            showButtons();
            showSummary(summaryData);";
    $mainjs .= "
        });";
    $mainjs .= "</script>";

    echo $mainjs;
?>
