<?php

/*

 * This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the
 *  Free Software Foundation, Inc.,
 *  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 

 */

/**
 * Description of digitallibrary_class_inc
 *
 * @author davidwaf
 */
class digitallibraryutil extends object {

    function init() {
        $this->objUploadMessages = $this->getObject('uploadmessages', 'filemanager');
        $this->objFiles = $this->getObject('dbfile', 'filemanager');
        $this->objFolders = $this->getObject('dbfolder', 'filemanager');
        $this->objUpload = $this->getObject('digitallibraryupload');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->userObj = $this->getObject("user", "security");
        $this->objFileTags = $this->getObject('dbfiletags', 'filemanager');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objCleanUrl = $this->getObject("cleanurl", "filemanager");
        $this->objQuotas = $this->getObject('dbquotas', 'filemanager');
        $this->objSymlinks = $this->getObject('dbsymlinks', 'filemanager');

        $this->loadClass("htmlheading", "htmlelements");
    }

    /**
     * Method to upload files to the server
     *
     * @access private
     */
    public function upload($folderId) {
        $folder = $this->objFolders->getFolder($folderId);

        $keywords = $this->getParam("tagsfield");
        if ($keywords == null) {
            return "ERROR:No tags provided";
        }
        if ($folder != FALSE) {
            $this->objUpload->setUploadFolder($folder['folderpath']);
        } else {
            $uploadFolder = '/digitallibrary';
            $this->objUpload->setUploadFolder($uploadFolder);
        }
        $this->objUpload->enableOverwriteIncrement = TRUE;

        // Upload Files
        $results = $this->objUpload->uploadFiles();

        // Check if User entered page by typing in URL
        if ($results == FALSE) {
            return "Error: Invalid upload action";
        }

        // Check if no files were provided
        if (count($results) == 1 && array_key_exists('nofileprovided', $results)) {
            return "ERROR: No file provided";
        }
        $overwrite = $this->objUploadMessages->processOverwriteMessages();
        $index = 0;
        $reason = "";
        foreach ($results as $row) {
            if (array_key_exists("reason", $row)) {
                $reason = $row['reason'];
                $index++;
                if ($index > 0) {
                    break;
                }
            }
        }

        if ($reason == 'needsoverwrite') {
            $messages = $this->objUploadMessages->processMessageUrl($results);
            $messages['folder'] = $this->getParam('folder');
            return $this->nextAction('uploadresults', $messages);
        }

        if ($folder != null) {

            $alertVal = null;

            if (key_exists("alerts", $folder)) {
                $alertVal = $folder['alerts'];
            }

            if ($alertVal == 'y') {
                $objContext = $this->getObject('dbcontext', 'context');
                $emailUtils = $this->getObject("emailutils", "filemanager");
                $folderParts = explode('/', $folder['folderpath']);

                if ($folderParts[0] == 'context') {
                    $contextcode = $folderParts[1];
                    $context = $objContext->getContext($contextcode);

                    $emailUtils->sendFileEmailAlert($folder['id'], $contextcode, $context['title']);
                }
            }
        }
        return $folder['id']; //  $this->nextAction('home', array("folder" => $folder['id']));
    }

    /**
     * this is for viewing files that match the selected tag
     * @return type 
     */
    public function showFilesWithTag($tag) {

        if (trim($tag) == '') {

            return 'Empty tag supplied';
        }



        $files = $this->objFileTags->getFilesWithTagByFilter(" AND tbl_files.path like '/digitallibrary/%' AND tbl_files_filetags.tag='$tag' ");

        if (count($files) == 0) {
            return "No files with selected tag";
        }

        $objPreviewFolder = $this->getObject('previewfolder', "filemanager");
        $objPreviewFolder->targetModule = "digitallibrary";

        $table = $objPreviewFolder->previewContent(array(), $files, "", "dl");


        $this->appendArrayVar('headerParams', $this->getJavascriptFile('selectall.js', 'htmlelements'));

        $content = "";
        $content.= '<h1>' . $this->objLanguage->languageText('mod_filemanager_fileswithtag', 'filemanager', 'Files with tag') . ': ' . $tag . '</h1>';

        if (count($files) > 0) {
            $form = new form('deletefiles', $this->uri(array('action' => 'multidelete')));
            $form->addToForm($table);

            $button = new button('submitform', $this->objLanguage->languageText('mod_filemanager_deleteselecteditems', 'filemanager', 'Delete Selected Items'));
            $button->setToSubmit();

            $selectallbutton = new button('selectall', $this->objLanguage->languageText('phrase_selectall', 'system', 'Select All'));
            $selectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', true);");

            $deselectallbutton = new button('deselectall', $this->objLanguage->languageText('phrase_deselectall', 'system', 'Deselect all'));
            $deselectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', false);");

            $form->addToForm($button->show() . ' &nbsp; &nbsp; ' . $selectallbutton->show() . ' ' . $deselectallbutton->show());

            $content.= $form->show();
        } else {
            $content.= $table;
        }


        return $content;
    }

    /**
     * get files that match the filter
     * @return type 
     */
    public function showFilesMatchingFilter($searchQuery) {

        if (trim($searchQuery) == '') {

            return 'Empty search querry supplied';
        }



        $files = $this->objFiles->getMatchingFiles(" WHERE filename like '%$searchQuery%' or description like '%$searchQuery%'");

        if (count($files) == 0) {
            return "No files found";
        }

        $objPreviewFolder = $this->getObject('previewfolder', "filemanager");
        $objPreviewFolder->targetModule = "digitallibrary";

        $table = $objPreviewFolder->previewContent(array(), $files, "", "dl");


        $this->appendArrayVar('headerParams', $this->getJavascriptFile('selectall.js', 'htmlelements'));

        $content = "";
        $content.= '<h1>' . $this->objLanguage->languageText('mod_filemanager_fileswithtag', 'filemanager', 'Files with tag') . ': ' . $searchQuery . '</h1>';

        if (count($files) > 0) {
            $form = new form('deletefiles', $this->uri(array('action' => 'multidelete')));
            $form->addToForm($table);

            $button = new button('submitform', $this->objLanguage->languageText('mod_filemanager_deleteselecteditems', 'filemanager', 'Delete Selected Items'));
            $button->setToSubmit();

            $selectallbutton = new button('selectall', $this->objLanguage->languageText('phrase_selectall', 'system', 'Select All'));
            $selectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', true);");

            $deselectallbutton = new button('deselectall', $this->objLanguage->languageText('phrase_deselectall', 'system', 'Deselect all'));
            $deselectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', false);");

            $form->addToForm($button->show() . ' &nbsp; &nbsp; ' . $selectallbutton->show() . ' ' . $deselectallbutton->show());

            $content.= $form->show();
        } else {
            $content.= $table;
        }


        return $content;
    }

    function showFileInfo($fileId) {
        $file = $this->objFiles->getFile($fileId);
        $content = "";

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

        $fileFolder = $file['filefolder'];

        $folderId = $this->objFolders->getFolderId($fileFolder);

        $folder = $this->objFolders->getFolder($folderId);
        if ($folder['access'] == 'private_all' || $folder['access'] == 'private_selected') {
            $fileDownloadPath = $this->uri(array("action" => "downloadsecurefile", "path" => $file['path'], "filename" => $file['filename']));
        }

        if ($file['access'] == 'private_all' || $file['access'] == 'private_selected') {
            $fileDownloadPath = $this->uri(array("action" => "downloadsecurefile", "path" => $file['path'], "filename" => $file['filename']));
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

        $folderParts = explode('/', $file['filefolder']);


        $folderPermission = $this->objFolders->checkPermissionUploadFolder($folderParts[0], $folderParts[1]);

        if ($folderPermission) {
            $header->str .= $editLink->show();
        }

        $content.= $header->show();


        $content.= '<br /><p><strong>' . $this->objLanguage->languageText('word_description', 'system', 'Description') . ':</strong> <em>' . $file['description'] . '</em></p>';
        $content.= '<p><strong>' . $this->objLanguage->languageText('word_tags', 'system', 'Tags') . ':</strong> ';

        $tags = $this->objFileTags->getFileTags($file['id']);
        if (count($tags) == 0) {
            $content.= '<em>' . $this->objLanguage->languageText('phrase_notags', 'system', 'no tags') . '</em>';
        } else {
            $comma = '';
            foreach ($tags as $tag) {
                $tagLink = new link($this->uri(array('action' => 'viewbytag', 'tag' => $tag)));
                $tagLink->link = $tag;

                $content.= $comma . $tagLink->show();
                $comma = ', ';
            }
        }

        $content.= '</p>';
        $tabContent = $this->newObject('tabber', 'htmlelements');
        $tabContent->width = '90%';
        $objFilePreview = $this->getObject('filepreview', 'filemanager');
        $preview = $objFilePreview->previewFile($file['id']);
        $embedCode = "";
        if ($preview != '') {

            if ($file['category'] == 'images') {

                $preview = '<div id="filemanagerimagepreview">' . $preview . '</div>';
            }

            $objWashout = $this->getObject('washout', 'utilities');
            $embedCode = '<h2>' . $this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code') . '</h2>';

            $embedCode .= '<p>' . $this->objLanguage->languageText('mod_filemanager_embedinstructions', 'filemanager', 'Copy this code and paste it into any text box to display this file.') . '</p>';

            $embedCode .= '<form name="formtocopy">

    <input name="texttocopy" readonly="readonly" style="width:70%" type="text" value="' . $embedCode . '" />';
            $embedCode .= '
    <br /><input type="button" onclick="javascript:copyToClipboard(document.formtocopy.texttocopy);" value="Copy to Clipboard" />
    </form>';
        }

        $embedValue = htmlentities('[FILEPREVIEW id="' . $file['id'] . '" comment="' . $file['filename'] . '" /]');

        $objWashout = $this->getObject("washout", "utilities");
        $embedValue = $objWashout->parseText($embedValue);

        $previewContent = '<h2>' . $this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview') . '</h2>' . $preview;


        //$tabContent->addTab($this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview'), $previewContent);
        $tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview'), 'content' => $previewContent));

        //$tabContent->addTab($this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code'), $embedCode);
        $tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code'), 'content' => $embedCode));


        $fileInfo = $this->objLanguage->languageText('mod_filemanager_fileinfo', 'filemanager', 'File Information');

        $fileInfoContent = '<h2>' . $fileInfo . '</h2>' . $this->objFiles->getFileInfoTable($file['id']);



        if (array_key_exists('width', $file)) {


            $mediaInfo = $this->objLanguage->languageText('mod_filemanager_mediainfo', 'filemanager', 'Media Information');

            $fileInfoContent .= '<br /><h2>' . $mediaInfo . '</h2>' . $this->objFiles->getFileMediaInfoTable($file['id']);
        }


        $tabContent->addTab(array('name' => $fileInfo, 'content' => $fileInfoContent));
        $fileAccess = $this->getObject("folderaccess", "filemanager");
        $tabContent->addTab(array('name' => "Access", 'content' => $fileAccess->createFileAccessControlForm($file['id']) . '<br/>' . $fileAccess->createFileVisibilityForm($file['id'])));


        $content.= $tabContent->show();

        if ($file['category'] == 'archives' && $file['datatype'] == 'zip') {

            $folderParts = explode('/', $file['filefolder']);

            $form = new form('extractarchive', $this->uri(array('action' => 'extractarchive')));
            $form->addToForm($this->objLanguage->languageText('mod_filemanager_extractarchiveto', 'filemanager', 'Extract Archive to') . ': ' . $this->objFolders->getTree($folderParts[0], $folderParts[1], 'htmldropdown', $folderId));

            $button = new button('submitform', $this->objLanguage->languageText('mod_filemanager_extractfiles', 'filemanager', 'Extract Files'));
            $button->setToSubmit();

            $form->addToForm($button->show());

            $hiddeninput = new hiddeninput('file', $file['id']);
            $form->addToForm($hiddeninput->show());
            $content.= $form->show();
        }



        $content.= '<p><br />' . $link->show() . ' ' . $link2->show() . '</p>';
        return $content;
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $folderId Parameter description (if any) ...
     * @return object  Return description (if any) ...
     * @access public
     */
    function showCreateFolderForm($folderId) {
        $folderPath = $this->objFolders->getFolderPath($folderId);

        if ($folderPath == FALSE) {
            return '';
        }

        $folderParts = explode('/', $folderPath);

    
        $form = new form('createfolder', $this->uri(array('action' => 'createfolder')));

        $label = new label('Create a subfolder in: ', 'input_parentfolder');

        $digitalLibraryUtil=  $this->getObject("digitallibraryutil", "digitallibrary");
        $form->addToForm($label->show() . '<br/>' . $digitalLibraryUtil->getTree($folderParts[0], $folderParts[1], 'htmldropdown', $folderId));

        // $objInputMasks = $this->getObject('inputmasks', 'htmlelements');
        // echo $objInputMasks->show();

        $textinput = new textinput('foldername');
        //$textinput->setCss('text input_mask anytext');

        $label = new label('Name of Folder: ', 'input_foldername');

        $form->addToForm('<br/>' . $label->show() . '<br/>' . $textinput->show() . '&nbsp;');

        $button = new button('create', 'Create Folder');
        $button->setToSubmit();

        $form->addToForm('<br/>' . $button->show());

        return $form->show();
    }

    /**
     * Method to create a folder.
     *
     * @access private
     */
    public function createfolder() {
        $parentId = $this->getParam('parentfolder', 'ROOT');

        $foldername = $this->getParam('foldername');

        // If no folder name is given, res
        if (trim($foldername) == '') {
            return $this->nextAction('viewfolder', array('folder' => $parentId, 'error' => 'nofoldernameprovided'));
        }

        if (preg_match('/\\\|\/|\\||:|\\*|\\?|"|<|>/', $foldername)) {
            return $this->nextAction('viewfolder', array('folder' => $parentId, 'error' => 'illegalcharacters'));
        }

        // Replace spaces with underscores
        $foldername = str_replace(' ', '_', $foldername);
        $parentFolder = null;
        if ($parentId == 'ROOT') {
            $folderpath = 'users/' . $this->objUser->userId();
        } else {
            $parentFolder = $folder = $this->objFolders->getFolder($parentId);


            if ($folder == FALSE) {
                return $this->nextAction(NULL, array('error' => 'couldnotfindparentfolder'));
            }
            $folderpath = $folder['folderpath'];
        }


        $this->objMkdir = $this->getObject('mkdir', 'files');

        $path = $this->objConfig->getcontentBasePath() . '/' . $folderpath . '/' . $foldername;

        $result = $this->objMkdir->mkdirs($path);

        if ($result) {

            $folderId = $this->objFolders->indexFolder($path);

            if ($parentFolder != null) {

                $alertVal = null;

                if (key_exists("alerts", $parentFolder)) {
                    $alertVal = $folder['alerts'];
                }

                if ($alertVal == 'y') {
                    $objContext = $this->getObject('dbcontext', 'context');
                    $emailUtils = $this->getObject("emailutils", "filemanager");
                    $folderParts = explode('/', $parentFolder['folderpath']);

                    if ($folderParts[0] == 'context') {
                        $contextcode = $folderParts[1];
                        $context = $objContext->getContext($contextcode);

                        $emailUtils->sendFolderEmailAlert($folderId, $contextcode, $context['title']);
                    }
                }
            }
            return $this->nextAction('home', array('folder' => $folderId, 'message' => 'foldercreated'));
        } else {
            return $this->nextAction(NULL, array('error' => 'couldnotcreatefolder'));
        }
    }

    /**
     * Method to generate a folder tree
     * @param string $folderType Type of Folders - either users, context, workgroup, or group
     * @param string $id Either User Id of Context Code
     * @param string $treeType Type of Tree - Either dhtml or htmldropdown
     * @param string $selected Record Id of default selected node
     */
    function getTree($folderType = 'users', $id, $treeType = 'dhtml', $selected = '') {
        //Create a new tree
        $menu = new treemenu();
        $objIcon = $this->newObject('geticon', 'htmlelements');

        $icon = 'folder.gif';
        $expandedIcon = 'folder-expanded.gif';

        $baseFolder = $folderType . '/' . $id;
        $baseFolderId = $this->objFolders->getFolderId($baseFolder);

        /* if ($baseFolderId == $selected) {
          $folderText = '<strong>' . $this->getFolderType($folderType, $id) . '</strong>';
          $cssClass = 'confirm';
          } else {
          $folderText = $this->objFolders->getFolderType($folderType, $id);
          $cssClass = '';
          } */
        $cssClass = '';
        $folderText = 'Digital Library';


        if ($treeType == 'htmldropdown') {
            $allFilesNode = new treenode(array('text' => strip_tags($folderText), 'link' =>$baseFolderId));
        } else {
            $allFilesNode = new treenode(array('text' => $folderText, 'link' => $this->uri(array('action' => 'home', 'folder' => $baseFolderId)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
        }

        $refArray = array();
        $refArray[$baseFolder] = & $allFilesNode;

        $folders = $this->objFolders->getFolders($folderType, $id);

        $objFile = $this->getObject("dbfile", "filemanager");
        if (count($folders) > 0) {
            foreach ($folders as $folder) {
                $extTitle = '';

                $access = null;
                if (key_exists("access", $folder)) {
                    $access = $folder['access'];
                }
                if ($access == 'private_all') {
                    $objIcon->setIcon('info');
                    $extTitle = $objIcon->show();
                }
                $folderText = basename($folder['folderpath']) . $extTitle;
                $folderShortText = substr(basename($folder['folderpath']), 0, 60) . '...' . $extTitle;

                if ($folder['id'] == $selected) {
                    $folderText = '<strong>' . $folderText . '</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }

                if ($treeType == 'htmldropdown') {
                   
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $folder['id'], 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                } else {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $this->uri(array('action' => 'viewfolder', 'folder' => $folder['id'])), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                }


                $parent = dirname($folder['folderpath']);

                //echo $folder['folderpath'].' - '.$parent.'<br />';
                if (array_key_exists($parent, $refArray)) {
                    $refArray[dirname($folder['folderpath'])]->addItem($node);
                }

                $refArray[$folder['folderpath']] = & $node;
            }
        }

        $menu->addItem($allFilesNode);

        if ($treeType == 'htmldropdown') {
            $treeMenu = &new htmldropdown($menu, array('inputName' => 'parentfolder', 'id' => 'input_parentfolder', 'selected' => $selected));
        } else {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
            $this->setVar('pageSuppressXML', TRUE);

            $objSkin = & $this->getObject('skin', 'skin');
            $treeMenu = &new dhtml($menu, array('images' => 'skins/_common/icons/tree', 'defaultClass' => 'treeMenuDefault'));
        }



        return $treeMenu->getMenu();
    }

    /**
     * Method to view the contents of a folder
     *
     * @access private
     */
    public function viewfolder($folderId, $infoMessage, $errorMessage) {

        // TODO: Check permission to enter folder
        // Get Folder Details
        $folder = $this->objFolders->getFolder($folderId);

        $folderpath = $this->objFolders->getFolderPath($folderId);

        if ($folder == FALSE) {
            return $this->nextAction(NULL);
        }

        //var_dump($folder);

        $folderParts = explode('/', $folder['folderpath']);

        $quota = $this->objQuotas->getQuota($folder['folderpath']);
        //var_dump($quota);

        if ($folderParts[0] == 'context' && $folderParts[1] != $this->contextCode) {
            return $this->nextAction(NULL);
        }

        $folderPermission = $this->objFolders->checkPermissionUploadFolder($folderParts[0], $folderParts[1]);
        $subfolders = $this->objFolders->getSubFolders($folderId);
        $files = $this->objFiles->getFolderFiles($folder['folderpath']);
        $symlinks = $this->objSymlinks->getFolderSymlinks($folderId);
        $objPreviewFolder = $this->getObject('previewfolder', 'filemanager');
        $objPreviewFolder->targetModule = "digitallibrary";

        $objPreviewFolder->editPermission = $folderPermission;
        $table =
                $objPreviewFolder->previewContent(
                $subfolders, $files, $this->getParam("mode"), $this->getParam("name"), $symlinks, explode(
                        '____', $this->getParam('restriction')), ($this->getParam('forcerestrictions') == 'yes')
        );

        $breadcrumbs = $this->objFolders->generateBreadCrumbs($folder['folderpath']);


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
        $this->loadClass('fieldset', 'htmlelements');
        $folderPermission2 = $folderPermission;
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

        $folderContent.= '<h1>' . $folderpath . '</h1>';
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

        $uploadForm = $this->getObject("digitallibraryupload");
        $uploadForm->formaction = $this->uri(array('action' => 'upload', 'folderid' => $folderId), "digitallibrary");


        $uploadTitle = '<h3>' . $this->objLanguage->languageText('phrase_uploadfiles', 'system', 'Upload Files') . '</h3>';

        $messages = '';
        if ($errorMessage) {
            $messages = '<div class="error">' . $errorMessage . '</div>';
        }
        if ($infoMessage) {
            $messages = '<div class="info">' . $errorMessage . '</div>';
        }
        $folderContent.=$messages . $uploadTitle . $uploadForm->show();

        $tabContent = $this->newObject('tabber', 'htmlelements');
        $tabContent->width = '90%';
        $tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_folderiew', 'filemanager', 'View Folder'), 'content' => $folderContent));
        $tabContent->addTab(array('name' => $this->objLanguage->languageText('mod_filemanager_actionview', 'filemanager', 'Folder Actions'), 'content' => $folderActions));

        return $tabContent->show();
    }

    /**
     * Method to call a further action within a module.
     *
     * @param  string $action Action to perform next.
     * @param  array  $params Parameters to pass to action.
     * @return NULL
     */
    public function nextAction($action, $params = array(), $module = NULL) {
        // list($template, $_) = $this->_dispatch($action, $this->_moduleName);
        $params['action'] = $action;
        header('Location: ' . html_entity_decode($this->uri($params, $module)));
        return NULL;
    }

}

?>
