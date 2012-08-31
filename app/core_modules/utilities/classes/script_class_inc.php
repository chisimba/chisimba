<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* Class to verify a string contains no script attacks
*
* @category  Chisimba
* @package utilities
* @copyright 2006, University of the Western Cape & AVOIR Project
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version $Id$
* @author Nic Appleby
* @link      http://avoir.uwc.ac.za
*/

class script extends object
{
    /**
    * Method to remove script from a string
    *
    * @access public
    * @param string $str string to be checked
    * @return string the string without script tags
    */
    public function removeScript($str) {
        $pattern = '/<script(.*)<\/script>/i';
        while (preg_match($pattern,$str,$match)) {
            $str = str_replace($match,' ',$str);
        }
        return $str;
    }
}
?>