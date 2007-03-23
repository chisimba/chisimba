<?
/**
* Check whether User Folder for Upload Exists
*
* This class checks whether a user folder exists for file uploads
* It also creates a number of subfolders to store files in 
* dependent on file category.
*
* @author Tohir Solomons
* @package filemanager
*/
class userfoldercheck extends object
{
    /**
    * @var array $subFolders List of Possible Subfolders for storing files
    */
    private $subFolders;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objMkdir =& $this->getObject('mkdir', 'files');
        
        // List of Subfolders to be created in Path
        // $this->subFolders = array('images', 'audio', 'video', 'documents', 'flash', 'freemind', 'archives', 'temp', 'other', 'obj3d', 'scripts');
    }
    
    /**
    * Method to check that the user folder for uploads, and subfolders exist
    * @param string $userId UserId of the User
    */
    public function checkUserFolder($userId)
    {
        if (trim($userId) == '') {
            return FALSE;
        } else {
            // Set Up Path
            $path = $this->objConfig->getcontentBasePath().'/users/'.$userId;
            
            //foreach ($this->subFolders as $folder)
            //{
                $result = $this->objMkdir->mkdirs($path, 0777);
            //}
            
            return $result;
        }
    }
    
    /**
    * Method to check whether a version subfolder exists
    * This will usually be something like images/1, etc.
    *
    * @param string $userId UserId of the User
    * @param string $folder Name of the Folder
    * @param int $version Version of the Subfolder
    */
    public function checkUserFolderVersionExists($userId, $folder, $version)
    {
        // Take global array and inverse
        $subFolders = array_flip($this->subFolders);
        unset($subFolders['temp']); // Remove Temp
        $subFolders = array_flip($subFolders); // Inverse back
        
        if (in_array($folder, $subFolders)) {
            // Set Up Path
            $path = $this->objConfig->getcontentBasePath().'/users/'.$userId.'/'.$folder.'/'.$version;
            
            return $this->objMkdir->mkdirs($path);
        } else {
            return FALSE;
        }
    }
    
    
    

}

?>