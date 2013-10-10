<?php
/**
* etdfiles class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* etdfiles class for managing file uploads / downloads / creation - integrates with the filemanager and files classes
* @author Megan Watson
* @copyright (c) 2004 UWC
* @version 0.1
*/

class etdfiles extends dbtable
{
    /**
    * @var string $modulePath The base path for module content on the file system
    * @access private
    */
    private $modulePath;

    /**
    * @var string $filePath The path for etd file content on the file system
    * @access private
    */
    private $filePath;

    /**
    * @var string $fileExts The allowed extensions for the files
    * @access private
    */
    private $fileExts = 'doc,pdf,odt,txt,sxw,htm,html,jpg,jpeg,gif,png,xml';

    /**
    * Constructor
    */
    function init()
    {
        try{
            $this->objUpload =& $this->getObject('upload', 'files');
            $this->objMkdir =& $this->getObject('mkdir', 'files');
            $this->objConfig =& $this->getObject('altconfig', 'config');
            $this->dbFiles =& $this->newObject('dbfiles');
            
            $contentPath = $this->objConfig->getcontentBasePath();
            $path = $this->objConfig->getcontentPath();
    
            $this->modulePath = $contentPath.'modules/';
            $this->etdPath = $contentPath.'modules/etd/';
            $this->xmlPath = $contentPath.'modules/etd/xml/';
            $this->filePath = $contentPath.'modules/etd/docs/';
            $this->downloadPath = $path.'modules/etd/docs/';
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }

    /**
    * Method to upload a new document to the filemanager
    *
    * @access public
    * @param string $submitId The submission id
    * @param string $id The id of the current document
    * @return
    */
    public function uploadFile($submitId, $id = NULL)
    {
        $fileName = 'etd_'.$submitId;
        $this->checkDir($this->filePath);
        $this->objUpload->setUploadFolder($this->filePath);
        $this->objUpload->setAllowedTypes($this->fileExts);
        $this->objUpload->overWrite = TRUE;
        $results = $this->objUpload->doUpload(TRUE, $fileName);
        if($results['success']){
            $fileId = $this->dbFiles->addFile($submitId, $results, $id);
        }
        return $results['message'];
    }

    /**
    * Method to get a document
    *
    * @access public
    * @param string $submitId The submission id of the document
    * @return
    */
    public function getFile($submitId)
    {
        $data = $this->dbFiles->getFile($submitId);
        
        if(isset($data) && !empty($data)){
            $data[0]['filepath'] = $this->downloadPath;
            return $data;
        }
        return array();
    }

    /**
    * Method to create and save a file to the filesystem
    *
    * @access public
    * @param string $contents The file contents as a string
    * @param string $file The name of the file to save
    * @param string $ext The file extension
    * @return
    */
    public function createFile($contents, $file, $ext = '.xml')
    {
        $this->checkDir($this->xmlPath);

        $fp = fopen($this->xmlPath.$file.$ext, 'wb');
        if(!$fp){
            return FALSE;
        }
        if(fwrite($fp, $contents) === FALSE){
            fclose($fp);
            return FALSE;
        }
        fclose($fp);
    }

    /**
    * Method to return the path to a file on the filesystem for reading
    *
    * @access public
    * @return
    */
    public function getPath()
    {
        $this->checkDir($this->xmlPath);
        return $this->xmlPath;
    }

    /**
    * Method to delete the file
    *
    * @access public
    * @param string $file The file name
    * @param string $ext The file extension
    * @return
    */
    public function removeFile($file, $ext)
    {
        $this->checkDir($this->xmlPath);
        if($this->checkFile($this->xmlPath.$file, $ext)){
            unlink($this->xmlPath.$file.$ext);
        }
        return TRUE;
    }

    /**
    * Method to check a file exists
    *
    * @access public
    * @param string $file The file name
    * @param string $ext The file extension
    * @return bool True = file exists; False = file does not exist.
    */
    public function checkFile($file, $ext = '.xml')
    {
        if(file_exists($file.$ext)){
            return TRUE;
        }
        return FALSE;
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
        if(!is_dir($this->modulePath)){
            $this->objMkdir->fullFilePath = $this->modulePath;
            $this->objMkdir->makedir();
        }
        if(!is_dir($this->etdPath)){
            $this->objMkdir->fullFilePath = $this->etdPath;
            $this->objMkdir->makedir();
        }
        if(!is_dir($dir)){
            $this->objMkdir->fullFilePath = $dir;
            $this->objMkdir->makedir();
        }
    }
}
?>