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
 * @version   CVS: $Id$
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
class filegetter
{
    /**
    * Method to load a file into a string when supplied with the full
    * file path to the file
    *
    * @param string $file the file to open and get
    */
    function getFileToString($file)
    {
        if (file_exists($file)) {
            //read it into a string and return the string
            $fp = fopen($file, "r")
                or die("fopen failed");   /* the file_exists should
                                             prevent this error but trap
                                             it anyway */
            $contents = fread($fp, filesize($file));
            fclose($fp);
            return $contents;
        } else {
            return False;
        }
    }
} // end class

?>