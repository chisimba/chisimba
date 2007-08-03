<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
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
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

    /* Simple class for outputting '<a href' links
    * @author James Scoble
    * @param $link
    * @param $text
    * @param $other
    */

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class href implements ifhtml
{

    /**
     * Description for public
     * @var    string
     * @access public
     */
        public $link;

    /**
     * Description for public
     * @var    string
     * @access public
     */
        public $text;

    /**
     * Description for public
     * @var    string
     * @access public
     */
        public $other;

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $link  Parameter description (if any) ...
     * @param  unknown $text  Parameter description (if any) ...
     * @param  unknown $other Parameter description (if any) ...
     * @return void   
     * @access public 
     */
        public function href($link=Null,$text=Null,$other=Null)
        {
            $this->link=$link;
            $this->text=$text;
            $this->other=$other;
        }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
        public function show()
        {
            $href="<a href='".$this->link."' ".$this->other.">".$this->text."</a>\n";
            return $href;
        }

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  string $link  Parameter description (if any) ...
     * @param  string $text  Parameter description (if any) ...
     * @param  string $other Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
        public function showlink($link,$text,$other=Null)
        {
            $href="<a href='".$link."' ".$other.">".$text."</a>\n";
            return $href;
        }
    } // end of class

?>