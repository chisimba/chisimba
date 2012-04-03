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
        $privateSelected = $this->objLanguage->languageText('mod_filemanager_private_selected', 'filemanager');
        $legend = $this->objLanguage->languageText('mod_filemanager_setaccess', 'filemanager');

        $objElement = new radio('access_radio');

        $objElement->addOption('private_all', $privateAllTxt . '<br/>');
        //$objElement->addOption('private_selected', $privateSelected . '<br/>');
        $objElement->addOption('public', $publicTxt . '<br/>');
        $access = $folder['access'] == NULL ? 'public' : $folder['access'];
        $objElement->setSelected($access);

        $fieldset = new fieldset();
        $fieldset->setLegend($legend);
        $fieldset->addContent($objElement->show());

        $applyButton = new button('apply', $this->objLanguage->languageText('mod_filemanager_apply', 'filemanager'));
        $applyButton->setToSubmit();

        $buttonCancel = new button('renamefoldercancel', $this->objLanguage->languageText('word_cancel'), 'document.getElementById(\'accessfolder\').style.display = \'none\'; adjustLayout();');


        $hiddeninput = new hiddeninput('id', $id);
        $form->addToForm($hiddeninput->show());

        $form->addToForm($fieldset->show());
        $form->addToForm($applyButton->show() . '&nbsp;' . $buttonCancel->show());
        return $form->show();
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
     * allows the user to donwload the selected file
     * @param <type> $filename
     */
    function downloadFile($filepath, $filename) {

        //check if user has access to the parent folder before accessing it

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