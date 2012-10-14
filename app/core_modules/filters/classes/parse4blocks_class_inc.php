<?php
/**
*
* Parse for blocks
*
* Class to parse a string (e.g. page content) that contains a filter
* code for including an internal or external block.
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
* @version   $Id: parse4twitter_class_inc.php 12001 2008-12-29 22:44:14Z charlvn $
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
* Parse for blocks
*
* Class to parse a string (e.g. page content) that contains a filter
* code for including an internal or external block.
*
* @author Derek Keats
*
*/
class parse4blocks extends object
{

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    
    /**
     *
     * String $objBlockFilter The block filter object from dynamiccanvas.
     * @access public
     *
     */
    public $objBlockFilter;

   /**
     *
     * Constructor for the TWITTER filter parser
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Load the blockfilter object from dynamic canvas
        $this->objBlockFilter = $this->getObject('blockfilter', 'dynamiccanvas');
    }

    /**
    *
    * Method to parse the string, which just uses the blockfilter in dynamic
    * canvas to parse it.
    **
    * @param  string $pageContent The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
        //Instantiate the modules class to check if twitter is registered
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the youtube API module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('dynamiccanvas', 'dynamiccanvas');
        if ($isRegistered) {
            return $this->objBlockFilter->parse($txt);
        } else {
            return $txt;
        }

    }
}
?>