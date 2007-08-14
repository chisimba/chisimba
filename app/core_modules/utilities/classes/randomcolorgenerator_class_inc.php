<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

 /**
 * Class to Generate a Random Color
 *
 * @category  Chisimba
 * @package   <module name>
 * @author Tohir Solomons
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * Found at: http://www.zend.com/tips/tips.php?id=243&single=1
 * It's setup to create colors on the lighter side of the spectrum so you
 * can use them with darker colors for the text.  
 */ 
class randomcolorgenerator extends object
{

    /**
    * Constructor
    */
    public function init()
    { }
    
    /**
    * Method to generate a random color
    * @return string Color in Hexadecimal format
    */
    public function generateColor()
    {
        $r = rand(128,255);
        $g = rand(128,255);
        $b = rand(128,255);
        
        return dechex($r).dechex($g).dechex($b);
    }

}

?>