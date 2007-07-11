<?php
/**
* Class to delete files and folders
* 
* @author Derek Keats 
*/

class del extends object 
{

    /**
    * 
    * @var string $fullFilePath The file including its path, 
    *  or the folder if deleting a folder.
    * 
    */
    var $fullFilePath;
    
    /**
    * 
    * @var boolean $err TRUE|FALSE; TRUE if error, else false
    * 
    */
    var $err;
    
    /**
    * 
    * @var string $errMst The error message
    * 
    */
    var $errMsg;
    
    /**
    * 
    * @var object $objLanguage String to hold the language object
    * 
    */
    var $objLanguage;
    
    /**
    * 
    * Standard init method to set the default values for
    * the properties.
    * 
    */
    function init()
    {
        $this->fullFilePath = NULL;
        $this->err = NULL;
        $this->errMsg = NULL;
        // Create an instance of the language object
        $this->objLanguage = &$this->getObject('language', 'language'); 
    }
    
	/**
    * 
    * Delete the file specified in $fullFilePath
    * inline {@internal checks the OS php is running on, and execute appropriate command}}
    * @access Public
    * @param string $type file | folder if its a file or a folder
    * @return bool TRUE | FALSE, sets error if error occurs
    * 
	*/
    function delete()
    {
        if ($this->fullFilePath != NULL) {
            //Check if its a file
            if (is_file($this->fullFilePath)) {
                //DO the unlink and check for success
                if (unlink( $this->fullFilePath )) {
                    return TRUE;
                } else {
                    $this->err = TRUE;
                    $this->errMsg = $this->objLanguage->languageText('mod_files_err_unable2del')
                      . ": " . $this->fullFilePath;
                }
            } else {
                $this->err = TRUE;
                $this->errMsg = $this->objLanguage->languageText('mod_files_err_isdirnotfile')
                  . ": " . $this->fullFilePath;
            }
        //If the file path is null
        } else {
            $this->err = TRUE;
            $this->errMsg = $this->objLanguage->languageText('mod_files_err_nofilepath') 
              . ": " . $this->fullFilePath;
            return FALSE;
        }
    } //function
    
} // end of class
?>