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
            case 'testgetid3':
                return $this->testGetId3();
            case 'indexfiles':
                return 'indexfiles_tpl.php';
            case 'indexfolders':
                return 'indexfolders_tpl.php';
            case 'viewfolder':
                return $this->showFolder($this->getParam('folder'));
            case 'createfolder':
                return $this->createFolder();
            default:
                return $this->filesHome();
        }
    }
    
    public function filesHome()
    {
        // Get Folder Details
        $folderpath = 'users/'.$this->objUser->userId();
        
        $folderId = $this->objFolders->getFolderId($folderpath);
        
        if ($folderId == FALSE) {
            $objIndexFileProcessor = $this->getObject('indexfileprocessor');

            $list = $objIndexFileProcessor->indexUserFiles($this->objUser->userId());
        
        }
        
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
        $listItem = explode('|', $this->getParam('listitems'));

        $resultInfo = '';
        $divider = '';



        foreach ($listItem as $item)
        {
            // Fix for HTTP headers. Input with dot in name gets converted to underscore.
            // if (substr_count($item, '.') > 0) {
                // $option = str_replace('.', '_', $_POST[$item]);
                // $rename = str_replace('.', '_', $_POST['rename__'.$item]);
                // $ext = str_replace('.', '_', $_POST['extension__'.$item]);
            // } else {
                $option = $_POST[$item];
                // $rename = $_POST['rename__'.$item];
                // $ext = $_POST['extension__'.$item];
            //}

            $fileInfo = $this->objFiles->getFileInfo($item);

            $resultInfo .= $divider.$fileInfo['filename'].'----'.trim($option);

            switch (trim($option))
            {
                case 'deletetemp':
                    $this->objFiles->deleteTemporaryFile($item);
                    break;
                case 'overwrite':
                    $this->objFileOverwrite->overWriteFile($item);
                    break;
                case 'rename':
                    $resultInfo .= '----'.$rename;
                    break;
                default:
                    break;
            }

            $divider = '--------';
        }


        return $this->nextAction('checkoverwrite', array('result'=>$resultInfo));
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
        if ($this->getParam('files') == NULL || !is_array($this->getParam('files')) || count($this->getParam('files')) == 0) {
            return $this->nextAction(NULL, array('message'=>'nofilesconfirmedfordelete'));
        } else {
            $files = $this->getParam('files');
            foreach ($files as $file)
            {
                $fileDetails = $this->objFiles->getFile($file);
                if ($fileDetails['userid'] = $this->objUser->userId()) {
                    $this->objFiles->deleteFile($file, TRUE);
                }
            }

            return $this->nextAction(NULL, array('message'=>'filesdeleted'));
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
    *
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

    function testGetId3()
    {
        $this->objUpload->analyzeMediaFile('/opt/lampp/htdocs/testing_playground/juliet_interview.mp3');
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
    
    function showFolder($id)
    {
        // TODO: Check permission to enter folder
        
        // Get Folder Details
        $folder = $this->objFolders->getFolder($id);
        
        if ($folder == FALSE) {
            return $this->nextAction(NULL);
        }
        
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
    *
    */
    function createFolder()
    {
        // echo '<pre>';
        // print_r($_POST);
        
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
}

?>