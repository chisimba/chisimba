<?php
/**
 * dbassignmentuploadablefiletypes_class_inc.php
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
 * @package   assignment
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbassignmentuploadablefiletypes_class_inc.php 22485 2011-09-15 10:30:27Z joconnor $
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
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Database class for assignment.
 *
 * @author Jeremy O'Connor
 * @package assignment
 *
 */
class dbassignmentuploadablefiletypes extends dbtable {
    /**
     * Initialisation.
     * @access public
     */
    public function init() {
        parent::init('tbl_assignment_uploadablefiletypes');
        $this->objUser = $this->getObject('user', 'security');
        //$this->objLanguage = $this->getObject('language', 'language');
    }
    /**
     * Method to get all filetypes for an assignment.
     * @access public
     * @param string $id The assignment
     * @return array List of filetypes
     */
    public function getFiletypes($id) {
        $sql = "
        WHERE assignmentid ='{$id}'
        "; //ORDER BY filetype ASC
        return $this->getAll($sql);
    }
    /**
     * Method to add list of filetypes.
     * @access public
     * @param string $id The assignment
     * @param array $filetypes An array of filetypes
     * @return void
     */
    public function addFiletypes(
        $id,
        $filetypes
    ) {

        if (!empty($filetypes)) {
            foreach ($filetypes as $filetype){
                $this->insert(array(
                            'assignmentid'=>$id,
                            'filetype'=>$filetype,
                            'userid' => $this->objUser->userId(),
                            'last_modified' => date('Y-m-d H:i:s', time()),
                            'updated' => date('Y-m-d H:i:s', time())
                        ));

            }
        }
        return;
    }
    /**
     * Method to delete the filetypes.
     * @access public
     * @param string $id The assignment
     * @return void
     */
    public function deleteFiletypes($id) {
        $this->delete('assignmentid', $id);
        return;
    }
}
?>