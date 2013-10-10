<?php
/**
 *
 * Database access for schools contacts
 *
 * Database access for schools. This is a sample database model class
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
 * @package   schools
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
* Database access for schools
*
* Database access for schools. This is a sample database model class
* that you will need to edit in order for it to work.
*
* @package   schools
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class dbschools_contacts extends dbtable
{

    /**
    *
    * Intialiser for the schools database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table to our demo table
        parent::init('tbl_schools_contacts');
        $this->table = 'tbl_schools_contacts';
    }

    /**
     * Method to return the contacts for a school
     * 
     * @access public
     * @param integer $id The id of the school to get contact for
     * @return array The array of contacts for a school
     */
    public function getContacts($sid)
    {
        return $this->fetchAll(" WHERE `school_id` = '$sid'" );
    }

    /**
     *
     * Method to delete school contacts
     * 
     * @access public
     * @param string $sid The id of the school to delete
     * return boolean 
     */
    public function deleteSchoolContacts($sid)
    {
        return $this->delete('school_id', $sid);
    }

    /**
     * Method to add a school contact to the database
     * 
     * @access public
     * @param array @data The array of school contact data
     * @return string $id The id of the school contact added
     */
    public function insertContact($data)
    {
        return $this->insert($data);
    }

    /**
     *
     * Method to delete school contact
     * 
     * @access public
     * @param string $id The id of the contact to delete
     * return boolean 
     */
    public function deleteContact($id)
    {
        return $this->delete('id', $id);
    }
    
    /**
     * Method to return the a contact
     * 
     * @access public
     * @param integer $id The id of contact to get
     * @return array The array of contact data
     */
    public function getContact($id)
    {
        $data = $this->getRow('id', $id);

        return $data;
    }

    /**
     * Method to update contacts to the database
     * 
     * @access public
     * @param array @data The array of contact data
     * @return string $id The id of contact edited
     */
    public function updateContact($id, $data)
    {
        return $this->update('id', $id, $data);
    }
}
?>