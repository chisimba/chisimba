<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$this->appendArrayVar('headerParams', $this->getJavascriptFile('selectall.js', 'htmlelements'));

$fileDownloadPath = $this->objConfig->getcontentPath();
if (isset($file['path'])) {
    $fileDownloadPath .= $file['path'];
}
$fileDownloadPath = $this->objCleanUrl->cleanUpUrl($fileDownloadPath);
$objThumbnail = $this->getObject('thumbnails', 'filemanager');
if (!isset($selectParam)) {
    $selectParam = '';
}
if (!isset($widthHeight)) {
    $widthHeight = '';
}
$checkOpenerScript = '
        <script type="text/javascript">
        //<![CDATA[
        ' . $selectParam . '

        function selectFile(path)
        {

            if (window.opener) {

                 try
                 {
                   window.opener.CKEDITOR.tools.callFunction(1, path' . $widthHeight . ') ;

                 }
                catch(err)
                {
                   window.opener.CKEDITOR.tools.callFunction(2, path' . $widthHeight . ') ;
                }

                 window.top.close() ;
                 window.top.opener.focus() ;
            }
        }

 function selectFileWindow(name,filename, fileid)
        {
            if (window.opener) {

                //alert(fileName[id]);
                window.opener.document.getElementById("input_selectfile_"+name).value = filename;
                window.opener.document.getElementById("hidden_"+name).value = fileid;

                window.close();
                window.opener.focus();
            } else {
                window.parent.document.getElementById("input_selectfile_"+name).value = filename;
                window.parent.document.getElementById("hidden_"+name).value =fileid;
                window.parent.hidePopWin();
            }
        }

 function selectImageWindow(name,path, filename,fileid)
        {
            if (window.opener) {

                window.opener.document.getElementById("imagepreview_"+name).src = path;
                window.opener.document.getElementById("hidden_"+name).value = fileid;
                window.close();
                window.opener.focus();
            } else {
                window.parent.document.getElementById("selectfile_"+name).value = filename;
                window.parent.document.getElementById("hidden_"+name).value =fileid;
                window.parent.hidePopWin();
            }
        }
        //]]>
        </script>
                ';

$this->appendArrayVar('headerParams', $checkOpenerScript);
$this->loadClass('fieldset', 'htmlelements');
if ($folderPermission2) {
    $fieldset = new fieldset();

    $fieldset->setLegend($this->objLanguage->languageText('mod_filemanager_createafolder', 'filemanager', 'Create a Folder'));
    $fieldset->addContent($this->objFolders->showCreateFolderForm($folderId));
    echo $fieldset->show();
}
$accessLink = "";
if ($folder['folderlevel'] == 2) {
    $icon = '';
    $linkRename = '';
    $folderpath = $breadcrumbs;
} else if ($folderPermission) {
    $icon = $objIcon->getDeleteIconWithConfirm($folderId, array('action' => 'deletefolder', 'id' => $folderId), 'filemanager', $this->objLanguage->languageText('mod_filemanager_confirmdeletefolder', 'filemanager', 'Are you sure wou want to remove this folder?'));
    //$objLinkRename = new link($this->uri(array('action' => 'renamefolder', 'folder'=>$folderId)));
    //$objLinkRename->link = $this->objLanguage->languageText('mod_filemanager_rename', 'filemanager');
    $linkRename = '<span id="renameButton" style="cursor: pointer; text-decoration: underline">' . $this->objLanguage->languageText('mod_filemanager_rename', 'filemanager') . '</span><script type="text/javascript">
document.getElementById(\'renameButton\').onclick = function() {
    document.getElementById(\'renamefolder\').style.display = \'inline\';
    adjustLayout();
};
</script>&nbsp;|&nbsp;';

    $accessLink = '<span id="accessButton" style="cursor: pointer; text-decoration: underline">' . $this->objLanguage->languageText('mod_filemanager_access', 'filemanager') .
            '</span>
<script type="text/javascript">
    document.getElementById(\'accessButton\').onclick = function() {
    document.getElementById(\'accessfolder\').style.display = \'inline\';
    adjustLayout();
};
</script>&nbsp;|&nbsp;';
//$objLinkRename->show();
} else {
    $icon = '';
    $linkRename = '&nbsp;|&nbsp;';
    $accessLink = '&nbsp;|&nbsp;';
}

$folderContent = "";

switch ($this->getParam('message')) {
    default:
        break;
    case 'foldercreated':
        $folderContent.= '<span class="confirm">' . $this->objLanguage->languageText('mod_filemanager_folderhasbeencreated', 'filemanager', 'Folder has been created') . ' </span>';
        break;
    case 'filesdeleted':
        $folderContent.= '<span class="confirm">' . $this->getParam('numfiles') . ' ' . $this->objLanguage->languageText('mod_filemanager_fileshavebeendeleted', 'filemanager', 'File(s) have been deleted') . ' </span>';
        break;
    case 'folderdeleted':
        $folderContent.= '<span class="confirm"><strong>' . $this->getParam('ref') . '</strong> ' . $this->objLanguage->languageText('mod_filemanager_folderhasbeendeleted', 'filemanager', 'folder has been deleted') . ' </span>';
        break;
}

switch ($this->getParam('error')) {
    default:
        break;
    case 'nofoldernameprovided':
        $folderContent.= '<span class="error">' . $this->objLanguage->languageText('mod_filemanager_folderwasnotcreatednoname', 'filemanager', 'Folder was not created. No name provided') . '</span>';
        break;
    case 'illegalcharacters':
        $folderContent.= '<span class="error">' . $this->objLanguage->languageText('mod_filemanager_folderwasnotcreatedillegalchars', 'filemanager', 'Folder was not created. Folders cannot contain any of the following characters') . ': \ / : * ? &quot; &lt; &gt; |</span>';
        break;
}

echo '<h1>' . $folderpath . '</h1>';
$folderActions = ""; // $fieldset->show(); //'<table border="0"><tr><td valign="baseline"></td><td valign="baseline">' . $linkRename . $accessLink . $icon . '</td></tr></table>';
if ($folder['folderlevel'] != 2 && $folderPermission) {
    $form = new form('formrenamefolder', $this->uri(array('action' => 'renamefolder')));
    $objInputFolder = new hiddeninput('folder', $folderId);
    $form->addToForm($objInputFolder->show());
    $label = new label($this->objLanguage->languageText('mod_filemanager_nameoffolder', 'filemanager') . ': ', 'input_foldername');
    $textinput = new textinput('foldername', $folderpath);
    $form->addToForm($label->show() . $textinput->show());
    $buttonSubmit = new button('renamefoldersubmit', $this->objLanguage->languageText('mod_filemanager_renamefolder', 'filemanager'));
    $buttonSubmit->setToSubmit();
    $form->addToForm('&nbsp;' . $buttonSubmit->show() . '<br/><div class="warning">' . $this->objLanguage->languageText('mod_filemanager_renamewarning', 'filemanager') . '</div>'); // . '&nbsp;' . $buttonCancel->show());


    $fieldset = new fieldset();
    $fieldset->setLegend($this->objLanguage->languageText('mod_filemanager_renamefolder', 'filemanager'));
    //$folderId
    $fieldset->addContent($form->show());

    $folderActions.= '<span id="renamefolder" style="display: xnone;">' . $fieldset->show() . '<br /></span>';
    $objAccess = $this->getObject("folderaccess", "filemanager");
    $accessContent = $objAccess->createAccessControlForm($folder['id']);
    $folderActions.= '<span id="accessfolder" >' . $accessContent . '<br /></span>';

    $alertContent = $objAccess->createAlertsForm($folder['id']);
    $folderActions.= '<span id="alertsfolder" >' . $alertContent . '<br /></span>';


    $fieldset = new fieldset();
    $fieldset->setLegend($this->objLanguage->languageText('mod_filemanager_deletefolder', 'filemanager', 'Delete Folder'));
    $fieldset->addContent('<br/><div class="warning">' . $this->objLanguage->languageText('mod_filemanager_deletewarning', 'filemanager') . '</div><br/>' . $icon);
    $folderActions.=$fieldset->show();
}

if ((count($files) > 0 || count($subfolders) > 0 || count($symlinks) > 0) && $folderPermission) {
    $form = new form('movedeletefiles', $this->uri(array('action' => 'multimovedelete')));
    $form->addToForm($table);

    $folderPath_ = $this->objFolders->getFolderPath($folderId);
    if ($folderPath_ !== FALSE) {
        $folderParts = explode('/', $folderPath_);
        $folderTree = $this->objFolders->getTree($folderParts[0], $folderParts[1], 'htmldropdown', $folderId);
        $objButtonMove = new button('movefiles', $this->objLanguage->languageText('mod_filemanager_moveselecteditems', 'filemanager'));
        $objButtonMove->setToSubmit();
        $move = $this->objLanguage->languageText('mod_filemanager_moveto', 'filemanager') . ':&nbsp;' . $folderTree . '&nbsp;' . $objButtonMove->show() . '&nbsp;';
    } else {
        $move = '';
    }

    $button = new button('submitform', $this->objLanguage->languageText('mod_filemanager_deleteselecteditems', 'filemanager', 'Delete Selected Items'));
    $button->setToSubmit();

    // Set Ability to create symlinks to nothing - default no ability
    $symlink = '';

    // Check ability to create symlinks
    if ($this->contextCode != '' && $this->getParam('context') != 'no' && substr($folder['folderpath'], 0, 7) != 'context') {

        $folderPermission = $this->objFolders->checkPermissionUploadFolder('context', $this->contextCode);

        if ($folderPermission) {
            $symlinkButton = new button('symlinkcontext', $this->objLanguage->code2Txt('mod_filemanager_attachtocontext', 'filemanager', NULL, 'Attach to [-context-]'));
            $symlinkButton->setToSubmit();

            $symlink = '&nbsp;' . $symlinkButton->show();
        }
    }

    $selectallbutton = new button('selectall', $this->objLanguage->languageText('phrase_selectall', 'system', 'Select All'));
    $selectallbutton->setOnClick("javascript:SetAllCheckBoxes('movedeletefiles', 'files[]', true);");

    $deselectallbutton = new button('deselectall', $this->objLanguage->languageText('phrase_deselectall', 'system', 'Deselect all'));
    $deselectallbutton->setOnClick("javascript:SetAllCheckBoxes('movedeletefiles', 'files[]', false);");

    $form->addToForm($move . $button->show() . $symlink . '&nbsp;' . $selectallbutton->show() . '&nbsp;' . $deselectallbutton->show());

    $folderInput = new hiddeninput('folder', $folderId);
    $form->addToForm($folderInput->show());

    $folderContent.= $form->show();
} else {
    $folderContent.= $table;
}


if ($folderPermission2) {

    $folderContent.= '<h3>' . $this->objLanguage->languageText('phrase_uploadfiles', 'system', 'Upload Files') . '</h3>';

    if ($quota['quotausage'] >= $quota['quota']) {
        $folderContent.= '<p class="warning">' . $this->objLanguage->languageText('mod_filemanager_quotaexceeded', 'filemanager', 'Allocated Quota Exceeded. First delete some files and then try to upload again.') . '</p>';
    } else {
        $folderContent.= $this->objUpload->show($folderId, ($quota['quota'] - $quota['quotausage']));
    }
}

$tabContent = $this->newObject('tabber', 'htmlelements');
$tabContent->width = '90%';
$tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_folderiew', 'filemanager', 'View Folder'), 'content' => $folderContent));
$tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_actionview', 'filemanager', 'Folder Actions'), 'content' => $folderActions));

echo $tabContent->show();
?>