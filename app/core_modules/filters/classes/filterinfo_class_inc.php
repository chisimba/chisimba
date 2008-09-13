<?php
/**
 *
 * Get lists of filters in various formats
 *
 * This class gets lists of filters in various formats, for use by
 * the washout utility class and others
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
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: filterinfo_class_inc.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Get lists of filters in various formats
 *
 * This class gets lists of filters in various formats, for use by
 * the washout utility class and others
*
* @author Derek Keats
* @package filters
*
*/
class filterinfo extends object
{

    /**
    *
    * Intialiser for the _MODULECODE controller
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
        $this->objConfig = $this->getObject('altconfig', 'config');
    }
    
    /**
    * 
    * Return a list of all filters as an array
    * 
    * @return string array List of all the filters in the filters directory
    * @access public
    *  
    */
    public function getFilters()
    {
        // save cwd
        $savedDir = getcwd();
        //load up all of the parsers from filters
        $filterDir = $this->objConfig->getsiteRootPath() . "core_modules/filters/classes/";
        chdir($filterDir);
        $parsers = glob("parse4*_class_inc.php");
        // restore path
        chdir($savedDir);
        return $parsers;
    }
}
?>