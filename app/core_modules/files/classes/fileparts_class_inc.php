<?php
/**
* Class to return parts from a file name
* @author Derek Keats
*
*/
class fileparts extends object
{

    /**
    * Standard init function
    */
    function init()
    {
    }
    
    /**
    *
    * Method to return the extension when passed a file name.
    * It works on the principle that the extension is any asci
    * characters after the last . in the filename.
    *
    * @param string $fName The file name from which to extract the extension
    *
    */
    function getExtension($fName)
    {
        //explode into an array by .
        $ext = explode (".", basename($fName));
        //Count array elements and subtract 1 due to 0th element being first
        $elem = (count($ext) - 1);
        //Return the last element which is the extension
        return $ext[$elem];
    
    }
 
}
?>