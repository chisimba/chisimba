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
 * This class is used to control accessibility of a file/ folder. This typically
 * involves making file public/ private
 *
 * @author davidwaf
 */
class folderaccess extends object {

    //secure folder 
    private $secureFolder;

    function init() {
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');

        $this->objLanguage = $this->getObject("language", "language");
        $this->sysConf = $this->getObject('dbsysconfig', 'sysconfig');
        $this->secureFolder = $this->sysConf->getValue('SECUREFODLER', 'filemanager');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objConfig = $this->getObject('altconfig', 'config');

        $this->objUser = $this->getObject('user', 'security');
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroupAdminModel = $this->getObject('groupadminmodel', 'groupadmin');
    }

    /**
     * this creates a form that allows a user to set the access control on
     * a folder/ file 
     */
    function createAccessControlForm($id) {
        $dbFolder = $this->getObject("dbfolder", "filemanager");
        $folder = $dbFolder->getFolder($id);
        $form = new form('accessform', $this->uri(array('action' => 'setfolderaccess')));
        $publicTxt = $this->objLanguage->languageText('mod_filemanager_public', 'filemanager');
        $privateAllTxt = $this->objLanguage->languageText('mod_filemanager_private_all', 'filemanager');
        $legend = $this->objLanguage->languageText('mod_filemanager_setaccess', 'filemanager');

        $objElement = new radio('access_radio');

        $objElement->addOption('private_all', $privateAllTxt . '<br/>');
        //$objElement->addOption('private_selected', $privateSelected . '<br/>');
        $objElement->addOption('public', $publicTxt . '<br/>');

        $accessVal = null;

        if (key_exists("access", $folder)) {
            $accessVal = $folder['access'];
        }
        $access = $accessVal == NULL ? 'public' : $folder['access'];
        $objElement->setSelected($access);


        $applyButton = new button('apply', $this->objLanguage->languageText('mod_filemanager_apply', 'filemanager'));
        $applyButton->setToSubmit();



        $fieldset = new fieldset();
        $fieldset->setLegend($legend);
        $fieldset->addContent($objElement->show() . '<br/>' . '<div class="warning">' . $this->objLanguage->languageText('mod_filemanager_accesswarning', 'filemanager') . '</div><br/>' . $applyButton->show());

        $hiddeninput = new hiddeninput('id', $id);
        $form->addToForm($hiddeninput->show());

        $form->addToForm($fieldset->show());
        return $form->show();
    }

    /**
     * Creates  form that allows user to enable/disable alerts on folder changes.
     * These alerts include adding/deleting/renaming and generally any changes on
     * the file/folder
     * @param type $id
     * @return type 
     */
    function createAlertsForm($id) {
        $dbFolder = $this->getObject("dbfolder", "filemanager");
        $folder = $dbFolder->getFolder($id);

        //mod_filemanager_alertchangesonfolder

        $form = new form('accessform', $this->uri(array('action' => 'setfolderalerts')));
        $legend = $this->objLanguage->languageText('mod_filemanager_alerts', 'filemanager');

        $objElement = new checkbox('alerts', $this->objLanguage->languageText('mod_filemanager_alertchangesonfolder', 'filemanager'), false);

        $alertVal = null;

        if (key_exists("alerts", $folder)) {
            $alertVal = $folder['alerts'];
        }
        $alerts = $alertVal == NULL ? false : $folder['alerts'] == 'y' ? true : false;
        $objElement->setChecked($alerts);


        $applyButton = new button('apply', $this->objLanguage->languageText('mod_filemanager_apply', 'filemanager'));
        $applyButton->setToSubmit();



        $fieldset = new fieldset();
        $fieldset->setLegend($legend);
        $fieldset->addContent($objElement->show() . '<br/>' . $applyButton->show());

        $hiddeninput = new hiddeninput('id', $id);
        $form->addToForm($hiddeninput->show());

        $form->addToForm($fieldset->show());
        return $form->show();
    }

    /**
     * this creates a form that allows a user to set the access control on
     * a folder/ file 
     */
    function createFileAccessControlForm($id) {
        $dbFile = $this->getObject("dbfile", "filemanager");
        $file = $dbFile->getFile($id);
        $form = new form('accessform', $this->uri(array('action' => 'setfileaccess')));
        $publicTxt = $this->objLanguage->languageText('mod_filemanager_public', 'filemanager');
        $privateAllTxt = $this->objLanguage->languageText('mod_filemanager_private_all', 'filemanager');
        $legend = $this->objLanguage->languageText('mod_filemanager_setaccess', 'filemanager');

        $objElement = new radio('access_radio');

        $objElement->addOption('private_all', $privateAllTxt . '<br/>');
        //$objElement->addOption('private_selected', $privateSelected . '<br/>');
        $objElement->addOption('public', $publicTxt . '<br/>');
        
        
        $access = 'public';

        if (key_exists("access", $file)) {
            $access = $file['access'];
        }
        
         $objElement->setSelected($access);


        $applyButton = new button('apply', $this->objLanguage->languageText('mod_filemanager_apply', 'filemanager'));
        $applyButton->setToSubmit();

        $fieldset = new fieldset();
        $fieldset->setLegend($legend);
        $fieldset->addContent($objElement->show() . '<br/>' . $applyButton->show());



        $hiddeninput = new hiddeninput('id', $id);
        $form->addToForm($hiddeninput->show());

        $form->addToForm($fieldset->show());
        return $form->show();
    }

    /**
     * this creates the visibility control field
     * @param type $id
     * @return type 
     */
    function createFileVisibilityForm($id) {
        $dbFile = $this->getObject("dbfile", "filemanager");
        $file = $dbFile->getFile($id);
        $form = new form('visibilityform', $this->uri(array('action' => 'setfilevisibility', 'id' => $id)));
        $visibleTxt = $this->objLanguage->languageText('mod_filemanager_visible', 'filemanager');
        $hiddenTxt = $this->objLanguage->languageText('mod_filemanager_hidden', 'filemanager');
        $legend = $this->objLanguage->languageText('mod_filemanager_visibility', 'filemanager');

        $objElement = new radio('access_radio');

        $objElement->addOption('visible', $visibleTxt . '<br/>');
        $objElement->addOption('hidden', $hiddenTxt . '<br/>');
        $access = $file['visibility'] == NULL ? 'visible' : $file['visibility'];
        $objElement->setSelected($access);

        $applyButton = new button('apply', $this->objLanguage->languageText('mod_filemanager_apply', 'filemanager'));
        $applyButton->setToSubmit();


        $fieldset = new fieldset();
        $fieldset->setLegend($legend);
        $fieldset->addContent($objElement->show() . '<br/>' . $applyButton->show());


        $hiddeninput = new hiddeninput('id', $id);
        $form->addToForm($hiddeninput->show());

        $form->addToForm($fieldset->show());

        $content = $form->show();
        $objModule = $this->getObject('modules', 'modulecatalogue');
        //See if the simple map module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('digitallibrary');
        if ($isRegistered) {
            $dlfieldset = new fieldset();
            $dlfieldset->setLegend("Link to digital library");
            $link = new link($this->uri(array("action" => "linkfromfilemanager", "fileid" => $id), "digitallibrary"));
            $link->link = "<strong>Link this file</>";
            $dlfieldset->addContent($link->show());
            $content.=$dlfieldset->show();
        }
        return $content;
    }

    /**
     * this sets the access control over a file. Depending on the access type, the
     * file location may be moved between public and secure
     * @param type $fileId
     * @param type $access 
     */
    public function setAccess($folderId, $access) {
        $dbFolder = $this->getObject("dbfolder", "filemanager");
        $dbFile = $this->getObject("dbfile", "filemanager");
        $folder = $dbFolder->getFolder($folderId);
        $files = $dbFile->getFolderFiles($folder['folderpath']);
        $dbFolder->setFolderAccess($folderId, $access);


        $contentBasePath = $this->objConfig->getcontentBasePath();
        $objMkDir = $this->getObject('mkdir', 'files');

        //for private all, we move the folder to the private section

        if ($access == 'private_all') {
            foreach ($files as $file) {
                $destFolder = $this->secureFolder . '/' . $file['filefolder'];
                $objMkDir->mkdirs($destFolder);
                @chmod($destFolder, 0777);
                $filePathFull = $contentBasePath . '/' . $file['path'];
                $destFilePathFull = $this->secureFolder . '/' . $file['path'];
                rename($filePathFull, $destFilePathFull);
            }
        }
        if ($access == 'public') {
            foreach ($files as $file) {
                $destFolder = $contentBasePath . '/' . $file['filefolder'];
                $objMkDir->mkdirs($destFolder);
                @chmod($destFolder, 0777);
                $destFilePathFull = $contentBasePath . '/' . $file['path'];
                $sourceFilePathFull = $this->secureFolder . '/' . $file['path'];
                rename($sourceFilePathFull, $destFilePathFull);
            }
        }
    }

    /**
     * sets the alerts status on the selected folder
     * @param type $folderId
     * @param type $alertStatus 
     */
    public function setFolderAlerts($folderId, $alertStatus) {
        $dbFolder = $this->getObject("dbfolder", "filemanager");
        $dbFolder->setFolderAlerts($folderId, $alertStatus);
    }

    /**
     * sets the visibility 
     * @param type $folderId
     * @param type $access 
     */
    public function setFileVisibility($fileId, $visibility) {
        $dbFile = $this->getObject("dbfile", "filemanager");
        $dbFile->setFileVisibility($fileId, $visibility);

        $file = $dbFile->getFile($fileId);
        $contentBasePath = $this->objConfig->getcontentBasePath();
        $objMkDir = $this->getObject('mkdir', 'files');


        if ($visibility == 'hidden') {

            $destFolder = $this->secureFolder . '/' . $file['filefolder'];
            $objMkDir->mkdirs($destFolder);
            @chmod($destFolder, 0777);
            $filePathFull = $contentBasePath . '/' . $file['path'];
            $destFilePathFull = $this->secureFolder . '/' . $file['path'];
            rename($filePathFull, $destFilePathFull);
        }

        if ($file['access'] == 'private_all') {
            return;
        }
        
        if ($visibility == 'visible') {
        
            $destFolder = $contentBasePath . '/' . $file['filefolder'];
            $objMkDir->mkdirs($destFolder);
            @chmod($destFolder, 0777);
            $destFilePathFull = $contentBasePath . '/' . $file['path'];
            $sourceFilePathFull = $this->secureFolder . '/' . $file['path'];
            rename($sourceFilePathFull, $destFilePathFull);
       
            
        }
    }

    /**
     * sets the access 
     * @param type $folderId
     * @param type $access 
     */
    public function setFileAccess($fileId, $access) {
        $dbFile = $this->getObject("dbfile", "filemanager");
        $dbFile->setFileAccess($fileId, $access);

        $file = $dbFile->getFile($fileId);
        $contentBasePath = $this->objConfig->getcontentBasePath();
        $objMkDir = $this->getObject('mkdir', 'files');

        //for private all, we move the folder to the private section

        if ($access == 'private_all') {

            $destFolder = $this->secureFolder . '/' . $file['filefolder'];
            $objMkDir->mkdirs($destFolder);
            @chmod($destFolder, 0777);
            $filePathFull = $contentBasePath . '/' . $file['path'];
            $destFilePathFull = $this->secureFolder . '/' . $file['path'];
            rename($filePathFull, $destFilePathFull);
        }
        if ($access == 'public') {
            $destFolder = $contentBasePath . '/' . $file['filefolder'];
            $objMkDir->mkdirs($destFolder);
            @chmod($destFolder, 0777);
            $destFilePathFull = $contentBasePath . '/' . $file['path'];
            $sourceFilePathFull = $this->secureFolder . '/' . $file['path'];
            rename($sourceFilePathFull, $destFilePathFull);
        }
    }

    /**
     * allows the user to donwload the selected file
     * @param <type> $filename
     */
    function downloadFile($filepath, $filename) {

        //check if user has access to the parent folder before accessing it

        $parts = explode('/', $filepath);
        switch ($parts[0]) {

            case 'context': // this is a context folder, so we must check if this user has access to the context first
                $contextCode = $parts[1];

                $userId = $this->objUser->userid();
                /* $groupId = $this->objGroupAdminModel->getId($contextCode);
                  if (!$this->objGroupOps->isGroupMember($groupId, $userId)) {
                  die("I'm sorry, you may not download that file.");
                  } */

                $objUserContext = $this->getObject('usercontext', 'context');
                if (!$objUserContext->isContextMember($userId, $contextCode)) {
                    die("I'm sorry, you may not download that file.");
                }
                break;
        }

        $baseDir = $this->secureFolder;

        // Detect missing filename
        if (!$filename && !$filepath)
            die("I'm sorry, you must specify a file name to download.");

        // Make sure we can't download files above the current directory location.
        if (eregi("\.\.", $filepath))
            die("I'm sorry, you may not download that file.");
        $file = str_replace("..", "", $filepath);

        // Make sure we can't download .ht control files.
        if (eregi("\.ht.+", $filepath))
            die("I'm sorry, you may not download that file.");

        // Combine the download path and the filename to create the full path to the file.
        $file = $baseDir . '/' . $filepath;

        // Test to ensure that the file exists.
        if (!file_exists($file))
            die("I'm sorry, the file doesn't seem to exist.");

        // Extract the type of file which will be sent to the browser as a header
        $type = filetype($file);

        // Get a date and timestamp
        $today = date("F j, Y, g:i a");
        $time = time();


        // Send file headers
        header("Content-type: $type");
        header("Content-Disposition: attachment;filename=" . urlencode($filename));
        header('Pragma: no-cache');
        header('Expires: 0');

        // Send the file contents.
        readfile($file);
    }

}

?>