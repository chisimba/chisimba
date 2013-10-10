<?php
/**
 *
 * Make module
 *
 * Enable developers to quickly build a module that complies with Chisimba development standards.
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
 * @package   makemodule
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbmakemodule.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* Database accesss class for Chisimba for the module makemodule
*
* @author Derek Keats
* @package makemodule
*
*/
class dbmakemodule extends dbtable
{

    /**
    *
    * Intialiser for the makemodule database connector
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
        parent::init('tbl_makemodule_text');
    }

    /**
     * 
     * Get the text of the init_overview
     *
     * @return string The text of the init_overview
     * @access public
     * 
     */
    public function getOverview()
    {
        return $this->getAll(' WHERE id=\'init_overview\' ');

    }

}
?>
