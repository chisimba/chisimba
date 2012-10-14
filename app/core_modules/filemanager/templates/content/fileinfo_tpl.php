<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('formatfilesize', 'files');
$this->loadClass('htmlheading', 'htmlelements');
echo '<div id="filemanagerbreadcrumbs">' . $fileBreadrumbs . '</div>';

// Set for Layout Template
$this->setVar('folderId', $folderId);
$objIcon = $this->newObject('geticon', 'htmlelements');
$objFileIcons = $this->getObject('fileicons', 'files');
$objFileIcons->size = 'large';
$objIcon->setIcon('edit');

$editLink = new link($this->uri(array('action' => 'editfiledetails', 'id' => $file['id'])));
$editLink->link = $objIcon->show();

$header = new htmlheading();
$header->type = 1;
$header->str = $objFileIcons->getFileIcon($file['filename']) . ' ' . str_replace('_', ' ', htmlentities($file['filename']));


$fileDownloadPath = $this->objConfig->getcontentPath() . $file['path'];
$fileDownloadPath = $this->objCleanUrl->cleanUpUrl($fileDownloadPath);

$folder = $this->objFolders->getFolder($folderId);
if ($folder['access'] == 'private_all' || $folder['access'] == 'private_selected') {
    $fileDownloadPath = $this->uri(array("action" => "downloadsecurefile", "path" => $file['path'], "filename" => $file['filename']));
}


$accessKeyExists = false;

if (key_exists("access", $file)) {
    $accessKeyExists = true;
}
$visibilityKeyExists = false;

if (key_exists("visibility", $file)) {
    $visibilityKeyExists = true;
}

if ($accessKeyExists) {
    if ($file['access'] == 'private_all' || $file['access'] == 'private_selected') {
        $fileDownloadPath = $this->uri(array("action" => "downloadsecurefile", "path" => $file['path'], "filename" => $file['filename']));
    }
}
if ($visibilityKeyExists) {
    if ($file['visibility'] == 'hidden') {
        $fileDownloadPath = $this->uri(array("action" => "downloadsecurefile", "path" => $file['path'], "filename" => $file['filename']));
    }
}

$objIcon->setIcon('download');
$link = new link($fileDownloadPath);
$link2 = new link($fileDownloadPath);

$link->link = $objIcon->show();
$link2->link = $this->objLanguage->languageText('phrase_downloadfile', 'filemanager', 'Download File');
$copyToClipBoardJS = '
    
  <script type="text/javascript">
  function copyToClipboard(text) {
   if (window.clipboardData) {
      window.clipboardData.setData("Text",text);
  }
}
         </script>
';
$this->appendArrayVar('headerParams', $copyToClipBoardJS);
$header->str .= ' ' . $link->show() . ' ';

if ($mode == 'selectfilewindow' || $mode == 'selectimagewindow' || $mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {
    if (count($restrictions) == 0) {
        $header->str .= ' (<a href="javascript:selectFile();">' . $this->objLanguage->languageText('mod_filemanager_selectfile', 'filemanager', 'Select File') . '</a>) ';
    } else if (in_array(strtolower($file['datatype']), $restrictions)) {
        $header->str .= ' (<a href="javascript:selectFile();">' . $this->objLanguage->languageText('mod_filemanager_selectfile', 'filemanager', 'Select File') . '</a>) ';
    }

    if ($mode == 'fckimage' || $mode == 'fckflash') {
        if (isset($file['width']) && isset($file['height'])) {
            $widthHeight = ', ' . $file['width'] . ', ' . $file['height'];
        } else {
            $widthHeight = '';
        }
    } else {
        $widthHeight = '';
    }

    if ($mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {

        $checkOpenerScript = '
        <script type="text/javascript">
        //<![CDATA[
        ' . $selectParam . '

        function selectFile()
        {
            if (window.opener) {
                try
                 {
                     window.opener.CKEDITOR.tools.callFunction(1, "' . htmlspecialchars_decode($this->uri(array('action' => 'file', 'id' => $file['id'], 'filename' => $file['filename'], 'type' => '.' . $file['datatype']), 'filemanager', '', TRUE, FALSE, TRUE)) . '"' . $widthHeight . ') ;
            
                 }
                catch(err)
                {
                     window.opener.CKEDITOR.tools.callFunction(2, "' . htmlspecialchars_decode($this->uri(array('action' => 'file', 'id' => $file['id'], 'filename' => $file['filename'], 'type' => '.' . $file['datatype']), 'filemanager', '', TRUE, FALSE, TRUE)) . '"' . $widthHeight . ') ;
             
                }

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
                window.opener.document.getElementById("input_selectfile_' . $this->getParam('name') . '").value = "' . htmlspecialchars_decode($file['filename']) . '";
                window.opener.document.getElementById("hidden_' . $this->getParam('name') . '").value = "' . htmlspecialchars_decode($file['id']) . '";
            
                window.close();
                window.opener.focus();
            } else {
                window.parent.document.getElementById("input_selectfile_' . $this->getParam('name') . '").value = "' . htmlspecialchars_decode($file['filename']) . '";
                window.parent.document.getElementById("hidden_' . $this->getParam('name') . '").value = "' . htmlspecialchars_decode($file['id']) . '";
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
                window.opener.document.getElementById("imagepreview_' . $this->getParam('name') . '").src = "' . $objThumbnails->getThumbnail($file['id'], $file['filename'], $file['path']) . '";
                window.opener.document.getElementById("hidden_' . $this->getParam('name') . '").value = "' . htmlspecialchars_decode($file['id']) . '";
                window.close();
                window.opener.focus();
            } else {
                window.parent.document.getElementById("selectfile_' . $this->getParam('name') . '").value = "' . htmlspecialchars_decode($file['filename']) . '";
                window.parent.document.getElementById("hidden_' . $this->getParam('name') . '").value = "' . htmlspecialchars_decode($file['id']) . '";
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


echo '<br /><p><strong>' . $this->objLanguage->languageText('word_description', 'system', 'Description') . ':</strong> <em>' . $file['filedescription'] . '</em></p>';
echo '<p><strong>' . $this->objLanguage->languageText('word_tags', 'system', 'Tags') . ':</strong> ';

if (count($tags) == 0) {
    echo '<em>' . $this->objLanguage->languageText('phrase_notags', 'system', 'no tags') . '</em>';
} else {
    $comma = '';
    foreach ($tags as $tag) {
        $tagLink = new link($this->uri(array('action' => 'viewbytag', 'tag' => $tag)));
        $tagLink->link = $tag;

        echo $comma . $tagLink->show();
        $comma = ', ';
    }
}

echo '</p>';

//$tabContent = $this->newObject('tabcontent', 'htmlelements');
//$tabContent = $this->newObject('jquerytabs', 'htmlelements');
$tabContent = $this->newObject('tabber', 'htmlelements');
$tabContent->width = '90%';

if ($preview != '') {

    if ($file['category'] == 'images') {
        // $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery.imagefit_0.2.js', 'jquery'));
        // $this->appendArrayVar('bodyOnLoad', "jQuery('#filemanagerimagepreview').imagefit();");

        $preview = '<div id="filemanagerimagepreview">' . $preview . '</div>';
    }

    $objWashout = $this->getObject('washout', 'utilities');

    $preview = $objWashout->parseText($embedValue);

    $previewContent = '<h2>' . $this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview') . '</h2>' . $preview;


    //$tabContent->addTab($this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview'), $previewContent);
    $tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview'), 'content' => $previewContent));

    //$tabContent->addTab($this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code'), $embedCode);
    $tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code'), 'content' => $embedCode));
}

$fileInfo = $this->objLanguage->languageText('mod_filemanager_fileinfo', 'filemanager', 'File Information');

$fileInfoContent = '<h2>' . $fileInfo . '</h2>' . $this->objFiles->getFileInfoTable($file['id']);



if (array_key_exists('width', $file)) {


    $mediaInfo = $this->objLanguage->languageText('mod_filemanager_mediainfo', 'filemanager', 'Media Information');

    $fileInfoContent .= '<br /><h2>' . $mediaInfo . '</h2>' . $this->objFiles->getFileMediaInfoTable($file['id']);
}


$tabContent->addTab(array('name' => $fileInfo, 'content' => $fileInfoContent));
if ($folderPermission) {
    $fileAccess = $this->getObject("folderaccess", "filemanager");
    $tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_access', 'filemanager', 'Access'), 'content' => $fileAccess->createFileAccessControlForm($file['id']) . '<br/>' . $fileAccess->createFileVisibilityForm($file['id'])));
}

echo $tabContent->show();

if ($file['category'] == 'archives' && $file['datatype'] == 'zip') {

    $folderParts = explode('/', $file['filefolder']);
    //getTree($folderType='users', $id, $treeType='dhtml', $selected='')

    $form = new form('extractarchive', $this->uri(array('action' => 'extractarchive')));
    $form->addToForm($this->objLanguage->languageText('mod_filemanager_extractarchiveto', 'filemanager', 'Extract Archive to') . ': ' . $this->objFolders->getTree($folderParts[0], $folderParts[1], 'htmldropdown', $folderId));

    $button = new button('submitform', $this->objLanguage->languageText('mod_filemanager_extractfiles', 'filemanager', 'Extract Files'));
    $button->setToSubmit();

    $form->addToForm($button->show());

    $hiddeninput = new hiddeninput('file', $file['id']);
    $form->addToForm($hiddeninput->show());
    echo $form->show();
}

// echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_filehistory', 'filemanager', 'File History').'</h3>';
// echo $this->objFiles->getFileHistory($file['id']);




echo '<p><br />' . $link->show() . ' ' . $link2->show() . '</p>';
?>
