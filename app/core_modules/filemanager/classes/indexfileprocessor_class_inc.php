<?php


class indexfileprocessor extends object 
{

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
    }

    function processIndexedFile($filePath, $mimetype='')
    {
        $this->objCleanUrl->cleanUpUrl($filePath);
        $savePath = $this->objConfig->getcontentBasePath().'/'.$filePath;
        $this->objCleanUrl->cleanUpUrl($savePath);
        
        $cleanFilename = $this->objCleanUrl->cleanFilename($filePath);
        $cleanFilenameSavePath = $this->objConfig->getcontentBasePath().'/'.$cleanFilename;
        $this->objCleanUrl->cleanUpUrl($cleanFilenameSavePath);;
        
        $renameAttempt = rename($savePath, $cleanFilenameSavePath);
        
        if ($renameAttempt == TRUE) {
            $filePath = $cleanFilename;
            $savePath = $cleanFilenameSavePath;
        }
        
        $filename = basename($filePath);
        
        if ($mimetype == '') {
            $mimetype = $this->objMimetype->getMimeType($filePath);
        }
        
        $category = $this->objFileFolder->getFileFolder($filename, $mimetype);
        
        $savePath = $this->objConfig->getcontentBasePath().'/'.$filePath;
        
        $this->objCleanUrl->cleanUpUrl($savePath);
        
        $fileSize = filesize($savePath);
        
        // 1) Add to Database
        $fileId = $this->objFile->addFile($filename, $filePath, $fileSize, $mimetype, $category);
        
        // 2) Start Analysis of File
        if ($category == 'images' || $category == 'audio' || $category == 'video' || $category == 'flash') {
            // Get Media Info
            $fileInfo = $this->objUpload->analyzeMediaFile($savePath);
            
            // Add Information to Databse
            $this->objMediaFileInfo->addMediaFileInfo($fileId, $fileInfo);
            
            // Create Thumbnail if Image
            // Thumbnails are not created for temporary files
            if ($category == 'images') {
                $this->objThumbnails->createThumbailFromFile($savePath, $fileId);
            }
        }
        
        return $category;
    }
} // end class
?>