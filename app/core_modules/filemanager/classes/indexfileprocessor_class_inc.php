<?php

/**
* Class to index files not yet stored in the database
*
* This class scans the filesystem for files
* @author Tohir Solomons
*/
class indexfileprocessor extends object 
{

    /**
    * Constructor
    */
    function init()
    {
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objMimetype = $this->getObject('mimetypes', 'files');
        $this->objFile =& $this->getObject('dbfile');
        $this->objMediaFileInfo =& $this->getObject('dbmediafileinfo');
        $this->objFileFolder =& $this->getObject('filefolder');
        $this->objCleanUrl =& $this->getObject('cleanurl');
        $this->objUpload =& $this->getObject('upload');
        $this->objThumbnails =& $this->getObject('thumbnails');
        $this->objIndexFiles = $this->getObject('indexfiles');
        $this->objAnalyzeMediaFile =& $this->getObject('analyzemediafile');
    }
    
    /**
    * Method to Scan and index the files of a user
    * @param string $userId User Id whose folder should be scanned
    * @return array List of Files that were indexed
    */
    function indexUserFiles($userId='1')
    {
        $results = $this->objIndexFiles->scanDirectory($this->objConfig->getcontentBasePath().'users/'.$userId.'/');

        $files = $results[0];
        
        $indexedFiles = array();

        if (count($files) > 0) {
            foreach ($files as $file)
            {
                    preg_match('/(?<=usrfiles(\\\|\/)).*/', $file, $regs);
                	$path = $regs[0];
                    $this->objCleanUrl->cleanUpUrl($path);
                    
                    $record = $this->objFile->getFileDetailsFromPath($path);
                    
                    if ($record == FALSE) {
                        $indexedFiles[] = $this->processIndexedFile($path, $userId);
                    }
            }
        }
        
        return $indexedFiles;
    }

    /**
    * Method to take a file that is not in the index, process its data
    * and add it to the database
    * @param string $filePath Path to File
    * @param string $userId UserId of the Person to whom the file should belong to
    * @param string $mimetype Mimetype of the File (Optional)
    * @return string File Id
    */
    function processIndexedFile($filePath, $userId, $mimetype='')
    {
        // Clean Up the File Path
        $this->objCleanUrl->cleanUpUrl($filePath);
        // Create the Full Path to the File
        $savePath = $this->objConfig->getcontentBasePath().'/'.$filePath;
        // Clean up the Full Path to the File
        $this->objCleanUrl->cleanUpUrl($savePath);
        
        
        // Take filename, and create cleaned up version (no punctuation, etc.)
        $cleanFilename = $this->objCleanUrl->cleanFilename($filePath);
        // Create the Full Path to the File based on cleaned up filename
        $cleanFilenameSavePath = $this->objConfig->getcontentBasePath().'/'.$cleanFilename;
        // Clean up the Full Path to the File based on cleaned up filename
        $this->objCleanUrl->cleanUpUrl($cleanFilenameSavePath);
        
        // Attempt Rename
        $renameAttempt = rename($savePath, $cleanFilenameSavePath);
        
        // If rename is successful, swop dirty filename with clean one for database
        if ($renameAttempt == TRUE) {
            $filePath = $cleanFilename;
            $savePath = $cleanFilenameSavePath;
        }
        
        // Determine filename
        $filename = basename($filePath);
        
        // Get mimetype if not given
        if ($mimetype == '') {
            $mimetype = $this->objMimetype->getMimeType($filePath);
        }
        
        // Get Category
        $category = $this->objFileFolder->getFileFolder($filename, $mimetype);
        
        // File Size
        $fileSize = filesize($savePath);
        
        // 1) Add to Database
        $fileId = $this->objFile->addFile($filename, $filePath, $fileSize, $mimetype, $category, '1', $userId);
        
        // 2) Start Analysis of File
        if ($category == 'images' || $category == 'audio' || $category == 'video' || $category == 'flash') {
            // Get Media Info
            $fileInfo = $this->objAnalyzeMediaFile->analyzeFile($savePath);
            
            // Add Information to Databse
            $this->objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo[0]);
            
            // Check if alternative mimetype is provided
            if ($fileInfo[1] != '') {
                $this->objFile->updateMimeType($fileId, $fileInfo[1]);
            }
            
            // Create Thumbnail if Image
            // Thumbnails are not created for temporary files
            if ($category == 'images') {
                $this->objThumbnails->createThumbailFromFile($savePath, $fileId);
            }
        }
        
        return $fileId;
    }
} // end class
?>