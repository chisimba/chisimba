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
 * @package   practicals
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
 * Database class for practicals.
 *
 * @author Jeremy O'Connor
 * @package practicals
 *
 */
class dbpracticalslearningoutcomes extends dbtable {

    /**
     * Initialisation.
     * @access public
     */
    public function init() {
        parent::init('tbl_practicals_learningoutcomes');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     * Method to get all filetypes for an practicals.
     * @access public
     * @param string $id The practicals
     * @return array List of filetypes
     */
    public function getGoals($id) {
        $sql = "
        WHERE practical_id ='{$id}'
        "; //ORDER BY filetype ASC
        return $this->getAll($sql);
    }

    public function getGoalsFormatted($id) {
        $sql = "select g.learningoutcome,g.id
        from tbl_context_learneroutcomes g,tbl_practicals_learningoutcomes a
        WHERE a.practical_id ='{$id}' and g.id=a.learningoutcome_id
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
     * @param <type> $practicalId
     * @param <type> $workgroupId
     * @return <type>
     */
    public function addGoal($practicalId, $goalId) {
        $result = $this->insert(array('practical_id' => $practicalId, 'learningoutcome_id' => $goalId));
        return $result;
    }

    /**
     * Method to delete the filetypes.
     * @access public
     * @param string $id The practical
     * @return void
     */
    public function deleteGoals($id) {
        $this->delete('practical_id', $id);
        return;
    }

}

?>