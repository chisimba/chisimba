<?php

/**
* Class to parse a string (e.g. page content) that contains a reference to
* a Mindmeister Mindmap from http://www.mindmeister.com/
*
* PHP version 5
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the
* Free Software Foundation, Inc.,
* 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*
* @category  Chisimba
* @package   filters
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 Derek Keats
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   CVS: $Id$
* @link      http://avoir.uwc.ac.za
*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 *
 * Class to parse a string (e.g. page content) that contains a reference to
 * a Mindmeister Mindmap from http://www.mindmeister.com/
 *
 *
 *
 * @author Derek Keats
 *
 */
class parse4mindmeister extends object
{
    /**
     * used to extract params from a string
     * @var String
     */
    public $objExpar;

    /**
     * unique id
     * @var String
     */
    public $id;
    
    /**
     *
     * flash width
     */
    public $width;

    /**
     *
     * flash height
     */
    public $height;

    /**
     *
     * Constructor for the wikipedia parser
     *
     * @return void
     * @access public
     *
     */
    function init()
    {
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }

    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The parsed string
    *
    */
    public function parse($txt)
    {
    	//Instantiate the modules class to check if youtube is registered
        $objModule = $this->getObject('modules','modulecatalogue');
    	//Match filters based on a wordpress style
    	preg_match_all('/\\[MINDMEISTER:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
    	//Get all the ones in links
    	$counter = 0;
    	foreach ($results[0] as $item) {
            $this->item=$item;
        	$str = $results[1][$counter];
        	$ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
            $replacement = $this->showMap();
        	$txt = str_replace($item, $replacement, $txt);
        	$counter++;
            //Clear the set params
            unset($this->id);
            unset($this->objExpar->id);
            unset($this->width);
            unset($this->objExpar->width);
            unset($this->height);
            unset($this->objExpar->height);
    	}
        return $txt;
    }

    /**
     *
     * Method to set up the parameter / value pairs for th efilter
     * @access public
     * @return VOID
     *
     */
    public function setUpPage()
    {
        if (isset($this->objExpar->id)) {
            $this->id = $this->objExpar->id;
        } else {
            $this->id=NULL;
        }
        if (isset($this->objExpar->width)) {
            $this->width = $this->objExpar->width;
        } else {
            $this->width=600;
        }
        if (isset($this->objExpar->height)) {
            $this->height = $this->objExpar->height;
        } else {
            $this->height=400;
        }
    }

    /**
     *
     * A method to return the flash presentation for rendering in the page
     * @param string $uri The URL of the flash file to show
     * @return string the flash file rendered for viewing within a div
     * @access public
     *
     */
    public function showMap()
    {
         $ret = '<iframe width="' . $this->width
           . '" height="' .$this->height . '" '
           . 'frameborder="0" src="http://www.mindmeister.com/maps/'
           . 'public_map_shell/' . $this->id
           . '?width=' . $this->width . '&height='
           .$this->height. '&zoom=1" scrolling="no" '
           . 'style="overflow:hidden"></iframe>';

        return $ret;
    }
}
?>