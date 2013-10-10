<?php

/**
 * Class to Scan Presentation Folder for Files
 *
 * When OpenOffice exports presentations to HTML, the filenames are formatted as:
 *
 * text[slidenumber].html
 *
 * This class scans for files in this format, and returns the list as an array.
 * This information can then be used in various ways.
 *
 * 1) text[maxslidenumber].html => maxslidenumber+1 = number of slides
 * 2) title of each html file is the title of the slide
 * 3) Content of html file after navigation is the content of the slide.
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
class scanpresentation extends folderbot
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
        $this->set_recurse(FALSE); // set to false to only list the folder without subfolder.
        $this->scan($directory);

        return $this->fileIndex;
    }

    /**
    * Method to trigger a file event and add the file to the output
    * array.  Triggered by the parent class, it is called every
    * time a file has been found.
    */
    public function file_event()
    {
        if (preg_match('%.*/text\d*\.html%', $this->curfile)) {
            $this->fileIndex[basename($this->curfile)] = $this->curfile;
        }
    }

    /**
    * Method to trigger a folder event and add thefolder to the output
    * array.  Triggered by the parent class, it is called every
    * time a folder has been found.
    */
    public function folder_event()
    {
        //$this->folderIndex[] = $this->curfile;
    }

} // end class
?>