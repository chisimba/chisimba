<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('formatfilesize', 'files');
$this->loadClass('htmlheading', 'htmlelements');

echo '<div id="filemanagerbreadcrumbs">'.$fileBreadrumbs.'</div>';



// Set for Layout Template
$this->setVar('folderId', $folderId);

$objIcon = $this->newObject('geticon', 'htmlelements');



$objFileIcons = $this->getObject('fileicons', 'files');
$objFileIcons->size = 'large';

$objIcon->setIcon('edit');

$editLink = new link ($this->uri(array('action'=>'editfiledetails', 'id'=>$file['id'])));
$editLink->link = $objIcon->show();

$header = new htmlheading();
$header->type = 1;
$header->str = $objFileIcons->getFileIcon($file['filename']).' '.str_replace('_', ' ', htmlentities($file['filename']));

$fileDownloadPath = $this->objConfig->getcontentPath().$file['path'];
$fileDownloadPath = $this->objCleanUrl->cleanUpUrl($fileDownloadPath);

$objIcon->setIcon('download');
$link = new link ($fileDownloadPath);
$link2 = new link ($fileDownloadPath);

$link->link = $objIcon->show();
$link2->link = $this->objLanguage->languageText('phrase_downloadfile', 'filemanager', 'Download File');

$header->str .= ' '.$link->show().' ';

if ($mode == 'selectfilewindow' || $mode == 'selectimagewindow' || $mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {
    if (count($restrictions) == 0) {
        $header->str .= ' (<a href="javascript:selectFile();">'.$this->objLanguage->languageText('mod_filemanager_selectfile', 'filemanager', 'Select File').'</a>) ';
    } else if (in_array(strtolower($file['datatype']), $restrictions)) {
        $header->str .= ' (<a href="javascript:selectFile();">'.$this->objLanguage->languageText('mod_filemanager_selectfile', 'filemanager', 'Select File').'</a>) ';
    } 
    
    if ($mode == 'fckimage' || $mode == 'fckflash') {
        if (isset($file['width']) && isset($file['height'])) {
            $widthHeight = ', '.$file['width'].', '.$file['height'];
        }else {
            $widthHeight = '';
        }
    } else {
        $widthHeight = '';
    }
    
    if ($mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {
        
        //var_dump($file);
        
        $checkOpenerScript = '
        <script type="text/javascript">
        //<![CDATA[
        function selectFile()
        {
            if (window.opener) {
                window.top.opener.SetUrl("'.htmlspecialchars_decode($this->uri(array('action'=>'file', 'id'=>$file['id'], 'filename'=>$file['filename'], 'type'=>'.'.$file['datatype']), 'filemanager', '', TRUE, FALSE, TRUE)).'"'.$widthHeight.') ;
                window.top.close() ;
                window.top.opener.focus() ;
            }
        }
        //]]>
        </script>
                ';
        
        $this->appendArrayVar('headerParams', $checkOpenerScript);
        
    } else if ($mode == 'selectfilewindow') {
        $checkOpenerScript = '
        <script type="text/javascript">
        function selectFile()
        {
            if (window.opener) {
                
                //alert(fileName[id]);
                window.opener.document.getElementById("input_selectfile_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['filename']).'";
                window.opener.document.getElementById("hidden_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['id']).'";
            
                window.close();
                window.opener.focus();
            } else {
                window.parent.document.getElementById("input_selectfile_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['filename']).'";
                window.parent.document.getElementById("hidden_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['id']).'";
                window.parent.hidePopWin();
            }
        }
        </script>
                ';
        
        $this->appendArrayVar('headerParams', $checkOpenerScript);
        
    } else if ($mode == 'selectimagewindow') {
        
        $objThumbnails = $this->getObject('thumbnails');
        
        $checkOpenerScript = '
        <script type="text/javascript">
        function selectFile()
        {
            if (window.opener) {
                window.opener.document.getElementById("imagepreview_'.$this->getParam('name').'").src = "'.$objThumbnails->getThumbnail($file['id'], $file['filename'], $file['path']).'";
                //window.opener.document.getElementById("selectfile_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['filename']).'";
                window.opener.document.getElementById("hidden_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['id']).'";
                window.close();
                window.opener.focus();
            } else {
                window.parent.document.getElementById("selectfile_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['filename']).'";
                window.parent.document.getElementById("hidden_'.$this->getParam('name').'").value = "'.htmlspecialchars_decode($file['id']).'";
                window.parent.hidePopWin();
            }
        }
        </script>
                ';
        
        $this->appendArrayVar('headerParams', $checkOpenerScript);
    }
}

if ($folderPermission) {
    $header->str .= $editLink->show();
}

echo $header->show();




echo '<br /><p><strong>'.$this->objLanguage->languageText('word_description', 'system', 'Description').':</strong> <em>'.$file['filedescription'].'</em></p>';

echo '<p><strong>'.$this->objLanguage->languageText('word_tags', 'system', 'Tags').':</strong> ';

if (count($tags) == 0) {
    echo '<em>'.$this->objLanguage->languageText('phrase_notags', 'system', 'no tags').'</em>';
} else {
    $comma = '';
    foreach ($tags as $tag)
    {
        $tagLink = new link ($this->uri(array('action'=>'viewbytag', 'tag'=>$tag)));
        $tagLink->link = $tag;

        echo $comma.$tagLink->show();
        $comma = ', ';
    }
}

echo '</p>';

$tabContent = $this->newObject('tabcontent', 'htmlelements');
$tabContent->width = '90%';
if ($preview != '') {
    
    
    
    
    
    if ($file['category'] == 'images') {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery/jquery.imagefit_0.2.js', 'htmlelements'));
        $this->appendArrayVar('bodyOnLoad', "jQuery('#filemanagerimagepreview').imagefit();");
        
        $preview = '<div id="filemanagerimagepreview">'.$preview.'</div>';
    }
    
    $previewContent = '<h2>'.$this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview').'</h2>'.$preview;
    
    
    $tabContent->addTab($this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview'), $previewContent);
    $tabContent->addTab($this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code'), $embedCode);
}

$fileInfo = $this->objLanguage->languageText('mod_filemanager_fileinfo', 'filemanager', 'File Information');

$fileInfoContent = '<h2>'.$fileInfo.'</h2>'.$this->objFiles->getFileInfoTable($file['id']);



if (array_key_exists('width', $file)) {
    
    
    $mediaInfo = $this->objLanguage->languageText('mod_filemanager_mediainfo', 'filemanager', 'Media Information');
    
    $fileInfoContent .= '<br /><h2>'.$mediaInfo.'</h2>'.$this->objFiles->getFileMediaInfoTable($file['id']);
}

$tabContent->addTab($fileInfo, $fileInfoContent);


echo $tabContent->show();
















if ($file['category'] == 'archives' && $file['datatype'] == 'zip') {
    
    $folderParts = explode('/', $file['filefolder']);
    //getTree($folderType='users', $id, $treeType='dhtml', $selected='')
    
    $form = new form ('extractarchive', $this->uri(array('action'=>'extractarchive')));
    $form->addToForm($this->objLanguage->languageText('mod_filemanager_extractarchiveto', 'filemanager', 'Extract Archive to').': '.$this->objFolders->getTree($folderParts[0], $folderParts[1], 'htmldropdown', $folderId));

    $button = new button ('submitform', $this->objLanguage->languageText('mod_filemanager_extractfiles', 'filemanager', 'Extract Files'));
    $button->setToSubmit();

    $form->addToForm($button->show());

    $hiddeninput = new hiddeninput ('file', $file['id']);
    $form->addToForm($hiddeninput->show());
    echo $form->show();
}

// echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_filehistory', 'filemanager', 'File History').'</h3>';
// echo $this->objFiles->getFileHistory($file['id']);




echo '<p><br />'.$link->show().' '.$link2->show().'</p>';


?>