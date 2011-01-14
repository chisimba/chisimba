<?php
/**
 *
 * A database login class
 *
 * Provides an element in the chain of comannd design pattern for an
 * authentication against the Chisimba database.
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
 * @package   login
 * @author    Multiple contributors
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://www.chisimba.com
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
* A database login class
*
* Provides an element in the chain of comannd design pattern for an
* authentication against the Chisimba database.
*
* @author Multiple contributors
* @package login
*
*/
class auth_database extends object
{

    /**
    *
    * Intialiser for authentication chain of command. It creates
    * an array containing the valid login methods.
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Set the valid elements in the chain of command
        $validElements = array('database');
    }

    // WORKING HERE
    
}
?>