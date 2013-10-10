<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_internalmail_new
 * @author Kevin Cyster
 */
class emailfiles extends dbTable
{
    /**
     * @var string $userId The userId of the current user
     * @access private
     */
    private $userId;

    /**
     * @var string $modulePath The base path for all module content on the file system
     * @access private
     */
    private $modulePath;

    /**
     * @var string $filePath The path for email file content on the file system
     * @access private
     */
    private $filePath;

    /**
     * @var string $tempPath The path for temporary email file content on the file system
     * @access private
     */
    protected $tempPath;

    /**
     * @var integer $maxFileSize The maximum size for file attachments in bytes
     * @access private
     */
    private $maxFileSize;

    /**
     * Method to construct the class.
     *
     * @access public
     * @return
     */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objUpload = $this->newObject('upload', 'files');
        $this->objMkdir = $this->newObject('mkdir', 'files');
        $this->objConfig = $this->newObject('altconfig', 'config');
        $this->dbAttachments = $this->newObject('dbattachments');
        //        $this->dbEmail =& $this->newObject('dbemail');
        $contentBasePath = $this->objConfig->getcontentBasePath();
        $contentPath = $this->objConfig->getcontentPath();
        $this->modulePath = $contentBasePath.'modules/';
        $this->filePath = $contentBasePath.'modules/internalmail/';
        $this->tempPath = $contentBasePath.'modules/internalmail/'.$this->userId.'/';
        $this->downloadLocation = $contentPath.'/modules/internalmail/'.$this->userId.'/';
        // set upload size
        $this->objSysconfig = $this->newObject('dbsysconfig', 'sysconfig');
        $this->uploadSize = $this->objSysconfig->getValue('ATTACHMENT_MAX_SIZE', 'internalmail');
        $attachmentSize = substr($this->uploadSize, 0, -1);
        $maxPost = substr(ini_get('post_max_size') , 0, -1);
        $maxUpload = substr(ini_get('upload_max_filesize') , 0, -1);
        $maxSize = min($attachmentSize, $maxPost, $maxUpload);
        $this->maxFileSize = $maxSize*1048576;
		$this->objCleanUrl = $this->newObject('cleanurl','filemanager');
    }

    /**
     * Method to upload a new document to the filemanager
     *
     * @access public
     * @param string $submitId
     * @return
     */
    public function uploadFile()
    {
        $this->checkDir($this->tempPath);
        $this->objUpload->setUploadFolder($this->tempPath);
        $this->objUpload->overWrite = TRUE;
        $this->objUpload->maxSize = $this->maxFileSize;
        $this->objUpload->setAllowedTypes('all');
        $this->objUpload->inputname = 'attachment';
        $results = $this->objUpload->doUpload(TRUE);
        if ($results['success']) {
            $this->addAttachment($results);
        }
        return $results;
    }

    /**
     * Method to check the directory path and create the directories if required
     *
     * @access private
     * @param string $dir The directory path
     * @return
     */
    private function checkDir($dir)
    {
        if (!is_dir($this->modulePath)) {
            $this->objMkdir->fullFilePath = $this->modulePath;
            $this->objMkdir->makedir();
        }
        if (!is_dir($this->filePath)) {
            $this->objMkdir->fullFilePath = $this->filePath;
            $this->objMkdir->makedir();
        }
        if (!is_dir($dir)) {
            $this->objMkdir->fullFilePath = $dir;
            $this->objMkdir->makedir();
        }
    }

    /**
     * Method to set session data
     *
     * @access public
     * @param array $fileData The array of file details
     * @return
     */
    public function addAttachment($fileData)
    {
        $sessionData = $this->getSession('attachments');
        if ($sessionData != NULL) {
            $sessionData[] = $fileData;
            $this->setSession('attachments', $sessionData);
        } else {
            $sessionData[] = $fileData;
            $this->setSession('attachments', $sessionData);
        }
    }

    /**
     * Method to get session data
     *
     * @return array $attachments
     */
    public function getAttachments()
    {
        $attachments = $this->getSession('attachments');
        return $attachments;
    }

    /**
     * Method to delete and attchment before the email is sent
     *
     * @access public
     * @param string $filename The name of the file to remove
     * @return
     */
    public function deleteAttachment($filename)
    {
        $attachments = $this->getSession('attachments');
        if ($attachments != NULL) {
            foreach($attachments as $key => $attachment) {
                if ($attachment['filename'] == $filename) {
                    unset($attachments[$key]);
                    $this->setSession('attachments', $attachments);
                    if (file_exists($filename)) { 
                        unlink($this->tempPath.$filename);
                    }
                }
            }
        }
    }

    /**
     * Method to save the attachments and move the file from the temp directory
     *
     * @access public
     * @param string $emailId The id of the email the attachments belong to
     * @return
     */
    public function saveAttachments($emailId)
    {
        $attachments = $this->getAttachments($emailId);
        if ($attachments != NULL) {
            foreach($attachments as $attachment) {
                $attachmentId = $this->dbAttachments->addAttachments($emailId, $attachment);
                rename($this->tempPath.$attachment['filename'], $this->filePath.$attachmentId);
            }
            $attachmentCount = count($attachments);
        }else{
            $attachmentCount = 0;
        }
        $this->clearAttachments();
        return $attachmentCount;
    }

    /**
     * Method to clean up attachments
     *
     * @access public
     * @return
     */
    public function clearAttachments()
    {
        if (glob($this->tempPath.'*') != FALSE) {
            foreach(glob($this->tempPath.'*') as $filename) {
                unlink($filename);
            }
        }
        $this->unsetSession('attachments');
    }

    /**
     * Method to download the attachment
     *
     * @access private
     * @param string $attachId The id of the file to download
     * @return data $fileData The file data
     */
    public function outputFile($attachId)
    {
        $fileData = $this->dbAttachments->getAttachment($attachId);
        $name = $fileData[0]['file_name'];
        $store = $fileData[0]['stored_name'];
        copy($this->filePath.$store, $this->tempPath.$name);
        $filePath = $this->downloadLocation.$name;
        $this->objCleanUrl->cleanUpUrl($filePath);
        header("Location:{$filePath}");
    }

    /**
     * Method to create temporary attachment files from existing attachments
     *
     * @access public
     * @param string $emailId The id of the email
     * @return
     *
     */
    public function createAttachments($emailId)
    {
        $attachments = $this->dbAttachments->getAttachments($emailId);
        if ($attachments) {
            foreach($attachments as $attachment) {
                $name = $attachment['file_name'];
                $type = $attachment['file_type'];
                $size = $attachment['file_size'];
                $store = $attachment['stored_name'];
                copy($this->filePath.$store, $this->tempPath.$name);
                $sessionData = array();
                $sessionData['filename'] = $name;
                $sessionData['mimetype'] = $type;
                $sessionData['filesize'] = $size;
                $this->addAttachment($sessionData);
            }
        }
    }

    /**
     * Method to prepare a file that was not uploaded as an attachment
     *
     * @access public
     * @param string $file The file that is to be attached
     * @param string $filename The name the file is to be attached as e.g. myfile.xls attached as newfile.xls
     * @param string $filetype The mimetype of the file
     * @return
     */
    public function prepareAttachment($file, $filename = NULL, $filetype = NULL)
    {
        $this->checkDir($this->tempPath);
        $sessionData = array();
        $sessionData['filesize'] = filesize($file);
        if ($filetype != NULL) {
            $sessionData['mimetype'] = $filetype;
        } else {
            $name = basename($file);
            $fileArray = explode('.', $name);
            $ext = array_pop($fileArray);
            $sessionData['mimetype'] = $ext;
        }

        if ($filename != NULL) {
            rename($file, $this->tempPath.$filename);
            $sessionData['filename'] = $filename;
        } else {
            rename($file, $this->tempPath.basename($file));
            $sessionData['filename'] = basename($file);
        }
        $this->addAttachment($sessionData);
    }
}
?>
