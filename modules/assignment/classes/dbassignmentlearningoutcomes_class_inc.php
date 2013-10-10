<?php

/**
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
class dbassignmentlearningoutcomes extends dbtable {

    /**
     * Initialisation.
     * @access public
     */
    public function init() {
        parent::init('tbl_assignment_learningoutcomes');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     * Method to get all filetypes for an assignment.
     * @access public
     * @param string $id The assignment
     * @return array List of filetypes
     */
    public function getGoals($id) {
        $sql = "
        WHERE assignment_id ='{$id}'
        "; //ORDER BY filetype ASC
        return $this->getAll($sql);
    }

    public function getGoalsFormatted($id) {
        $sql = "select g.learningoutcome,g.id
        from tbl_context_learneroutcomes g,tbl_assignment_learningoutcomes a
        WHERE a.assignment_id ='{$id}' and g.id=a.learningoutcome_id
        ";
        $str = "";
        $LO = $this->getArray($sql);
        $count=1;
        if (!empty($LO)) {
           
            foreach ($LO as $thisLO) {
                $str .= $thisLO["learningoutcome"].'<br/>';
                $count++;
            }
           
        } else {
            $str = " ";
        }
        return $str;
    }

    /**
     * links goals and workgroups
     * @param <type> $assignmentId
     * @param <type> $workgroupId
     * @return <type>
     */
    public function addGoal($assignmentId, $goalId) {
        $result = $this->insert(array('assignment_id' => $assignmentId, 'learningoutcome_id' => $goalId));
        return $result;
    }

    /**
     * Method to delete the filetypes.
     * @access public
     * @param string $id The assignment
     * @return void
     */
    public function deleteGoals($id) {
        $this->delete('assignment_id', $id);
        return;
    }

}

?>