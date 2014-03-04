<?php
$this->loadclass('link','htmlelements');

$ext=$this->getObject('extjs','htmlelements');
$ext->show();
$fileupload= '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ux/fileuploadfield/FileUploadField.js','htmlelements').'" type="text/javascript"></script>';


//$fileupload = '<script src="'.$this->getResourceUri('js/ext-3.0.0/examples/ux/FileUploadField.js').'" type="text/javascript"></script>';
$uploadjs = '<script src="'.$this->getResourceUri('js/upload.js').'" type="text/javascript"></script>';
$uploadcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/file-upload.css').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/main.css').'"/>';
$buttoncss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/buttons.css').'"/>';
$myTmpCss = "<style>div#buttons { padding-bottom: 0;} </style>";

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $uploadcss);
$this->appendArrayVar('headerParams', $fileupload);
$this->appendArrayVar('headerParams', $uploadjs);
$this->appendArrayVar('headerParams', $maincss);
$this->appendArrayVar('headerParams', $buttoncss);
$this->appendArrayVar('headerParams', $myTmpCss);

// urls
$uploadUrl = str_replace("amp;", "", $this->uri(array('action'=>'doupload')));

$upload = "";
if($this->getParam("upload")) {
    $upload = $this->getparam("upload");
}

$error = "";
if(strlen($this->getParam('message')) > 0) {
    $error = $this->getParam('message');
}

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);
$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent($postLoginMenu->show());
$rightSideColumn =  '<div id ="mainContent">';
$content = '<div id="heading"><h1>'.$this->objUtils->showPageHeading($action).'</h1></div>';
$content .= '<div id="buttons"></div>';
$content .= '<div id="error">'.$error.'</div><div id="fi-form"></div>';
$rightSideColumn .= $content;
$rightSideColumn .= '</div>';
$cssLayout->setMiddleColumnContent($rightSideColumn);

echo $cssLayout->show();

$mainjs = '<script type="text/javascript">
        Ext.onReady(function() {
            Ext.QuickTips.init();';

$mainjs .= $this->objUtils->showUploadForm($uploadUrl);
$mainjs .= '
                });';
$mainjs .= '</script>';
echo $mainjs;
?>