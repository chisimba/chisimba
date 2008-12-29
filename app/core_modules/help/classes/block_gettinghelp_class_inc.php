<?php

/**
 * Class block_gettinghelp extends object. The class creates a block about how to use the help.
 * 
 * PHP version 3
 * 
 * @category  Chisimba
 * @package   help
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 University of the Western Cape
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
 
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Class for displaying a block about how to use the help for a module
*
* @author Derek Keats
*         
* $Id$
*/
class block_gettinghelp extends object
{

    /**
     * Variable containing the title of the block
     * @var    string
     * @access public 
     */
    public $title;
    
    /**
    * Constructor for the class
    *
    * @access public
    * @return void
    */
    public function init()
    {
        //Create an instance of the help object
        $this->objHelp= $this->getObject('helplink','help');
         //Create an instance of the language object
        $this->objLanguage = $this->getObject('language','language');
        //Set the title
        $this->title=$this->objLanguage->languageText("mod_postlogin_helptitle",'postlogin');
    }
    
    /**
    * Method to output a block with information on how the help works.
    *
    * @access public
    * @return string html The html content of the block
    */
    public function show()
    {
        //Add the text tot he output
        $ret = $this->objLanguage->languageText("mod_postlogin_helphowto",'postlogin');
        //Create an instance of the help object
        $objHelp =  $this->getObject('helplink','help');
        //Add the help link to the output
        $ret .= "&nbsp;".$this->objHelp->show('mod_postlogin_helphowto','postlogin');
        return $ret;
    }
}
?>