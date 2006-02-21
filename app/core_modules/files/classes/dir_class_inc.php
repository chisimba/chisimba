<?php
/**
* Class to help kbook by reading the folders and files
* 
* @author Derek Keats 
*/

class dir extends object 
{
    /**
    * 
    * @var string $retStr The string to return
    */
    var $dirAr=array();
    
    /**
    * @var string $returnWhat what to return, directories or files or both
    * Valid values are folders | files | both
    */
    var $returnWhat="folders";
    
    /**
    * @var int $folderCount The number of folders in the current folder
    */
    var $folderCount=0;
    /**
    * @var int $fileCount The number of file in the current folder
    */
    var $fileCount=0;
    /**
    * Standard init method, loads the language object
    */
    function init()
    {
        // Create an instance of the language object
        $this->objLanguage = &$this->getObject('language', 'language'); 
    }

    /**
    * Called every time a folder is found. It just returns its name
    *   and must be imlemented if using folder events in a child class
    */
    function getFoldersAsArray($dir) // FOR TESTING NOT RETURNING ARRAY YET
    {
        if (file_exists($dir)) {
            /* replace the windows backslash with unix slash which
               works on all systems */
            $dir=trim(str_replace("\\", "/", $dir));
            // Count the number of characters in the string
            $chars=strlen($dir);
            // Get the last character of the string
            $lastChar=trim(substr($dir, $chars-1, 1));
            // If the last characted is a slash OK, if not make it so Mr Data
            if ($lastChar != "/") {
                $dir .= "/";
            }
            // open current folder
            $handle = opendir($dir) 
                or die($this->objLanguage->languageText("err_errorreadingfolder")
                .": " . $dir);
            // Get the entries
            $f = null;
            $retStr = null;
            while ($f = readdir($handle)) {
                if ($f != "CVS" && $f != "." && $f != "..") {
                    $fPath=$dir.$f;
                    switch ($this->returnWhat) {
                        case null:
                        case "folders":
                            if (is_dir($fPath)) {
                                $this->dirAr[] = $f;
                            }
                        break;
                        case "files":
                            if (!is_dir($fPath)) {
                                $this->dirAr[] = $f;
                            }
                        break;
                        case "both":
                           $this->dirAr[] = $f;
                        break;
                    }
                    if (is_dir($fPath)) {
                        $this->folderCount=$this->folderCount+1;
                    } else {
                        $this->fileCount=$this->fileCount+1;
                    }
                } 
            } // while
            closedir($handle);
            return True;
        } else {
            die($this->objLanguage->languageText("err_errorreadingfolder"));
        } 
    } // end of fn
    
    /**
    * 
    * Method to sort the folders default ascending
    * @param string $sortorder THe sort order, either asc or desc
    * 
    */
    function sortFolderArray($sortorder="asc")
    {
        if ($sortorder="asc") {
            sort($this->dirAr);
        } else {
            rsort($this->dirAr);
        }
        
    }
    
    

    
    
} // end of class
