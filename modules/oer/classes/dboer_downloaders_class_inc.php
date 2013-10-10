<?php
/**
 * This class contains util methods for displaying full original product details
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
 * @version    0.001
 * @package    oer

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     pwando paulwando@gmail.com
 */

/* ----------- data class extends dbTable for tbl_oer_downloaders------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Model class for the table tbl_oer_downloaders
 * @author Paul Mungai
 * @copyright 2012 Kengasolutions
 */
class dboer_downloaders extends dbTable {

    /**
     * Constructor method to define the table
     */
    function init() {
        parent::init('tbl_oer_downloaders');
        $this->objUser = &$this->getObject('user', 'security');        
    }

    /**
     * Return all records as per params
     * @param string $columname
     * @param string $colvalue
     * @return array
     */
    function listAll($columname, $colvalue) {
        return $this->getAll("WHERE " . $columname . "='" . $colvalue . "'");
    }

    /**
     * Return a single record
     * @param string $id ID
     * @return array The values
     */
    function listSingle($id) {
        return $this->getAll("WHERE id='" . $id . "'");
    }

    /**
     * save adaptation into db
     * @param Array $data
     * @return String $id Id of newly inserted record
     */
    function insertSingle($data) {
        $id = $this->insert($data);
        return $id;
    }

    /**
     * Updates A Record
     * @param string $id ID of the record to be updated
     * @param string $data
     * @return string
     */
    function updateSingle($id, $data) {
        return $this->update("id", $id, $data);
    }
    /**
     * Updates A Record
     * @param string $id ID of the record to be updated
     * @return string
     */
    function updateSingleRecord($id) {
        $data = array(
            'fname' => $this->getParam("fname"),
            'lname' => $this->getParam("lname"),
            'email' => $this->getParam("email"),
            'organisation' => $this->getParam("organisation"),
            'occupation' => $this->getParam("occupation"),
            'downloadreason' => $this->getParam("downloadreason"),
            'useterms' => $this->getParam("useterms"),
            'notifyoriginal' => $this->getParam("notifyoriginal"),
            'notifyadaptation' => $this->getParam("notifyadaptation"),
            'downloadformat' => $this->getParam("downloadformat"),
            'downloadtime' => date("F j, Y, g:i a")
        );

        return $this->update("id", $id, $data);
    }

    /**
     * Delete a record
     * @param string $id ID
     */
    function deleteSingle($id) {
        $this->delete("id", $id);
    }

}

?>