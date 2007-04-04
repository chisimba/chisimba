<?php
/**
* Method to
* 
* Adapted to KEWL.NextGen framework by Derek Keats from
* code by Jean-Georges Estiot <jg@sto.com.au>
* and obtained from PHPCLASSES http://www.phpclasses.org/
* 
* The following text is from JG's description on PHP classes.
* 
* It goes through a given folder path triggerring class-based events. 
* For example, every time a new folder is found, the folder_event() 
* method is called. Whatever you have made folder_event() do will 
* happen in your program. The intent was to use folderbot as a base 
* class for folder-based jobs and extend it functionality into derived 
* classes which custom-handle the events.
* 
* The core functionality that I intended to encapsulate in the class 
* is the following:
* - automatic detection of the folder entries . and .. (they are just skipped)
* - switcheable recursion. You can do the current folder as well as all sub-folders if required.
* - file filtering
* 
* Basically, what this class could do for you is shield you from the process 
* of writing code to list files in a folder.
*/

class folderbot extends object 
{
    /**
    * 
    * @var boolean $recurse True | False whether to recurse directories 
    * or not
    */
    var $recurse;

    /**
    * 
    * @var string $curfile
    */
    var $curfile;

    /**
    * Standard framework init method for the KEWL.NextGen framework
    * Sets the default recursion to true
    */
    function init()
    {
        $this->recurse = true;
    } 

    /**
    * Method to set folder recursion
    * 
    * @param boolean $how True | False whether or not to recurse
    */
    function set_recurse($how = true)
    {
        $this->recurse = $how;
    }
    
    
    /**
    * Method to scan the supplied path
    * 
    * @param string $path The path to be scanned
    */
    function scan($path)
    {
        $stack = array();
        array_push($stack, realpath($path)); // save current path onto stack
        
        $currentDir = getcwd();
        
        while ($path = array_pop($stack)) { // as long as there are paths on stack
            chdir($path); // make it current path
            $handle = opendir($path); // open current folder
            if (!$handle) {
                return 0; // this normally happens when you don't have permission to the folder
            } 
            // reset($stack);	This is left here from my debugging :)
            while (($this->curfile = readdir($handle)) != false) { // as long as there are files in the folder
                if (!strcmp(".", $this->curfile))continue; // this may not the most efficient way to detect the . and .. entries
                if (!strcmp("..", $this->curfile))continue; // but it is the easiest to understand
                if (!strcmp("CVS", $this->curfile))continue; # ignore CVS folders
                if (!strcmp("config", $this->curfile))continue; # do not display config folder
                $this->curfile = realpath($this->curfile); //using relative path would lose the folders nested more than 1 level deep
                if (is_dir($this->curfile)) { // if a folder
                    if ($this->recurse)array_push($stack, $this->curfile); // we push it onto the stack if we are recursing
                    $this->folder_event(); // and we trigger an event
                } else {
                    if ($this->is_match())$this->file_event(); // if match, we trigger the file event
                } 
            } //while files in folder
            closedir($handle); // tidy up :)
        } // while stack not empty
        
        chdir($currentDir);
    } 
    
    /**
    * 
    * you'll set this function in your extended class to filter the files. 
    * By default it takes everything
    * 
    */
    function is_match()
    {
        return true; 
    } 
    
    /**
    * 
    * file event. It will be called every time a file matching the filter has been found
    * the full path/filename is in $this->curfile
    *
    */
    function file_event()
    {
    }
    
    /**
    * 
    * folder event. same as the file_event() but with folder names. Note that you 
    * cannot filter folders in this version.
    *
    */
    function folder_event()
    {
    }
     
}

?>