<?php
    $extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
    $extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
    $extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
    $config = '<script language="JavaScript" src="'.$this->getResourceUri('js/config.js').'" type="text/javascript"></script>';
    $maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/main.css').'"/>';
    $buttoncss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/buttons.css').'"/>';

    $this->appendArrayVar('headerParams', $extallcss);
    $this->appendArrayVar('headerParams', $extbase);
    $this->appendArrayVar('headerParams', $extalljs);
    $this->appendArrayVar('headerParams', $config);
    $this->appendArrayVar('headerParams', $maincss);
    $this->appendArrayVar('headerParams', $buttoncss);

    // objects
    $objIcon= $this->newObject('geticon','htmlelements');

    $typeUrl = str_replace("amp;", "", $this->uri(array('action'=>'savefiletype')));;

    // Create an instance of the css layout class
    $cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
    $cssLayout->setNumColumns(2);
    $postLoginMenu  = $this->newObject('postloginmenu','toolbar');
    $cssLayout->setLeftColumnContent($postLoginMenu->show());
    $rightSideColumn =  '<div id ="mainContent">';
    $content = '<div id="heading"><h1>'.$this->objUtils->showPageHeading($action).'</h1></div>';
    $content .= '<div id="buttons"></div>';
    $content .= '<div id="config"></div>';
    $content .= '<div id="tabs"></div>';
    $content .= '<div id="filetype" class="x-hide-display"></div>';
    $content .= '</div>';
    $content .= '<div id="addtype-win" class="x-hidden"><div class="x-window-header">Add New File Type</div></div>';
    $rightSideColumn .= $content;
    $cssLayout->setMiddleColumnContent($rightSideColumn);

    echo $cssLayout->show();

    $count = 1;
    $ret = $this->objPermitted->getFileTypeData();
    $numRows = count($ret);
    $data = "[";

    $deleteFileType=new link();

    foreach($ret as $row) {
        $deleteFileType->link($this->uri(array('action'=>'deletefiletype','id'=>$row['id'])));
        $objIcon->setIcon('delete');
        $deleteFileType->link=$objIcon->show();
        $data .= "['".$row['name']."','".$row['ext']."', '".$deleteFileType->show()."']";

        if($count < $numRows) {
            $data .= ",";
        }
        $count++;
    }
    $data .= "]";

    $mainjs = "<script type='text/javascript'>
        Ext.onReady(function() {
            Ext.QuickTips.init();

            typeURL = '$typeUrl';
            showButtons();
            showTabs();
            showFileType($data);";
    $mainjs .= "
        });";
    $mainjs .= "</script>";
    
    echo $mainjs;
?>
