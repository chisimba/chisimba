<?php

/**
* Class to check whether Class/Object file exists.
*
* In the Chisimba framework, classes exist in a specified file format in modules.
* This class checks whether the FILE exist
* It does not test/throw exceptions whether the object has been/can be instantiated.
* @author Tohir Solomons
*/
class checkobject extends object
{
    
    /**
    * Standard Constructor
    */
    public function init()
    { }
    
    /**
    * Method to check whether an objects file exists
    * @param string $name Name of the Class
    * @param string $module Name of the Module to look in for the class
    * @return boolean TRUE if file is found else FALSE
    */
    public function objectFileExists($name, $moduleName)
    {
        if ($moduleName == '_core') {
            $filename = "classes/core/".strtolower($name)."_class_inc.php";
        } else {
            // Check in Config folder if module is gives as _site_
            if ($moduleName == '_site_') {
                $moduleName = 'config';
            }
            $filename = "modules/".$moduleName."/classes/".strtolower($name)."_class_inc.php";
        }
        
        $filename = strtolower($filename);
        
        if (file_exists($filename)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

?>