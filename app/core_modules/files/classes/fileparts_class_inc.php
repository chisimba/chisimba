<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 3
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */


/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
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