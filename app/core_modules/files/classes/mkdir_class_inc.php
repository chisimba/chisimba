<?php
/**
* 
* Class to make a directory & manage errors 
* in doing so
* 
* @author Derek Keats 
*/

class mkdir extends object 
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
    * Create a folder. Needs fullFilePath to be set
    * to a valid directory before hand
    * 
    * @access Public
    * 
	*/
    function makedir()
    {
        if ($this->fullFilePath != NULL) {
            //Check if its a file
            if (is_file($this->fullFilePath)) {
                $this->err = TRUE;
                $this->errMsg = $this->objLanguage->languageText('mod_files_err_isfilenotdir')
                  . ": " . $this->fullFilePath;
            } else {
                mkdir($this->fullFilePath);
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
