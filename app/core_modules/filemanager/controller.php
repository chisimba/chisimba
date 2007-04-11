<?
/**
* File Manager Controller
*
* @author Tohir Solomons
* @package filemanager
* @version 0.2
*/
class filemanager extends controller
{

    /**
    * Constructor
    */
    public function init()
    {
        $this->objFiles =& $this->getObject('dbfile');
        $this->objFolders =& $this->getObject('dbfolder');
        $this->objFileTags = $this->getObject('dbfiletags');
        $this->objFileOverwrite =& $this->getObject('checkoverwrite');
        $this->objCleanUrl =& $this->getObject('cleanurl');
        $this->objUpload =& $this->getObject('upload');
        $this->objFilePreview =& $this->getObject('filepreview');
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objUser =& $this->getObject('user', 'security');

        $this->objUploadMessages =& $this->getObject('uploadmessages');

        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objMenuTools =& $this->getObject('tools', 'toolbar');
        $this->loadClass('link', 'htmlelements');
    }

    /**
	* Method to process actions to be taken
    *
    * @param string $action String indicating action to be taken
	*/
    public function dispatch($action)
    {
        $this->setLayoutTemplate('filemanager_layout_tpl.php');
        
        $this->objFiles->updateFilePath();

        switch ($action)
        {
            case 'upload':
                return $this->handleUploads();
            case 'checkoverwrite':
                return $this->checkFileOverwrite();
            case 'fixtempfiles':
                return $this->fixTempFiles();
            case 'overwriteresults':
                return $this->showOverwriteResults();
            case 'file':
                return $this->showFile($this->getParam('id'), $this->getParam('filename'));
            case 'fileinfo':
                return $this->showFileInfo($this->getParam('id'), $this->getParam('filename'));
            case 'uploadresults':
                return $this->showUploadResults();
            case 'multidelete':
                return $this->showMultiDelete();
            case 'multideleteconfirm':
                return $this->multiDeleteConfirm();
            case 'selecttest':
                return $this->selecttest();
            case 'selectfilewindow':
                return $this->showFileWindow();
            case 'selectimagewindow':
                return $this->showImageWindow(FALSE);
            case 'selectrealtimeimagewindow':
                return $this->showImageWindow(TRUE);
            case 'sendpreview':
                return $this->sendPreview($this->getParam('id'), $this->getParam('jsId'));
            case 'selectfileuploads':
                return $this->selectFileUploads();
            case 'fckimage':
                return $this->showFCKImages();
            case 'fckflash':
                return $this->showFCKFlash();
            case 'fcklink':
                return $this->showFCKEditorInterface();
            case 'uploadfiles':
                $this->objMenuTools->addToBreadCrumbs(array('Upload Files'));
                return 'multipleupload_tpl.php';
            case 'indexfiles':
                return $this->indexFiles();
            case 'viewfolder':
                return $this->showFolder($this->getParam('folder'));
            case 'createfolder':
                return $this->createFolder();
            case 'deletefolder':
                return $this->deleteFolder($this->getParam('id'));
            case 'extractarchive':
                return $this->extractArchive();
            case 'editfiledetails':
                return $this->editFileDetails($this->getParam('id'));
            case 'updatefiledetails':
                return $this->updateFileDetails();
            case 'tagcloud':
                return $this->showTagCloud();
            case 'viewbytag':
                return $this->viewByTag($this->getParam('tag'));
            case 'thumbnail':
                return $this->getThumbnail($this->getParam('id'));
            default:
                return $this->filesHome();
        }
    }
    
    /**
    * Method to show the File Manager Home Page
    */
    public function filesHome()
    {
        // Get Folder Details
        $folderpath = 'users/'.$this->objUser->userId();
        
        $folderId = $this->objFolders->getFolderId($folderpath);
        
        // Get Folder Details
        $folder = $this->objFolders->getFolder($folderId);
        $this->setVarByRef('folder', $folder);
        
        if ($folderId == FALSE) {
            $objIndexFileProcessor = $this->getObject('indexfileprocessor');

            $list = $objIndexFileProcessor->indexUserFiles($this->objUser->userId());
        
        }
        
        // update the paths of files that do not have the filefolder item set
        // This is due to a patch added
        $this->objFiles->updateFilePath();
        
        $this->setVar('breadcrumbs', 'My Files');
        $this->setVar('folderpath', 'My Files');
        $this->setVar('folderId', $folderId);
        
        $subfolders = $this->objFolders->getSubFoldersFromPath($folderpath);
        $this->setVarByRef('subfolders', $subfolders);
        
        $files = $this->objFiles->getFolderFiles($folderpath);
        $this->setVarByRef('files', $files);
        
        $objPreviewFolder =& $this->getObject('previewfolder');
        $this->setVarByRef('table', $objPreviewFolder->previewContent($subfolders, $files));
                
        return 'showfolder.php';
    }

    /**
    * Method to download a file
    * @param string $id Record Id of the File
    * @param string $filename Filename of the File
    */
    public function showFile($id, $filename)
    {
		$this->requiresLogin(FALSE);
        $file = $this->objFiles->getFileInfo($id);

        if ($file == FALSE || $file['filename'] != $filename) {
            die('No Record of Such a File Exists.');
        }

        $filePath = $this->objConfig->getcontentPath().$file['path'];

        $this->objCleanUrl->cleanUpUrl($filePath);

        // To do: Build in Security on whether user can view file
        if (file_exists($filePath)) {
            header("Location:{$filePath}");

            // header('Content-type: '.$file['mimetype']);
            // header('Content-Disposition: inline; filename='.$file['filename']);
            // readfile($filePath);

        } else {
            die ('File does not exist');
        }
    }


    /**
    * Method to Show Information about a file
    * @param string $id Record Id of the File
    * @param string $filename Filename of the File
    */
    public function showFileInfo($id, $filename)
    {
        $file = $this->objFiles->getFileInfo($id);

        if ($file == FALSE) {
            return $this->nextAction(NULL, array('error'=>'filedoesnotexist'));
        }

        if (array_key_exists('getid3info', $file)) {
            unset ($file['getid3info']);
        }

        $this->setVarByRef('file', $file);
        
        $tags = $this->objFileTags->getFileTags($id);
        $this->setVarByRef('tags', $tags);
        
        $this->objMenuTools->addToBreadCrumbs(array('File Information: '.$file['filename']));
        return 'fileinfo_tpl.php';
    }

    /**
    * Method to Handle Uploads
    */
    public function handleUploads()
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
    * Method to Show the Results of File Uploads
    */
    public function showUploadResults()
    {

        $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
        $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());
        
        $this->setVar('overwriteMessage', $this->objUploadMessages->processOverwriteMessages());

        $this->objMenuTools->addToBreadCrumbs(array('Upload Results'));
        
        return 'list_uploadresults_tpl.php';
    }

    /**
    * Method to show the File Overwrite Checker
    */
    public function checkFileOverwrite()
    {
        $this->objMenuTools->addToBreadCrumbs(array('Overwrite Files?'));
        
        return 'list_fileoverwrite_tpl.php';
    }

    /**
    * Method to show the File Overwrite Checker, but in a popup window
    */
    public function checkFileOverwritePopup()
    {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressContainer', TRUE);

        $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
        $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());

        $this->appendArrayVar('bodyOnLoad', 'window.focus();');

        $this->setLayoutTemplate(NULL);

        return 'fileoverwrite_tpl.php';
    }


    /**
    * Method to handle temporary file overwrites
    */
    public function fixTempFiles()
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
                        // Create Path to Temp File
                        $tempFilePath = $this->objConfig->getcontentBasePath().'/filemanager_tempfiles/'.$item;
                        
                        // Get File Record
                        $fileInfo = $this->objFiles->getFileInfo($item);
                        
                        // If Temp File exists and Record Exists
                        // Perform Overwrite
                        if ($fileInfo != FALSE && file_exists($tempFilePath)) {
                        
                            // Generate Path to Existing File
                            $filePath = $this->objConfig->getcontentBasePath().$fileInfo['path'];
                            
                            // Delete Existing File if it exists
                            if (file_exists($filePath)) {
                                unlink($filePath);
                            }
                            
                            // Move Overwrite File
                            rename($tempFilePath, $filePath);
                            
                            // Todo: Reindex Metadata
                            
                            $resultInfo[$item] = 'overwrite';
                        } else {
                            $this->objFiles->deleteTemporaryFile($item);
                            
                            $resultInfo[$item] = 'cannotoverwrite';
                        }
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
    * Method to show the Results of the Upload
    *
    */
    public function showOverwriteResults()
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
    * Method to show the Multi Delete Confirmation Page
    */
    public function showMultiDelete()
    {
        $this->objMenuTools->addToBreadCrumbs(array('Confirm Delete'));
        return 'multidelete_form_tpl.php';
    }

    /**
    * Method to Delete Multiple Files
    */
    public function multiDeleteConfirm()
    {
        // echo '<pre>';
        // print_r($_POST);
        
        if ($this->getParam('files') == NULL || !is_array($this->getParam('files')) || count($this->getParam('files')) == 0) {
            return $this->nextAction(NULL, array('message'=>'nofilesconfirmedfordelete'));
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
                } else {
                    $fileDetails = $this->objFiles->getFile($file);
                    
                    // Check if User, and so be able to delete files
                    if ($fileDetails['userid'] = $this->objUser->userId()) {
                        $this->objFiles->deleteFile($file, TRUE);
                        $numFiles++;
                    }
                }
            }
            
            $call2 = $objBackground->setCallback("john.doe@tohir.co.za","Your Script","The really long running process that you requested is complete!");

            if ($this->getParam('folder') != '') {
                return $this->nextAction('viewfolder', array('folder'=>$this->getParam('folder'), 'message'=>'filesdeleted', 'numfiles'=>$numFiles, 'numfolders'=>$numFolders));
            } else {
                return $this->nextAction(NULL, array('message'=>'filesdeleted'));
            }
        }
    }

    /**
    * Method to demo the File Selector
    */
    public function selecttest()
    {
        return 'demo_showfilewindow_tpl.php';
    }

    /**
    * Method to Show the File Selector Pop Up Window
    */
    public function showFileWindow()
    {
        if ($this->getParam('restrict') == '') {
            $restriction = array();
        } else {
            $restriction = explode('____', $this->getParam('restrict'));
        }
        
        $this->setVarByRef('restrictions', $restriction);

        if ($this->getParam('mode') == 'fileupload') {
            $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
            $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());
        }

        $files = $this->objFiles->getUserFiles($this->objUser->userId(), NULL, $restriction, TRUE);

        $this->setVarByRef('files', $files);

        // Script to Close Window automatically if opener does not exist
        $checkOpenerScript = '
<script type="text/javascript">
function checkWindowOpener()
{
    if (!window.opener) {
        window.close();
    }
}
</script>
        ';

        $this->appendArrayVar('headerParams', $checkOpenerScript);
        $this->appendArrayVar('bodyOnLoad', 'checkWindowOpener();');
        $this->appendArrayVar('bodyOnLoad', 'window.focus();');

        $inputname = $this->getParam('name');
        $this->setVarByRef('inputname', $inputname);

        $defaultValue = $this->getParam('value');
        $this->setVarByRef('defaultValue', $defaultValue);

        $this->setLayoutTemplate(NULL);
        $this->setVar('pageSuppressBanner', TRUE);
        //$this->setVar('pageSuppressXML', TRUE);
        return 'popup_showfilewindow_tpl.php';
    }
    
    /**
    * Ajax function to send preview of files
    * @param string $fileId Record Id of the File
    * @param string $jsId JavaScript Id of the File
    */
    public function sendPreview($fileId, $jsId)
    {
        $file = $this->objFiles->getFileInfo($fileId);

        if ($file == FALSE) {
            echo 'No Such File Exists '.$fileId;
            echo '<pre>';
            print_r($_GET);
            echo '</pre>';
        } else {

            $link = new link("javascript:selectFile('".$fileId."', ".$jsId.");");
            $link->link = 'Select';

            echo '<h1>Preview of: '.$file['filename'].' ('.$link->show().')</h1>';
            echo $this->objFilePreview->previewFile($fileId);
        }
    }
    
    /**
    * Method to show the a window with previews to select an image
    * @param boolean $showFullLinks Flag whether to show full link to file or not
    */
    public function showImageWindow($showFullLinks=FALSE)
    {
        $restriction = array('gif', 'jpg', 'jpeg', 'png');

        if ($this->getParam('mode') == 'fileupload') {
            $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
            $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());
        }

        $files = $this->objFiles->getUserFiles($this->objUser->userId(), NULL, $restriction, TRUE);

        $this->setVarByRef('files', $files);

        // Script to Close Window automatically if opener does not exist
        $checkOpenerScript = '
<script type="text/javascript">
function checkWindowOpener()
{
    if (!window.opener) {
        window.close();
    }
}
</script>
        ';

        $this->appendArrayVar('headerParams', $checkOpenerScript);
        $this->appendArrayVar('bodyOnLoad', 'checkWindowOpener();');
        $this->appendArrayVar('bodyOnLoad', 'window.focus();');

        $inputname = $this->getParam('name');
        $this->setVarByRef('inputname', $inputname);

        $defaultValue = $this->getParam('value');
        $this->setVarByRef('defaultValue', $defaultValue);

        $this->setLayoutTemplate(NULL);
        $this->setVar('pageSuppressBanner', TRUE);
        
        
        if ($showFullLinks) {
            return 'popup_showrealtimeimagewindow_tpl.php';
        } else {
            return 'popup_showimagewindow_tpl.php';
        }
    }

    /**
    * Method to show the FCKEditor Interface For Images
    */
    public function showFCKImages()
    {
        $restriction = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

        return $this->showFCKEditorInterface($restriction, 'fckimage');
    }

    /**
    * Method to show the FCKEditor Interface For Flash
    */
    public function showFCKFlash()
    {
        $restriction = array('swf');

        return $this->showFCKEditorInterface($restriction, 'fckflash');
    }


    /**
    * Method to show the FCKEditor Interface
    * @param array $restriction List of FileTypes to Restrict to.
    */
    public function showFCKEditorInterface($restriction=array(), $action='fcklink')
    {


        if ($this->getParam('mode') == 'fileupload') {
            $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
            $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());
        }

        $files = $this->objFiles->getUserFiles($this->objUser->userId(), NULL, $restriction, TRUE);

        $this->setVarByRef('files', $files);

        $this->setVar('modeAction', $action);

        // Script to Close Window automatically if opener does not exist
        $checkOpenerScript = '
<script type="text/javascript">
function checkWindowOpener()
{
    if (!window.opener) {
        window.close();
    }
}
</script>
        ';

        $this->appendArrayVar('headerParams', $checkOpenerScript);
        $this->appendArrayVar('bodyOnLoad', 'checkWindowOpener();');
        $this->appendArrayVar('bodyOnLoad', 'window.focus();');

        $inputname = $this->getParam('name');
        $this->setVarByRef('inputname', $inputname);

        $defaultValue = $this->getParam('value');
        $this->setVarByRef('defaultValue', $defaultValue);

        $this->setLayoutTemplate(NULL);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);
        return 'fckeditor_showfilewindow_tpl.php';
    }


    /**
    * Method to Handle Uploads From the File Selector Popup
    */
    public function selectFileUploads()
    {
        // Upload Files
        if ($this->getParam('restrict') == '') {
            $results = $this->objUpload->uploadFile('fileupload1');
        } else {
            $uploadRestrict = explode('____', $this->getParam('restrict'));
            $results = $this->objUpload->uploadFile('fileupload1', $uploadRestrict);
        }

        $settingsArray = array();
        $settingsArray['name'] = $this->getParam('name');
        $settingsArray['context'] = $this->getParam('context');
        $settingsArray['workgroup'] = $this->getParam('workgroup');
        $settingsArray['value'] = $this->getParam('value');
        $settingsArray['restrict'] = $this->getParam('restrict');

        
        // Check if no files were provided
        if ($results['errorcode'] == '4') {
            $settingsArray['error'] = 'nofilesprovided';
        } else {
            // Put Message into Array
            $messages = $this->objUploadMessages->processMessageUrl(array($results));

            $settingsArray = array_merge($settingsArray, $messages);

            $settingsArray['mode'] = 'fileupload';

            if (array_key_exists('success', $results) && $results['success']) {
                $settingsArray['value'] = $results['fileid'];
            }

            if (isset($results['overwrite']) && $results['overwrite']) {
                $settingsArray['overwrite'] = $results['overwrite'];
                $settingsArray['value'] = $results['fileid'];
            }
        }

        return $this->nextAction($this->getParam('mode', 'selectfilewindow'), $settingsArray);
    }

    /**
	 * Ovveride the login object in the parent class
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
    * Method to show a folder, and list of files in the folder.
    * @param string $id Record Id of the folder
    */
    function showFolder($id)
    {
        // TODO: Check permission to enter folder
        
        // Get Folder Details
        $folder = $this->objFolders->getFolder($id);
        
        if ($folder == FALSE) {
            return $this->nextAction(NULL);
        }
        
        $this->setVarByRef('folder', $folder);
        
        $this->setVarByRef('folderpath', basename($folder['folderpath']));
        
        $this->setVar('folderId', $id);
        
        $subfolders = $this->objFolders->getSubFolders($id);
        $this->setVarByRef('subfolders', $subfolders);
        
        $files = $this->objFiles->getFolderFiles($folder['folderpath']);
        $this->setVarByRef('files', $files);
        
        $objPreviewFolder =& $this->getObject('previewfolder');
        $this->setVarByRef('table', $objPreviewFolder->previewContent($subfolders, $files));
        
        $breadcrumbs = $this->objFolders->generateBreadcrumbsFromUserPath($this->objUser->userId(), $folder['folderpath']);
        $this->setVarByRef('breadcrumbs', $breadcrumbs);
        
        return 'showfolder.php';
    }
    
    /**
    * Method to create a folder
    */
    function createFolder()
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
        
        if ($parentId == 'ROOT') {
            $folderpath = 'users/'.$this->objUser->userId();
        } else {
            $folder = $this->objFolders->getFolder($parentId);
            
            if ($folder == FALSE) {
                return $this->nextAction(NULL, array('error'=>'couldnotfindparentfolder'));
            }
            $folderpath = $folder['folderpath'];
        }
        
        
        $this->objMkdir =& $this->getObject('mkdir', 'files');
        
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
    * @param string $id Record Id of the Folder
    */
    private function deleteFolder($id)
    {
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
        $call2 = $objBackground->setCallback("john.doe@tohir.co.za","Your Script","The really long running process that you requested is complete!");
        
        
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
    * Method to extract the contents of an archive
    */
    function extractArchive()
    {
        
        $archiveFileId = $this->getParam('file');
        
        $file = $this->objFiles->getFullFilePath($archiveFileId);
        
        if ($file == FALSE) {
            return $this->nextAction('viewfolder', array('folder'=>$this->getParam('parentfolder'), 'error'=>'couldnotfindarchive')); 
        } else {
        
            $parentId = $this->getParam('parentfolder');
            
            if ($parentId == 'ROOT') {
                $parentId = $this->objFolders->getFolderId('users/'.$this->objUser->userId());
            }
            
            $folder = $this->objFolders->getFullFolderPath($parentId);
            
            //echo $folder;
            
            $objBackground = $this->newObject('background', 'utilities');
            
            //check the users connection status,
            //only needs to be done once, then it becomes internal 
            $status = $objBackground->isUserConn();

            //keep the user connection alive, even if browser is closed!
            $callback = $objBackground->keepAlive(); 

            $objZip = $this->newObject('wzip', 'utilities');
            $objZip->unzip($file, $folder);
            
            $objIndexFileProcessor = $this->getObject('indexfileprocessor');
            $objIndexFileProcessor->indexFolder($folder, $this->objUser->userId());
            
            $call2 = $objBackground->setCallback("john.doe@tohir.co.za","Your Script","The really long running process that you requested is complete!"); 
            
            return $this->nextAction('viewfolder', array('folder'=>$parentId, 'message'=>'archiveextracted', 'archivefile'=>$archiveFileId)); 
        }
    }
    
    /**
    * Method to Scan the File System and Index files that are not in the database
    */
    protected function indexFiles()
    {
        $objBackground = $this->newObject('background', 'utilities');
            
        //check the users connection status,
        //only needs to be done once, then it becomes internal 
        $status = $objBackground->isUserConn();

        //keep the user connection alive, even if browser is closed!
        $callback = $objBackground->keepAlive(); 
        
        $objIndexFileProcessor = $this->getObject('indexfileprocessor');

        $list = $objIndexFileProcessor->indexUserFiles($this->objUser->userId());
        
        $this->setVarByRef('list', $list);
        
        $call2 = $objBackground->setCallback("john.doe@tohir.co.za","Your Script","The really long running process that you requested is complete!");
        
        return 'indexfiles_tpl.php';
    }
    
    /**
    * Method to edit the details of a file
    * @param string $id Record Id of the File
    */
    protected function editFileDetails($id)
    {
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
    * Method to update the details of a file
    */
    protected function updateFileDetails()
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
    * Method to show a tag cloud for a user's files
    */
    protected function showTagCloud()
    {
        $tagCloudItems = $this->objFileTags->getTagCloudResults($this->objUser->userId());
        $this->setVarByRef('tagCloudItems', $tagCloudItems);
        
        return 'tagcloud_tpl.php';  
    }
    
    /**
    * Method to view user's files for a tag
    * @param string $tag
    */
    protected function viewByTag($tag)
    {
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
        
        $objPreviewFolder =& $this->getObject('previewfolder');
        $table = $objPreviewFolder->previewContent(array(), $files);
        $this->setVarByRef('table', $table);
        
        return 'showfileswithtags_tpl.php';
    }
    
    /**
    * Method to get the thumbnail path of a file
    * @param string $id Record Id of the File
    */
    protected function getThumbnail($id)
    {
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
}

?>