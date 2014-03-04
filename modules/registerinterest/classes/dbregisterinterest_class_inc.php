<?php

/**
 *
 * Database access for Register interest
 *
 * Database access for Register interest. This is a database model class
 * that provides data access to the default module table - tbl_registerinterest_text.
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
 * @package   registerinterest
 * @author    Derek Keats derek@dkeats.com
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
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Database access for Register interest
 *
 * Database access for Register interest. This is a database model class
 * that provides data access to the default module table - tbl_registerinterest_text.
 *
 * @package   registerinterest
 * @author    Derek Keats derek@dkeats.com
 *
 */
class dbregisterinterest extends dbtable {

    /**
     *
     * Intialiser for the registerinterest database connector
     * @access public
     * @return VOID
     *
     */
    public function init() {
        //Set the parent table to our demo table
        parent::init('tbl_registerinterest_interested');
        $this->jsAlert = $this->getObject('jsalerts', 'utilities');
    }

    /**
     * Method to return the name of the table using the ID from query string
     * 
     * @access public
     * @param type $tableID ID of the table where the query will be executed
     */
    public function setTableName($tableID) {
        if ($tableID == 2) {
            parent::init('tbl_registerinterest');
        } else {
            parent::init('tbl_registerinterest_interested');
        }
    }

    /**
     *
     * Save the data
     *
     * @param string The table name
     * @return string The text of the init_overview
     * @access public
     *
     */
    public function save($data) {
        //code to be executed when adding the a field users will register for
        return $this->insert($data);
    }

    /**
     * Method to remove a record from the database
     * 
     * @access public
     * @param string $id the record ID
     * @param string $tableID The table in which the query will be executed
     * @return delete query result 
     */
    public function remove($id, $tableID) {
        //set the table name
        $this->setTableName($tableID);
        //check if the value has not already been removed
        if ($this->valueExists('id', $id)) {
            return $this->delete('id', $id);
        } else {
            return FALSE;
        }
    }

    /**
     * Method to update the records
     * 
     * @access public
     * @param NULL
     * @return boolean TRUE if the record is successfully updated| FALSE if there was an error while updating the record
     */
    public function updateMail() {
        //get the new value
        $newValue = $this->getParam('newValue', NULL);
        //get the ID
        $id = $this->getParam('id', NULL);
        //set the value to be changed
        $valuesaArray = array(
            'email' => $newValue
        );
        //update database
        return $this->update('id', $id, $valuesaArray);
    }

}

?>