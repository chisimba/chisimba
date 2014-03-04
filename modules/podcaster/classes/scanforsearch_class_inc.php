<?php

/**
 * Class to Scan Presentation Folder for deleting purposes
 *
 * In the webpresent module, each presentation is stored within its own folder
 * When a presentation is deleted, all associated files in that folder needs to be deleted.
 *
 * This class scans for all the files in that folder, and present them in an array to be deleted
 *
 * @author Tohir Solomons
 */

/**
 * Load the Base Class Which this class extends
 */
$this->loadClass('folderbot', 'files');

/**
 * Start of Class
 */
class scanforsearch extends folderbot
{

    /**
    * @var array $fileIndex Array holding all matched files
    */
    protected $fileIndex;

    /**
    * @var array $folderIndex Array holding all matched folders
    */
    protected $folderIndex;

    /**
    * Method to scan a directory
    * @param  string $directory Directory to Scan
    * @return array  An array containing list of files and folders
    */
    public function scanDirectory($directory)
    {
        $this->set_recurse(true); // set to false to only list the folder without subfolder.
        $this->scan($directory);

        return array('files'=>$this->fileIndex, 'folders'=>$this->folderIndex);
    }

    /**
    * Method to trigger a file event and add the file to the output
    * array.  Triggered by the parent class, it is called every
    * time a file has been found.
    */
    public function file_event()
    {
        $this->fileIndex[] = $this->curfile;
    }

    /**
    * Method to trigger a folder event and add thefolder to the output
    * array.  Triggered by the parent class, it is called every
    * time a folder has been found.
    */
    public function folder_event()
    {
        $this->folderIndex[] = $this->curfile;
    }

} // end class
?>