<?php

/**
 /* Simple class for outputting '<a href' links>
 * 
 * PHP version 5
 * 
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
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


class href implements ifhtml
{

    /**
     * Holds the href link
     * @var    string
     * @access public
     */
        public $link;

    /**
     * Holds the text for the link
     * @var    string
     * @access public
     */
        public $text;

    /**
     * Holds other information in  a string
     * @var    string
     * @access public
     */
        public $other;

    /**
     * A method to set the href link
     * 
     * @param  unknown $link  Parameter description (if any) ...
     * @param  unknown $text  Parameter description (if any) ...
     * @param  unknown $other Parameter description (if any) ...
     * @return void href
     * @access public.
     */
        public function href($link=Null,$text=Null,$other=Null)
        {
            $this->link=$link;
            $this->text=$text;
            $this->other=$other;
        }

    /**
     * A method to show the href link
     * 
     * @return string Return href link
     * @access public
     */
        public function show()
        {
            $href="<a href='".$this->link."' ".$this->other.">".$this->text."</a>\n";
            return $href;
        }

    /**
     * A method to display the link
     * 
     * @param  string $link  Parameter description (if any) ...
     * @param  string $text  Parameter description (if any) ...
     * @param  string $other Parameter description (if any) ...
     * @return string Return link
     * @access public
     */
        public function showlink($link,$text,$other=Null)
        {
            $href="<a href='".$link."' ".$other.">".$text."</a>\n";
            return $href;
        }
    } // end of class

?>