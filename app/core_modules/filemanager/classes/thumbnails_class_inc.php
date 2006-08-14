<?
/**
* Class to Generate Thumbnails for the File Manager
*
* This class packages functionality to generate thumbnails for images that are uploaded.
* It is separated from the image resize class as it here it checks for folder existence.
*
* @author Tohir Solomons
* @package filemanager
*/
class thumbnails extends object
{

    /**
    * Constructor
    */
    public function init()
    {
        $this->name = 'fileupload';
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objMkdir =& $this->getObject('mkdir', 'files');
        $this->objImageResize =& $this->getObject('imageresize', 'files');
        $this->objFileParts =& $this->getObject('fileparts', 'files');
        $this->objCleanUrl = $this->getObject('cleanurl');
    }
    
    /**
    * Method to check that the user folder for uploads, and subfolders exist
    * @param string $userId UserId of the User
    * @return boolean True if folder exists, else False
    */
    public function checkThumbnailFolder()
    {
        // Set Up Path
        $path = $this->objConfig->getcontentBasePath().'\filemanager_thumbnails';
        
        // Check if Folder exists, else create it
        $result = $this->objMkdir->mkdirs($path);
        
        return $result;
    }
    
    /**
    * Method to create Thumbnail
    * @param string $filepath Path to File
    * @param string $fileId Record Id of the path
    */
    public function createThumbailFromFile($filepath, $fileId)
    {
        // Check if folder exists
        $this->checkThumbnailFolder();
        
        // Send Image to Resize Class
        $this->objImageResize->setImg($filepath);
        
        // Resize to 100x100 Maintaining Aspect Ratio
        $this->objImageResize->resize(100, 100, TRUE);
        
        //$this->objImageResize->show(); // Uncomment for testing purposes
        
        // Determine filename for file
        // If thumbnail can be created, give it a unique file name
        // Else resort to [ext].jpg - prevents clutter, other files with same type can reference this one file
        if ($this->objImageResize->canCreateFromSouce) {
            $img = $this->objConfig->getcontentBasePath().'/filemanager_thumbnails/'.$fileId.'.jpg';   
        } else {
            $img = $this->objConfig->getcontentBasePath().'/filemanager_thumbnails/'.$this->objImageResize->filetype.'.jpg';
        }
        
        // Save File
        return $this->objImageResize->store($img);
    }
    
    /**
    * Method to get the thumbnail for a file
    * @param string $fileId Record Id of the File
    * @param string $filename Filename of the file
    * @return string Path to the thumbnail or False
    */
    public function getThumbnail($fileId, $filename)
    {
        $extension = $this->objFileParts->getExtension($filename);
        
        if (file_exists($this->objConfig->getcontentPath().'/filemanager_thumbnails/'.$fileId.'.jpg')) {
            
            $url = $this->objConfig->getcontentPath().'/filemanager_thumbnails/'.$fileId.'.jpg';
            $this->objCleanUrl->cleanUpUrl($url);
            
            return $url;
        } else if (file_exists($this->objConfig->getcontentPath().'/filemanager_thumbnails/'.$extension.'.jpg')){
        
            $url = $this->objConfig->getcontentPath().'/filemanager_thumbnails/'.$extension.'.jpg';
            $this->objCleanUrl->cleanUpUrl($url);
            
            return $url;
            
        } else {
            return FALSE;
        }
    }
    
    
    

}

?>