<?
/**
 * Adaptor Pattern around the PEAR::Config Object
 * This class will provide the kng configuration to Engine
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
        $this->name = 'fileupload';
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objMkdir =& $this->getObject('mkdir', 'files');
        
        // List of Subfolders to be created in Path
        $this->subFolders = array('images', 'audio', 'video', 'documents', 'flash', 'freemind', 'archives', 'temp', 'other', 'obj3d', 'scripts');
    }
    
    /**
    * Method to check that the user folder for uploads, and subfolders exist
    * @param string $userId UserId of the User
    */
    public function checkUserFolder($userId)
    {
        // Set Up Path
        $path = $this->objConfig->getcontentBasePath().'/users/'.$userId;
        
        foreach ($this->subFolders as $folder)
        {
            $result = $this->objMkdir->mkdirs($path.'/'.$folder);
        }
        
        return $result;
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