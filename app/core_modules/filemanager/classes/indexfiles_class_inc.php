<?php

/**
* Class to use with viewsource to print linked files
*/
$this->loadClass('folderbot', 'files');
class indexfiles extends folderbot 
{

    /**
    * @var string $retStr The string to return
    */
    var $fileIndex;
    var $folderIndex;



    /**
    * Method to trigger a file event and add the file to the output
    * string.  Triggered by the parent class, it is called every 
    * time a file matching the filter has been found.
    */
    function file_event()
    {
        $this->fileIndex[] = $this->curfile;
    } 
    
    /**
    *  Called every time a folder is found. It just prints its name
    */
    function folder_event()
    {           
        $this->folderIndex[] = $this->curfile;
    }
    
    /**
    *
    *
    */
    function getIndex($directory)
    {
        $this->set_recurse(true); // set to false to only list the folder without subfolder.
        $this->scan($directory);
        
        return array($this->fileIndex, $this->folderIndex);
    }
} // end class
?>