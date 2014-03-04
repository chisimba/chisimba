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
 * This is the entry point of the digitallibrary module
 *
 * @author davidwaf
 */
class digitallibrary extends controller {

    function init() {
        $this->objUploadMessages = $this->getObject('uploadmessages', 'filemanager');
        $this->objFiles = $this->getObject('dbfile', 'filemanager');
        $this->objFolders = $this->getObject('dbfolder', 'filemanager');
        $this->objUpload = $this->getObject('upload', 'filemanager');

        $folderpath = 'digitallibrary/root';

        $folderId = $this->objFolders->getFolderId($folderpath);
        if ($folderId == FALSE) {
            $folderId = $this->objFolders->indexFolder($folderpath);
        }
    }

    /**
     * Standard Dispatch Function for Controller
     *
     * @access public
     * @param string $action Action being run
     * @return string Filename of template to be displayed
     */
    public function dispatch($action) {

        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        $this->setLayoutTemplate('layout_tpl.php');
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Default action of the module 
     */
    function __home() {
        $errorMessage = $this->getParam("errormessage");
        $folderId = $this->getParam("folder");

        if ($folderId == '') {
            $folderpath = 'digitallibrary/root';
            $folderId = $this->objFolders->getFolderId($folderpath);
        }
        $infoMessage = $this->getParam("message");
        $data = $folderId . '|' . $infoMessage . '|' . $errorMessage;
        $this->setVarByRef("data", $data);
        $this->setVarByRef("folderId", $folderId);
        return "home_tpl.php";
    }

    /**
     * Method to upload files to the server
     *
     * @access private
     */
    private function __upload() {
        $digitalLibrary = $this->getObject("digitallibraryutil", "digitallibrary");
        $folderId = $this->getParam("folderid");
        $result = $digitalLibrary->upload($folderId);
        $errorMessage = "";
        if ($this->startsWith($result, "ERROR")) {
            $messages = explode(":", $result);
            $errorMessage = $messages[1];
        }
        return $this->nextAction('viewfolder', array("folder" => $folderId, "errormessage" => $errorMessage));
    }

    private function __createfolder() {
        $digitalLibrary = $this->getObject("digitallibraryutil", "digitallibrary");
        return $digitalLibrary->createFolder();
    }

    /**
     * this builds a form with files that match the selected tag
     * @return string 
     */
    private function __viewbytag() {
        $tag = $this->getParam('tag');
        $this->setVarByRef("tag", $tag);
        return 'showfileswithtags_tpl.php';
    }

    /**
     * displays info on a selected file
     * @return string 
     */
    private function __fileinfo() {
        $fileId = $this->getParam("id");
        $file = $this->objFiles->getFile($fileId);
        $fileFolder = $file['filefolder'];
        $folderId = $this->objFolders->getFolderId($fileFolder);
        $this->setVarByRef("folderId", $folderId);
        $this->setVarByRef("fileid", $fileId);
        return "fileinfo_tpl.php";
    }

    /**
     * process the search requewst
     * @return string 
     */
    private function __search() {
        $searchParam = $this->getParam('filequery');

        $this->setVarByRef("searchParam", $searchParam);
        return 'search_tpl.php';
    }

    private function __viewfolder() {
        $id = $this->getParam('folder');
        $errorMessage = $this->getParam("errormessage");
        $infoMessage = $this->getParam("messag  e");
        $data = $id . '|' . $infoMessage . '|' . $errorMessage;
        $this->setVarByRef("data", $data);
        $this->setVarByRef("folderId", $id);
        return "showfolder_tpl.php";
    }

    private function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        $start = $length * -1; //negative
        return (substr($haystack, $start) === $needle);
    }

}

?>
