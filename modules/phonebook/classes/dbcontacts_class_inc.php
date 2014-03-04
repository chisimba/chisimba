<?php


/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version unknow
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
 * @package   phonebook
 * @author    Administrative User <admin@localhost.local>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbcontacts_class_inc.php 11940 2008-12-29 21:21:54Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts

/**
 * Description for $GLOBALS
 * @global integer $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
 * Model class for the table tbl_phonebook
 * @author:Godwin Du Plessis, Ewan Burns, Helio Rangeiro, Jacques Cilliers, Charl Daniels, and Qoane Seitlheko.
 * @copyright 2007 University of the Western Cape
 */


/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   phonebook
 * @author    Administrative User <admin@localhost.local>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbcontacts_class_inc.php 11940 2008-12-29 21:21:54Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class dbContacts extends dbTable
{
    /**
     * Constructor method to define the table
     */
    public function init() 
    {
        parent::init('tbl_phonebook');
    }
    /**
     * Return all records in the tbl_phonebook.
     * 
     * @param $userId is the id taken from the tbl_user
     */
    public function listAll($userId) 
    {
        $userrec = $this->getAll("WHERE userid = '$userId'");
        return $userrec;
    }
    /**
     * Return a single record in the tbl_phonebook.
     *
     * @param $id is the id taken from the tbl_phonebook
     */
    public function listSingle($id) 
    {
        $onerec = $this->getRow('id', $id);
        return $onerec;
    }
    /**
     * Insert a record in the tbl_phonebook.
     *
     * @param $userId         is the id taken from the tbl_user
     * @param $firstname      is the name taken from the form
     * @param $lastname       is the surname taken from the form
     * @param $emailaddress   is the email-address taken from the form
     * @param $cellnumber     is the cell phone number taken from the form
     * @param $landlinenumber is the landline number taken from the form
     * @param $address        is the address taken from the form
     *                           
     *                           Also checks if text inputs are empty and returns the add a record template
     */
    public function insertRecord($userid, $firstname, $lastname, $emailaddress, $cellnumber, $landlinenumber, $address) 
    {
        $this->objUser = $this->getObject('user', 'security');
        $arrayOfRecords = array(
            'userid' => $this->objUser->userId() ,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'emailaddress' => $emailaddress,
            'cellnumber' => $cellnumber,
            'landlinenumber' => $landlinenumber,
            'address' => $address,
            'created_by' => $this->now()
        );
        if (empty($firstname) && empty($lastname) && empty($emailaddress) && empty($cellnumber) && empty($landlinenumber) && empty($address)) {
            return "addentry_tpl.php";
        } else {
            return $this->insert($arrayOfRecords, 'tbl_phonebook');
        }
    }
    /**
     * Deletes a record from the tbl_phonebook
     *
     * @param $id is the generated id for a single record
     */
    public function deleteRec($id) 
    {
        return $this->delete('id', $id, 'tbl_phonebook');
    }
    /**
     * Updates a record to the tbl_phonebook
     *
     * @param $id             is the generated id for a single record
     * @param $arrayOfRecords is an array of all the information added in the form
     *                           
     */
    public function updateRec($id, $arrayOfRecords) 
    {
        return $this->update('id', $id, $arrayOfRecords, 'tbl_phonebook');
    }
}
?>
