
<?php
$this->loadclass('link','htmlelements');
$this->loadclass('link','htmlelements');
$objIcon= $this->newObject('geticon','htmlelements');
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';

$uxjs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ux/fileuploadfield/FileUploadField.js','htmlelements').'" type="text/javascript"></script>';
$uxdataviewjs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ux/DataView-more.js','htmlelements').'" type="text/javascript"></script>';

$iconscss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/buttons.css').'"/>';
$homejs = '<script language="JavaScript" src="'.$this->getResourceUri('js/home.js').'" type="text/javascript"></script>';
$homecss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/main.css').'"/>';
$fucss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/file-upload.css').'"/>';
$dataviewcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/data-view.css').'"/>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $uxjs);
$this->appendArrayVar('headerParams', $uxdataviewjs);
$this->appendArrayVar('headerParams',$iconscss);
$this->appendArrayVar('headerParams',$fucss);
$this->appendArrayVar('headerParams',$homejs);
$this->appendArrayVar('headerParams',$homecss);
$this->appendArrayVar('headerParams',$dataviewcss);

$upload = "";
if($this->getParam("upload")) {
    $upload = $this->getparam("upload");
}

$error = "";
if(strlen($this->getParam('message')) > 0) {
    $error = $this->getParam('message');
}


$dataUrl = str_replace("amp;", "", $this->uri(array('action'=>'getFiles','mode'=>'folders')));
$filesUrl = str_replace("amp;", "", $this->uri(array('action'=>'getFiles','mode'=>'files')));

$createFolderUrl = str_replace("amp;", "", $this->uri(array('action'=>'createfolder')));
$renameFolderUrl = str_replace("amp;", "", $this->uri(array('action'=>'renamefolder')));
$deleteFolderUrl = str_replace("amp;", "", $this->uri(array('action'=>'deletefolder')));
$uploadUrl = str_replace("amp;", "", $this->uri(array('action'=>'doupload')));
$settingsUrl = str_replace("amp;", "", $this->uri(array('action'=>'admin')));

$modPath=$this->objConfig->getModulePath();
$replacewith="";
$docRoot=$_SERVER['DOCUMENT_ROOT'];
$resourcePath=str_replace($docRoot,$replacewith,$modPath);
$codebase="http://" . $_SERVER['HTTP_HOST']."/".$resourcePath.'/wicid/resources/images/ext';

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);
$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$rightSideColumn = '<div id="error">'.$error.'</div>';
$rightSideColumn .=  '<div id="upload-win" class="x-hidden"><div class="x-window-header"></div></div><div id="toolbar"></div>';
$rightSideColumn .= '<div id ="mainContent"></div>';

$mainjs = "<script type='text/javascript'>
        Ext.onReady(function() {

            Ext.QuickTips.init();

            var dataUrl='".$dataUrl."',
                createFolderUrl='".$createFolderUrl."',
                renameFolderUrl='".$renameFolderUrl."',
                deleteFolderUrl='".$deleteFolderUrl."',
                uploadUrl = '".$uploadUrl."',
                settingsUrl = '".$settingsUrl."',
                filesUrl = '".$filesUrl."',
                modPath='".$codebase."';
            showHome(dataUrl,
            createFolderUrl,
            renameFolderUrl,
            deleteFolderUrl,
            uploadUrl,
            settingsUrl,
            filesUrl,
            modPath
            );
        });";
$mainjs .= "</script>";

echo $rightSideColumn;
echo $mainjs;
?>
