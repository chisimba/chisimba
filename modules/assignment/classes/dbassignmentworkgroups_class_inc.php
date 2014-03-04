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
 * Database class for assignment workgroups.
 *
 * @author David Wafula
 * @package assignment
 *
 */
class dbassignmentworkgroups extends dbtable {

    /**
     * Initialisation.
     * @access public
     */
    public function init() {
        parent::init('tbl_assignment_workgroups');
        $this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link','htmlelements');
    }

    /**
     * Method to get all worgroups for an assignment.
     * @access public
     * @param string $id The assignment
     * @return array List of workgroups
     */
    public function getWorkgroups($id) {
        $sql = "
        WHERE assignment_id ='{$id}'";

        return $this->getAll($sql);
    }


    public function getGroupsFormatted($id) {
        $sql = "select wg.description,wg.id
        from tbl_workgroup wg,tbl_assignment_workgroups lw
        WHERE lw.assignment_id ='{$id}' and wg.id=lw.workgroup_id
        ";
        $groups = $this->getArray($sql);
        $str = "";
        $count=1;
        if (!empty($groups)) {

            foreach ($groups as $thisGr) {
                /*
                $link=new link($this->uri(array("action"=>"joinworkgroup","workgroup"=>$thisGr['id']),"workgroup"));
                $link->link=$thisGr["description"].'<br/>';
                $str .= $link->show();
                */
                $str .= $thisGr["description"].'<br />';
                $count++;
            }

        } else {
            $str = "";
        }
        return $str;
    }


    /**
     * links assignments and workgroups
     * @param <type> $assignmentId
     * @param <type> $workgroupId
     * @return <type>
     */
    public function addWorkgroup($assignmentId, $workgroupId) {
        $result = $this->insert(array('assignment_id' => $assignmentId, 'workgroup_id' => $workgroupId));
        return $result;
    }

    /**
     * Method to delete the workgroups.
     * @access public
     * @param string $id The assignment
     * @return void
     */
    public function deleteWorkgroups($id) {
        $this->delete('assignment_id', $id);
        return;
    }

}

?>