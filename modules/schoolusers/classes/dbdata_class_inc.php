<?php
/**
 *
 * Database access for schoolusers
 *
 * Database access for schoolusers. This is a sample database model class
 * that you will need to edit in order for it to work.
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
 * @package   schoolusers
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
* Database access for schoolusers
*
* Database access for schoolusers. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   schoolusers
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbdata extends dbtable
{

    /**
    *
    * Intialiser for the schoolusers database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_schoolusers_data');
        $this->table = 'tbl_schoolusers_data';
    }

    /**
     * Method to add user data to the database
     * 
     * @access public
     * @param array @data The array of user data
     * @return string $id The id of the user data added
     */
    public function saveData($data)
    {
        return $this->insert($data);
    }

    /**
     *
     * Method to update  user data
     * 
     * @access public
     * @param string $sid The id of the user data to update
     * return boolean 
     */
    public function updateData($id, $data)
    {
        return $this->update('user_id', $id, $data);
    }

    /**
     *
     * Method to get user data
     * 
     * @access public
     * @param string $id The id of the user data to get
     * return boolean 
     */
    public function getData($id)
    {
        return $this->getRow('user_id', $id);
    }
}
?>