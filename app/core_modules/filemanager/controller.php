<?php
/**
 * 
 * File Manager
 * 
 * File Manager
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   helloforms
 * @author    Tohir Solomons tsolomons@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
 
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 * 
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *         
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* 
* Controller class for Chisimba for the module filemanager2
*
* @author Tohir Solomons
* @package filemanager2
*
*/
class filemanager extends controller
{
    
    /**
    * 
    * @var string $objConfig String object property for holding the 
    * configuration object
    * @access public;
    * 
    */
    public $objConfig;
    
    /**
    * 
    * @var string $objLanguage String object property for holding the 
    * language object
    * @access public
    * 
    */
    public $objLanguage;
    /**
    *
    * @var string $objLog String object property for holding the 
    * logger object for logging user activity
    * @access public
    * 
    */
    public $objLog;
    
    public $debug = FALSE;

    /**
    * 
    * Intialiser for the filemanager2 controller
    * @access public
    * 
    */
    public function init()
    {
        // File Manager Classes
        $this->objFiles = $this->getObject('dbfile', 'filemanager');
        $this->objFolders = $this->getObject('dbfolder', 'filemanager');
        $this->objFileTags = $this->getObject('dbfiletags', 'filemanager');
        $this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $this->objUpload = $this->getObject('upload', 'filemanager');
        $this->objFilePreview = $this->getObject('filepreview', 'filemanager');
        $this->objQuotas = $this->getObject('dbquotas', 'filemanager');
        $this->objSymlinks = $this->getObject('dbsymlinks', 'filemanager');
        
        $this->objUploadMessages = $this->getObject('uploadmessages', 'filemanager');
        
        // Other Classes
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objMenuTools = $this->getObject('tools', 'toolbar');
        $this->loadClass('link', 'htmlelements');
        
        
        $this->userId = $this->objUser->userId();
        
        if ($this->userId != '') {
            // Setup User Folder
            $folderpath = 'users/'.$this->userId;
            
            $folderId = $this->objFolders->getFolderId($folderpath);
            
            
            
            if ($folderId == FALSE) {
                $objIndexFileProcessor = $this->getObject('indexfileprocessor');
                $list = $objIndexFileProcessor->indexUserFiles($this->objUser->userId());
            }
        }
        
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        if ($this->contextCode != '') {
            $folderpath = 'context/'.$this->contextCode;
            
            $folderId = $this->objFolders->getFolderId($folderpath);
            if ($folderId == FALSE) {
                $objIndexFileProcessor = $this->getObject('indexfileprocessor');
                $list = $objIndexFileProcessor->indexFiles('context', $this->contextCode);
            }
        }
    }
    
    /**
     * Override the login object in the parent class
     *
     * @param void
     * @return bool
     * @access public
     */
    public function requiresLogin($action)
    {
        if ($action == 'file') {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     * Method to override the nextAction function to include automatic inclusion
     * of mode and restriction
     *
     * @param  string $action Action to perform next.
     * @param  array  $params Parameters to pass to action.
     * @return NULL
     */
    public function nextAction($action, $params = array(), $module='filemanager')
    {
        // If parameters is NULL, convert to array
        if ($params == NULL) {
            $params = array();
        }
        
        // Add Param for Mode
        if (is_array($params) && !array_key_exists('mode', $params) && $this->getParam('mode') != '') {
            $params['mode'] = $this->getParam('mode');
        }
        
        // Add Param for Restriction
        if (is_array($params) && !array_key_exists('restriction', $params) && $this->getParam('restriction') != '') {
            $params['restriction'] = $this->getParam('restriction');
        }
        
        // Add Param for Name - File/Image Select
        if (is_array($params) && !array_key_exists('name', $params) && $this->getParam('name') != '') {
            $params['name'] = $this->getParam('name');
        }
        
        if (is_array($params) && !array_key_exists('context', $params) && $this->getParam('context') != '') {
            $params['context'] = $this->getParam('context');
        }
        
        if (is_array($params) && !array_key_exists('workgroup', $params) && $this->getParam('workgroup') != '') {
            $params['workgroup'] = $this->getParam('workgroup');
        }
        
        return parent::nextAction($action, $params, $module);
    }
    
    /**
     * Method to override the uri function to include automatic inclusion
     * of mode and restriction
    *
    * @access  public
    * @param   array  $params         Associative array of parameter values
    * @param   string $module         Name of module to point to (blank for core actions)
    * @param   string $mode           The URI mode to use, must be one of 'push', 'pop', or 'preserve'
    * @param   string $omitServerName flag to produce relative URLs
    * @param   bool   $javascriptCompatibility flag to produce javascript compatible URLs
    * @returns string $uri the URL
    */
    public function uri($params = array(), $module = '', $mode = '', $omitServerName=FALSE, $javascriptCompatibility = FALSE, $omitExtraParams=FALSE)
    {
        $objFileManagerObject = $this->getObject('filemanagerobject');
        return $objFileManagerObject->uri($params, $module, $mode, $omitServerName, $javascriptCompatibility, $omitExtraParams);
    }
    
    /**
     * 
     * The standard dispatch method for the filemanager2 module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch($action='home')
    {
        $this->setLayoutTemplate('filemanager_layout_tpl.php');
        
        // retrieve the mode (edit/add/translate) from the querystring
        $mode = $this->getParam("mode", null);
        
        // hide banner and footer for certain modes
        $suppressModes = array ('selectfilewindow', 'selectimagewindow', 'fckimage', 'fckflash', 'fcklink');
        if (in_array($mode, $suppressModes)) {
            $this->setVar('pageSuppressBanner', TRUE);
            $this->setVar('pageSuppressToolbar', TRUE);
            $this->setVar('suppressFooter', TRUE);
            $this->setVar('mode', $mode);
        } else {
            $this->setVar('mode', NULL);
        }
        
        if ($this->getParam("restriction") == '') {
            $restrictions = array();
        } else {
            $restrictions = explode("_", strtolower($this->getParam("restriction")));
        }
        
        $this->setVar('restrictions', $restrictions);
        
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
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
    function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * 
    * Method to convert the action parameter into the name of 
    * a method of this class.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    * 
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__home";
        }
    }
    
    /**
     * Checks if the user should have access to the file manager.
     *
     * @return boolean True if the user has access, false otherwise.
     */
    protected function userHasAccess()
    {
        $limitedUsers = $this->objSysConfig->getValue('LIMITEDUSERS', 'filemanager');
        if ($limitedUsers) {
            $userId = $this->objUser->userId();
            $groups = array('Site Admin', 'Lecturers');
            $isMember = FALSE;
            foreach ($groups as $group) {
                $groupId = $this->objGroup->getId($group);
                if ($this->objGroup->isGroupMember($userId, $groupId)) {
                    $isMember = TRUE;
                    break;
                }
            }
            return $isMember;
        } else {
            return TRUE;
        }
    }
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * Default Action for File manager module
    * It shows the list of folders of a user
    * @access private
    */
    private function __home()
    {
        if ($this->getParam('value') != '' && $this->getParam('value') != 'undefined') {
            return $this->nextAction('fileinfo', array('id'=>$this->getParam('value')));
        }
        // Get Folder Details
        $folderpath = 'users/'.$this->objUser->userId();

        $folderId = $this->objFolders->getFolderId($folderpath);
        
        return $this->__viewfolder($folderId);
    }
    
    
    /**
     * Method to view/download the actual file
     * This approach is used to allow files to be move around without
     * adjust links in content
     * @access private
     */
    private function __file()
    {
        $id = $this->getParam('id');
        $filename = $this->getParam('filename');
        
        $file = $this->objFiles->getFileInfo($id);

        if ($file == FALSE || $file['filename'] != $filename) {
            die($this->objLanguage->languageText('mod_filemanager_norecordofsuchafile', 'filemanager', 'No Record of Such a File Exists').'.');
        }

        $filePath = $this->objConfig->getcontentPath().$file['path'];
        $filePath = $this->objCleanUrl->cleanUpUrl($filePath);

        if ($file['category'] == 'images') {

            $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

            if ($objSysConfig->getValue('FORCEMAXMODE', 'filemanager') == 'Y') {
                $forceMaxFilePath = $this->objConfig->getcontentPath().'/filemanager_forcemax/'.$id.'.jpg';
                $forceMaxFileBasePath = $this->objConfig->getcontentBasePath().'/filemanager_forcemax/'.$id.'.jpg';
                $originalImage = $this->objConfig->getcontentBasePath().$file['path'];
                $originalImage = $this->objCleanUrl->cleanUpUrl($originalImage);

                $forceMaxFilePath = $this->objCleanUrl->cleanUpUrl($forceMaxFilePath);
                $forceMaxFileBasePath = $this->objCleanUrl->cleanUpUrl($forceMaxFileBasePath);



                // To do: Build in Security on whether user can view file
                if (file_exists($forceMaxFileBasePath)) {
                    $filePath = $forceMaxFilePath;
                } else {
                    $width = $objSysConfig->getValue('FORCEMAXWIDTH', 'filemanager');
                    $height = $objSysConfig->getValue('FORCEMAXHEIGHT', 'filemanager');

                    if ($file['width'] == '' || $file['height'] == '') {
                        $fileInfo = getimagesize($originalImage);

                        if ($fileInfo != FALSE) {
                            $file['width'] = $fileInfo[0];
                            $file['height'] = $fileInfo[1];

                            $objMediaFileInfo = $this->getObject('dbmediafileinfo');
                            $objMediaFileInfo->updateWidthHeight($id, $fileInfo[0], $fileInfo[1]);
                        }
                    }

                    if ($file['width'] > $width || $file['height'] > $height) {
                        $imageResize = $this->getObject('imageresize', 'files');
                        $imageResize->setImg($originalImage);
                        $imageResize->resize($width, $height);

                        $objMkDir = $this->newObject('mkdir', 'files');
                        $objMkDir->mkdirs(dirname($forceMaxFileBasePath));

                        $imageResize->store($forceMaxFileBasePath);

                        $filePath = $forceMaxFilePath;

                    } else {
                        $filePath = $this->objConfig->getcontentPath().$file['path'];
                        $this->objCleanUrl->cleanUpUrl($filePath);
                    }
                }
            }
        }




        // To do: Build in Security on whether user can view file
        if (file_exists($filePath)) {
            //echo $filePath;
            header("Location:{$filePath}");
        } else {
            die ('File does not exist');
        }
    }
    
    /**
     * Method to view information about a file
     * @access private
     */
    private function __fileinfo()
    {
        $id = $this->getParam('id');
        $filename = $this->getParam('filename');
        
        $file = $this->objFiles->getFileInfo($id);

        if ($file == FALSE) {
            return $this->nextAction(NULL, array('error'=>'filedoesnotexist'));
        }

        if (array_key_exists('getid3info', $file)) {
            unset ($file['getid3info']);
        }

        $this->setVarByRef('file', $file);
        
        $folderParts = explode('/', $file['filefolder']);
        
        if ($folderParts[0] == 'context' && $folderParts[1] != $this->contextCode) {
            return $this->nextAction(NULL);
        }
        
        $folderPermission = $this->objFolders->checkPermissionUploadFolder($folderParts[0], $folderParts[1]);
        $this->setVar('folderPermission', $folderPermission);

        $tags = $this->objFileTags->getFileTags($id);
        $this->setVarByRef('tags', $tags);

        $this->objMenuTools->addToBreadCrumbs(array('File Information: '.$file['filename']));
        
        //$this->setLayoutTemplate('filemanager_3collayout_tpl.php');
        
        $objFilePreview = $this->getObject('filepreview');
        $preview = $objFilePreview->previewFile($file['id']);
        
        $this->setVarByRef('preview', $preview);
        
        if (trim($preview) == '') {
            $right = '';
        } else {
            $right = '<h2>'.$this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code').'</h2>';
            
            $right .= '<p>'.$this->objLanguage->languageText('mod_filemanager_embedinstructions', 'filemanager', 'Copy this code and paste it into any text box to display this file.').'</p>';
            
            $value = htmlentities('[FILEPREVIEW id="'.$file['id'].'" comment="'.$file['filename'].'" /]');
            
            $right .= '<form name="formtocopy">
            
    <input name="texttocopy" readonly="readonly" style="width:70%" type="text" value="'.$value.'" />';
            $right .= '
    <br /><input type="button" onclick="javascript:copyToClipboard(document.formtocopy.texttocopy);" value="Copy to Clipboard" />
    </form>';
        }
        
        
        $this->setVarByRef('embedCode', $right);
        
        $fileBreadrumbs = $this->objFolders->generateBreadCrumbs($file['path'], TRUE).$file['filename'];
        $this->setVarByRef('fileBreadrumbs', $fileBreadrumbs);
        
        // Get Folder Id of Item
        $folderId = $this->objFolders->getFolderId(dirname($file['path']));
        $this->setVarByRef('folderId', $folderId);
        
        $objCopy = $this->getObject('copytoclipboard', 'htmlelements');
        $objCopy->show();
        
        return 'fileinfo_tpl.php';
    }
    
    /**
     * Method to view a file which is actually a symlink
     * @access private
     */
    private function __symlink()
    {
        $id = $this->getParam('id');
        $symLink = $this->objSymlinks->getSymlink($id);
        
        if ($symLink == FALSE) {
            return $this->nextAction(NULL, array('error'=>'filedoesnotexist'));
        }
        
        $file = $this->objFiles->getFileInfo($symLink['fileid']);
        
        if ($file == FALSE) {
            return $this->nextAction(NULL, array('error'=>'filedoesnotexist'));
        }
        
        $symLinkFolder = $this->objFolders->getFolder($symLink['folderid']);
        $this->setVarByRef('folderId', $symLink['folderid']);
        
        
        if (array_key_exists('getid3info', $file)) {
            unset ($file['getid3info']);
        }

        $this->setVarByRef('file', $file);

        $tags = $this->objFileTags->getFileTags($id);
        $this->setVarByRef('tags', $tags);

        $this->objMenuTools->addToBreadCrumbs(array('File Information: '.$file['filename']));
        
        $folderParts = explode('/', $file['filefolder']);
        
        //$quota = $this->objQuotas->getQuota($folder['folderpath']);
        //var_dump($quota);
        
        if ($folderParts[0] == 'context' && $folderParts[1] != $this->contextCode) {
            return $this->nextAction(NULL);
        }
        
        $folderPermission = $this->objFolders->checkPermissionUploadFolder($folderParts[0], $folderParts[1]);
        $this->setVar('folderPermission', $folderPermission);
        
        $objFilePreview = $this->getObject('filepreview');
        $preview = $objFilePreview->previewFile($file['id']);
        
        $this->setVarByRef('preview', $preview);
        
        if (trim($preview) == '') {
            $right = '';
        } else {
            $right = '<h2>'.$this->objLanguage->languageText('mod_filemanager_embedcode', 'filemanager', 'Embed Code').'</h2>';
            
            $right .= '<p>'.$this->objLanguage->languageText('mod_filemanager_embedinstructions', 'filemanager', 'Copy this code and paste it into any text box to display this file.').'</p>';
            
            $value = htmlentities('[FILEPREVIEW id="'.$file['id'].'" comment="'.$file['filename'].'" /]');
            
            $right .= '<form name="formtocopy">
            
    <input name="texttocopy" readonly="readonly" style="width:70%" type="text" value="'.$value.'" />';
            $right .= '
    <br /><input type="button" onclick="javascript:copyToClipboard(document.formtocopy.texttocopy);" value="Copy to Clipboard" />
    </form>';
        }
        
        
        $this->setVarByRef('embedCode', $right);
        
        $fileBreadrumbs = $this->objFolders->generateBreadCrumbs($symLinkFolder['folderpath'].'/'.$file['filename'], TRUE).$file['filename'];
        $this->setVarByRef('fileBreadrumbs', $fileBreadrumbs);
        
        $objCopy = $this->getObject('copytoclipboard', 'htmlelements');
        $objCopy->show();
        
        return 'fileinfo_tpl.php';
    }
    
    /**
     * Underconstruction - method to return a preview of a file via ajax
     * @access private
     *
     */
    private function __ajaxfilepreview()
    {
        $id = $this->getParam('id');
        
    }
    
    /**
     * Method to upload files to the server
     *
     * @access private
     */
    private function __upload()
    {
        $folder = $this->objFolders->getFolder($this->getParam('folder'));

        if ($folder != FALSE) {
            $this->objUpload->setUploadFolder($folder['folderpath']);
        }

        // Upload Files
        $results = $this->objUpload->uploadFiles();

        // Check if User entered page by typing in URL
        if ($results == FALSE) {
            return $this->nextAction(NULL);
        }

        // Check if no files were provided
        if (count($results) == 1 && array_key_exists('nofileprovided', $results)) {
            return $this->nextAction('uploadresults', array('error'=>'nofilesprovided'));
        }

        // Put Message into Array
        $messages = $this->objUploadMessages->processMessageUrl($results);
        $messages['folder'] = $this->getParam('folder');

        return $this->nextAction('uploadresults', $messages);
    }
    
    /**
     * Attempted ajax upload - doesnt work
     * To be relooked
     * @access private
     */
    private function __ajaxupload()
    {
        $folder = $this->objFolders->getFolder($this->getParam('folder'));

        if ($folder != FALSE) {
            $this->objUpload->setUploadFolder($folder['folderpath']);
        }

        // Upload Files
        $results = $this->objUpload->uploadFiles();
        
        header("HTTP/1.0 200 OK");
        
    }
    
    /**
     * Method to display the results of file uploads to the user
     *
     * @access private
     */
    private function __uploadresults()
    {
        $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
        $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());

        $this->setVar('overwriteMessage', $this->objUploadMessages->processOverwriteMessages());

        $this->objMenuTools->addToBreadCrumbs(array('Upload Results'));

        return 'list_uploadresults_tpl.php';
    }
    
    /**
     * Method to fix temp files. These are files that require user intervention to be overwritten
     *
     * @access private
     */
    private function __fixtempfiles()
    {
        // Create Array of Files that are affected
        $listItem = explode('__', $this->getParam('listitems'));

        // Create Array for Results
        $resultInfo = array();

        // Loop through each files
        foreach ($listItem as $item)
        {
            // Get the option user has decided - either delete temp or overwrite
            $option = $this->getParam($item);

            // Check that Option is Valid
            if ($item != '') {
                // Take Action based on option
                switch (trim($option))
                {
                    // Delete Temp File
                    case 'delete':
                        $this->objFiles->deleteTemporaryFile($item);
                        $resultInfo[$item] = 'delete';
                        break;
                    // Overwrite File
                    case 'overwrite':
                        $resultInfo[$item] = $this->objFiles->overwriteFile($item);
                        break;
                    default:
                        $resultInfo[$item] = 'unknownaction';
                        break;
                }
            }
        }

        // Generate Flag For Results
        $result = '';
        $divider = '';

        if (count($resultInfo) > 0) {
            foreach ($resultInfo as $item=>$action)
            {
                $result .= $divider.$item.'__'.$action;
                $divider = '____';
            }
        }

        $nextAction = $this->getParam('nextaction');
        $nextParams = $this->getParam('nextparams');

        return $this->nextAction('overwriteresults', array('result'=>$result, 'nextaction'=>$nextAction, 'nextparams'=>$nextParams));
    }
    
    /**
     * Method to display the results of file overwriting to the user
     *
     * @access private
     */
    private function __overwriteresults()
    {
        $results = $this->getParam('result');

        if ($results == '') {
            return $this->nextAction(NULL, 'overwriteresultproblematic');
        } else {
            $this->setVarByRef('results', $results);
            return 'overwriteresults_tpl.php';
        }
    }
    
    /**
     * Method to delete multiple files
     * This step provides a form to request user confirmation
     *
     * @access private
     */
    private function __multidelete()
    {
        if (isset($_POST['symlinkcontext'])) {
            return $this->__symlinkcontext();
        }
        $this->objMenuTools->addToBreadCrumbs(array('Confirm Delete'));
        return 'multidelete_form_tpl.php';
    }
    
    
    /**
     * Method to delete multiple files, once user confirmation is given
     *
     * @access private
     */
    private function __multideleteconfirm()
    {
        // echo '<pre>';
        // print_r($_POST);

        if ($this->getParam('files') == NULL || !is_array($this->getParam('files')) || count($this->getParam('files')) == 0) {
            
            if ($this->getParam('folder') != '') {
                return $this->nextAction('viewfolder', array('message'=>'nofilesconfirmedfordelete', 'folder'=>$this->getParam('folder')));
            } else {
                return $this->nextAction(NULL, array('message'=>'nofilesconfirmedfordelete', 'folder'=>$this->getParam('folder')));
            }
        } else {
            $files = $this->getParam('files');

            $numFiles = 0;
            $numFolders = 0;

            $objBackground = $this->newObject('background', 'utilities');

            //check the users connection status,
            //only needs to be done once, then it becomes internal
            $status = $objBackground->isUserConn();

            //keep the user connection alive, even if browser is closed!
            $callback = $objBackground->keepAlive();

            foreach ($files as $file)
            {
                if (substr($file, 0, 8) == 'folder__') {
                    $folder = substr($file, 8);
                    $this->objFolders->deleteFolder($folder);
                    $numFolders++;
                } else if (substr($file, 0, 9) == 'symlink__') {
                    $symlink = substr($file, 9);
                    $this->objSymlinks->removeSymlink($symlink);
                    $numFiles++;
                } else {
                    $fileDetails = $this->objFiles->getFile($file);

                    // Check if User, and so be able to delete files
                    if ($fileDetails['userid'] = $this->objUser->userId()) {
                        $this->objFiles->deleteFile($file, TRUE);
                        $numFiles++;
                    }
                }
            }

            //$call2 = $objBackground->setCallback("john.doe@tohir.co.za","Your Script","The really long running process that you requested is complete!");

            if ($this->getParam('folder') != '') {
                return $this->nextAction('viewfolder', array('folder'=>$this->getParam('folder'), 'message'=>'filesdeleted', 'numfiles'=>$numFiles, 'numfolders'=>$numFolders));
            } else {
                return $this->nextAction(NULL, array('message'=>'filesdeleted'));
            }
        }
    }
    
    /**
     * Method to view the contents of a folder
     *
     * @access private
     */
    private function __viewfolder($id=NULL)
    {
        if ($id == NULL) {
            $id = $this->getParam('folder');
        }
        
        // TODO: Check permission to enter folder

        // Get Folder Details
        $folder = $this->objFolders->getFolder($id);

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

        $this->setVarByRef('folder', $folder);
        $this->setVarByRef('quota', $quota);

        $this->setVarByRef('folderpath', basename($folder['folderpath']));

        $this->setVar('folderId', $id);
        $this->setVar('folderPermission', $folderPermission);

        $subfolders = $this->objFolders->getSubFolders($id);
        $this->setVarByRef('subfolders', $subfolders);

        $files = $this->objFiles->getFolderFiles($folder['folderpath']);
        $this->setVarByRef('files', $files);
        
        $symlinks = $this->objSymlinks->getFolderSymlinks($id);
        
        
        
        $this->setVarByRef('symlinks', $symlinks);

        $objPreviewFolder = $this->getObject('previewfolder');
        $objPreviewFolder->editPermission = $folderPermission;
        $this->setVarByRef('table', $objPreviewFolder->previewContent($subfolders, $files, $symlinks, explode('____', $this->getParam('restriction'))));

        $breadcrumbs = $this->objFolders->generateBreadCrumbs($folder['folderpath']);
        $this->setVarByRef('breadcrumbs', $breadcrumbs);
        
        return 'showfolder.php';
    }
    
    /**
     * Method to create a folder.
     *
     * @access private
     */
    private function __createfolder()
    {
        $parentId = $this->getParam('parentfolder', 'ROOT');
        $foldername = $this->getParam('foldername');

        // If no folder name is given, res
        if (trim($foldername) == '') {
            return $this->nextAction('viewfolder', array('folder'=>$parentId, 'error'=>'nofoldernameprovided'));
        }

        if (preg_match('/\\\|\/|\\||:|\\*|\\?|"|<|>/', $foldername)) {
            return $this->nextAction('viewfolder', array('folder'=>$parentId, 'error'=>'illegalcharacters'));
        }
        
        // Replace spaces with underscores
        $foldername = str_replace(' ', '_', $foldername);

        if ($parentId == 'ROOT') {
            $folderpath = 'users/'.$this->objUser->userId();
        } else {
            $folder = $this->objFolders->getFolder($parentId);

            if ($folder == FALSE) {
                return $this->nextAction(NULL, array('error'=>'couldnotfindparentfolder'));
            }
            $folderpath = $folder['folderpath'];
        }


        $this->objMkdir = $this->getObject('mkdir', 'files');

        $path = $this->objConfig->getcontentBasePath().'/'.$folderpath.'/'.$foldername;

        $result = $this->objMkdir->mkdirs($path);

        if ($result) {
            $folderId = $this->objFolders->indexFolder($path);
            return $this->nextAction('viewfolder', array('folder'=>$folderId, 'message'=>'foldercreated'));
        } else {
            return $this->nextAction(NULL, array('error'=>'couldnotcreatefolder'));
        }
    }
    
    /**
     * Method to delete a folder
     *
     * @access private
     */
    private function __deletefolder()
    {
        $id = $this->getParam('id');
        
        // Get the Folder Path
        $folder = $this->objFolders->getFolderPath($id);

        $objBackground = $this->newObject('background', 'utilities');

        //check the users connection status,
        //only needs to be done once, then it becomes internal
        $status = $objBackground->isUserConn();

        //keep the user connection alive, even if browser is closed!
        $callback = $objBackground->keepAlive();

        // Delete the Folder
        $result = $this->objFolders->deleteFolder($id);

        //
        //$call2 = $objBackground->setCallback("john.doe@tohir.co.za","Your Script","The really long running process that you requested is complete!");


        if ($result == 'norecordoffolder') {
            return $this->nextAction(NULL, array('error'=>'norecordoffolder'));
        }

        $resultmessage = $result ? 'folderdeleted' : 'couldnotdeletefolder';

        // Get Parent Id based on the Folder Path
        $parentId = $this->objFolders->getFolderId(dirname($folder));

        // Redirect to Parent Folder
        return $this->nextAction('viewfolder', array('folder'=>$parentId, 'message'=>$resultmessage, 'ref'=>basename($folder)));
    }
    
    /**
     * Method to create a symlink to a file
     * This presents a form to request user confirmation,
     * as well as the link should be located.
     *
     * @access private
     */
    private function __symlinkcontext()
    {
        $this->objMenuTools->addToBreadCrumbs(array('Add to Course'));
        return 'symlinkcontext_tpl.php';
    }
    
    /**
     * Method to create the symlinks
     *
     * @access private
     */
    private function __symlinkconfirm()
    {
        $files = $this->getParam('files');
        $folder = $this->getParam('parentfolder');
        $origFolder = $this->getParam('folder');
        
        if (count($files) > 0) {
            
            foreach ($files as $file)
            {
                $this->objSymlinks->addSymlink($file, $folder);
            }
            
            return $this->nextAction('viewfolder', array('folder'=>$folder, 'message'=>'symlinksadded'));
        } else {
            return $this->nextAction('viewfolder', array('folder'=>$origFolder, 'message'=>'couldnotcreatesymlinks'));
        }
    }
    
    /**
     * Method to extract an archive, an index all files
     *
     * @access private
     */
    private function __extractarchive()
    {
        $archiveFileId = $this->getParam('file');
        
        $file = $this->objFiles->getFullFilePath($archiveFileId);
        
        if ($this->debug) {
            echo 'Zip Files Detail';
            var_dump($file);
        }

        if ($file == FALSE) {
            return $this->nextAction('viewfolder', array('folder'=>$this->getParam('parentfolder'), 'error'=>'couldnotfindarchive'));
        } else {

            $parentId = $this->getParam('parentfolder');

            if ($parentId == 'ROOT') {
                $parentId = $this->objFolders->getFolderId('users/'.$this->objUser->userId());
            }
            
            if ($this->debug) {
                echo 'Posted Variables';
                var_dump($_POST);
                
                echo 'Folder ID';
                var_dump($parentId);
            }
            
            $folder = $this->objFolders->getFolderPath($parentId);
            $fullFolderPath = $this->objFolders->getFullFolderPath($parentId);

            $folderParts = explode('/', $folder);
            
            if ($this->debug) {
                echo 'FolderParts';
                var_dump($folder);
                var_dump($folderParts);
            }
            
            $objBackground = $this->newObject('background', 'utilities');

            //check the users connection status,
            //only needs to be done once, then it becomes internal
            $status = $objBackground->isUserConn();

            //keep the user connection alive, even if browser is closed!
            $callback = $objBackground->keepAlive();

            $objZip = $this->newObject('wzip', 'utilities');
            $objZip->unzip($file, $fullFolderPath);
            
            if ($this->debug) {
                echo 'Full Folder Path';
                var_dump($fullFolderPath);
            }
            
            $objIndexFileProcessor = $this->getObject('indexfileprocessor');
            $objIndexFileProcessor->indexFolder($folderParts[0], $folderParts[1], $fullFolderPath, $this->objUser->userId());
            
            //$call2 = $objBackground->setCallback("john.doe@tohir.co.za","Your Script","The really long running process that you requested is complete!");
            
            return $this->nextAction('viewfolder', array('folder'=>$parentId, 'message'=>'archiveextracted', 'archivefile'=>$archiveFileId));
        }
    }
    
    /**
     * Method to present a user with a form to update the details of a file
     *
     *
     */
    private function __editfiledetails()
    {
        $id = $this->getParam('id');
        
        $file = $this->objFiles->getFile($id);
        
        if ($file == FALSE) {
            return $this->nextAction(NULL, array('error'=>'filedoesnotexist'));
        } else {
            $this->setVarByRef('file', $file);
            $tags = $this->objFileTags->getFileTags($id);
            $this->setVarByRef('tags', $tags);
            return 'editfiledetails_tpl.php';
        }
    }
    
    /**
     * Method to update the details such as description, tags
     * and license of a file
     * @access private
     */
    private function __updatefiledetails()
    {
        $id = $this->getParam('id');
        $description = $this->getParam('description');
        $license = $this->getParam('creativecommons');
        $keywords = $this->getParam('keywords');

        if ($id == '') {
            return $this->nextAction(NULL, array('error'=>'filedoesnotexist'));
        } else {
            $result = $this->objFiles->updateDescriptionLicense($id, $description, $license);

            if ($result) {
                $this->objFileTags->addFileTags($id, $keywords);
            }

            return $this->nextAction('fileinfo', array('id'=>$id, 'message'=>'filedetailsupdated'));
        }
    }
    
    /**
     * Method to display a tag cloud of the current user's files
     *
     *
     */
    private function __tagcloud()
    {
        $tagCloudItems = $this->objFileTags->getTagCloudResults($this->objUser->userId());
        $this->setVarByRef('tagCloudItems', $tagCloudItems);

        return 'tagcloud_tpl.php';
    }
    
    
    /**
     * Method to view all files that match a certain tag
     *
     *
     */
    private function __viewbytag()
    {
        $tag = $this->getParam('tag');
        
        if (trim($tag) == '') {
            return $this->nextAction('tagcloud', array('error'=>'notag'));
        }

        $this->setVarByRef('tag', $tag);

        $files = $this->objFileTags->getFilesWithTag($this->objUser->userId(), $tag);
        $this->setVarByRef('files', $files);

        if (count($files) == 0) {
            return $this->nextAction('tagcloud', array('error'=>'nofileswithtag', 'tag'=>$tag));
        }

        $this->setVarByRef('files', $files);

        $objPreviewFolder = $this->getObject('previewfolder');
        $table = $objPreviewFolder->previewContent(array(), $files);
        $this->setVarByRef('table', $table);

        return 'showfileswithtags_tpl.php';
    }
    
    
    /**
     * Method to get a thumbnail preview of a file
     *
     *
     */
    private function __thumbnail()
    {
        $id = $this->getParam('id');
        
        // Get the File Details of the File
        $file = $this->objFiles->getFile($id);

        // Check that File Exists
        if ($file == FALSE) {
            return FALSE;
        } else {
            // Load Thumbnails Class
            $objThumbnail = $this->getObject('thumbnails', 'filemanager');

            // Get Thumbnail
            $thumb = $objThumbnail->getThumbnail($id, $file['filename']);

            // If thumbnail does not exist
            if ($thumb == FALSE) {
                // Re/create it
                $objThumbnail->createThumbailFromFile($this->objConfig->getcontentBasePath().'/'.$file['path'], $id);

                // Get Thumbnail
                $thumb = $objThumbnail->getThumbnail($id, $file['filename']);
            }

            // Redirect to thumnail
            header('Location:'.$thumb);
        }
    }
    
    /**
     * Method to show the fckeditor interface for inserting images
     *
     *
     */
    private function __fckimage()
    {
        return $this->nextAction(NULL, array('mode'=>'fckimage', 'restriction'=>'jpg____gif____png____jpeg', 'loadwindow'=>'yes', 'url'=>$this->getParam('url')));
    }
    
    /**
     * Method to show the fckeditor interface for inserting flash movies
     *
     *
     */
    private function __fckflash()
    {
        return $this->nextAction(NULL, array('mode'=>'fckflash', 'restriction'=>'swf', 'loadwindow'=>'yes'));
    }
    
    
    /**
     * Method to show the fckeditor interface to insert a link to a file in file manager
     *
     *
     */
    private function __fcklink()
    {
        return $this->nextAction(NULL, array('mode'=>'fcklink', 'loadwindow'=>'yes'));
    }
    
    
    /**
     * Method to update the search index for all files in filemanager
     *
     *
     */
    private function __indexsearchfiles()
    {
        $this->objFiles->updateFileSearch();
        return $this->nextAction(NULL, array('message'=>'searchindexupdated'));
    }
    
    
    /**
     *
     *
     *
     */
   private  function __search()
    {
        return 'search_tpl.php';
    }
    
    /**
     *
     *
     *
     */
    private function __quotamanager()
    {
        return 'quotamanager_tpl.php';
    }
    
    /**
     *
     *
     *
     */
    private function __ajaxgetquotas()
    {
        $this->setLayoutTemplate(NULL);
        $this->setPageTemplate(NULL);
        
        $searchType = $this->getParam('searchType');
        $searchField = $this->getParam('searchField');
        $searchFor = $this->getParam('searchFor');
        $orderBy = $this->getParam('orderBy');
        
        if ($searchType == 'context') {
            $defaultQuota = $this->objQuotas->getDefaultContextQuota();
        } else {
            $searchType = 'user';
            $defaultQuota = $this->objQuotas->getDefaultUserQuota();
        }
        
        $results = $this->objQuotas->getResults($searchType, $searchField, $searchFor, $orderBy);
        $this->setVarByRef('results', $results);
        $this->setVarByRef('searchType', $searchType);
        $this->setVarByRef('defaultQuota', $defaultQuota);
        
        return 'quotaslist_tpl.php';
    }
    
    private private function __editquota()
    {
        $id = $this->getParam('id');
        
        $quota = $this->objQuotas->getQuotaFromId($id);
        
        if ($quota == FALSE) {
            return $this->nextAction('quotamanager', array('error'=>'unknownquota'));
        } else {
            $this->setVarByRef('quota', $quota);
            return 'editquota_tpl.php';
        }
    }
    
    private function __updatequota()
    {
        // Get Values
        $id = $this->getParam('id');
        $quotatype = $this->getParam('quotatype');
        $customquota = $this->getParam('customquota');
        
        // Are we setting default or custom value
        if ($quotatype == 'Y') {
            $this->objQuotas->setToUseDefaultQuota($id);
        } else {
            if ($customquota == '') {
                return $this->nextAction('editquota', array('id'=>$id, 'error'=>'novalue'));
            }
            
            if (!is_numeric($customquota)) {
                return $this->nextAction('editquota', array('id'=>$id, 'error'=>'nonumber'));
            } else {
                $this->objQuotas->setToUseCustomQuota($id, $customquota);
            }
        }
        
        // Get quota
        $quota = $this->objQuotas->getQuotaFromId($id);
        
        // If quota doesn't exist, redirect
        if ($quota == FALSE) {
            return $this->nextAction('quotamanager');
        } else {
            // Do an approximate search to quota that was update
            if (substr($quota['path'], 0, 7) == 'context') {
                return $this->nextAction('quotamanager', array('message'=>'quotatupdated', 'id'=>$quota['id'], 'searchType'=>'context', 'searchField_context'=>'contextcode', 'searchfor'=>substr($quota['path'], 8)));
            } else {
                return $this->nextAction('quotamanager', array('message'=>'quotatupdated', 'id'=>$quota['id'], 'searchType'=>'users', 'searchField_user'=>'firstname', 'searchfor'=>$this->objUser->getFirstname(substr($quota['path'], 6))));
            }
        }
    }
    
    /*------------- END: Set of methods to replace case selection ------------*/
    

}
?>
