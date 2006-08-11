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
        $this->objFileOverwrite =& $this->getObject('checkoverwrite');
        $this->objCleanUrl =& $this->getObject('cleanurl');
        $this->objUpload =& $this->getObject('upload');
        $this->objFilePreview =& $this->getObject('filepreview');
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objUser =& $this->getObject('user', 'security');
        
        $this->objUploadMessages =& $this->getObject('uploadmessages');
        
        $this->objLanguage =& $this->getObject('language', 'language');
        
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('xajax', 'xajaxwrapper');
        $this->loadClass('xajaxresponse', 'xajaxwrapper');
    }
    
    /**
	* Method to process actions to be taken
    *
    * @param string $action String indicating action to be taken
	*/
    public function dispatch($action)
    {
        $this->setLayoutTemplate('filemanager_layout_tpl.php');
        
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
            default: 
                return $this->filesHome();
        }
    }
    
    /**
    * Method to Show the File Manager Home Page
    */
    public function filesHome()
    {
        $category = $this->getParam('category', NULL);
        $filter = $this->getParam('filter', NULL);
        
        $categories = array('images', 'audio', 'video', 'documents', 'flash', 'freemind', 'archives', 'other', 'obj3d', 'scripts');
        
        
        if (!in_array($category, $categories)) {
            $category = NULL;
        }
        
        $listFiles = $this->objFiles->getUserFiles($this->objUser->userId(), $category);
        $this->setVarByRef('files', $listFiles);
        
        $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
        $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());
        
        if ($category == '') {
            $category = 'files';
        }
        $this->setVar('category', $category);
        
        switch ($category)
        {
            case 'images':
                return 'list_images.php';
            default:
                return 'list_files.php';
        }
    }
    
    /**
    * Method to download a file
    * @param string $id Record Id of the File
    * @param string $filename Filename of the File
    */
    public function showFile($id, $filename)
    {
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
        
        return 'fileinfo_tpl.php';
    }
    
    /**
    * Method to Handle Uploads
    */
    public function handleUploads()
    {
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
        
        return $this->nextAction('uploadresults', $messages);
    }
    
    /**
    * Method to Show the Results of File Uploads
    */
    public function showUploadResults()
    {
        
        $this->setVar('successMessage', $this->objUploadMessages->processSuccessMessages());
        $this->setVar('errorMessage', $this->objUploadMessages->processErrorMessages());
        
        return 'list_uploadresults_tpl.php';
    }
    
    /**
    * Method to show the File Overwrite Checker
    */
    public function checkFileOverwrite()
    {
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
    
    function showMultiDelete()
    {
        
        
        return 'multidelete_form_tpl.php';
    }
    
    function multiDeleteConfirm()
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
    
    function selecttest()
    {
        $objSelectFile = $this->getObject('selectfile', 'filemanager');
        $objSelectFile->restrictFileList = array('htm');
        echo $objSelectFile->show();
    }
    
    function showFileWindow()
    {
        
        $files = $this->objFiles->getUserFiles($this->objUser->userId(), NULL, explode('____', $this->getParam('restrict')), TRUE);
        
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
        
        $xajax = new xajax($this->uri(array('action'=>'selectfilewindow')));
        $xajax->registerFunction(array($this, 'generatePreview')); // Register another function in this controller
        $xajax->processRequests(); // XAJAX method to be called
        $this->appendArrayVar('headerParams', $xajax->getJavascript()); // Send JS to header
        
        $inputname = $this->getParam('name');
        $this->setVarByRef('inputname', $inputname);
        
        $this->setLayoutTemplate(NULL);
        $this->setVar('pageSuppressToolbar', TRUE);
        return 'popup_showfilewindow_tpl.php';
    }
    
    function generatePreview($fileId, $jsValue)
    {
        $objResponse = new xajaxResponse();
        
        $file = $this->objFiles->getFileInfo($fileId);
        
        if ($file == FALSE) {
            $objResponse->addAlert('No Such File Exists');
        } else {
            
            $link = new link("javascript:selectFile('".$fileId."', ".$jsValue.");");
            $link->link = 'Select';
            
            $content = '<br /><br />';
            $content .= '<h1>Preview of: '.$file['filename'].' ('.$link->show().')</h1>';
            $content .= $this->objFilePreview->previewFile($fileId);
            
            $objResponse->addAssign('previewwindow', 'innerHTML', $content);
            //$objResponse->addAlert('Hello');
        }
        
        
        return $objResponse->getXML();
    }
}

?>