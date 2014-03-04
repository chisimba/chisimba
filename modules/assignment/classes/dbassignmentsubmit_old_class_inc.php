<?php
/**
 *
 * Assignments
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
 * @package   assignment2
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbassignmentsubmit_old_class_inc.php 22485 2011-09-15 10:30:27Z joconnor $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} // end security check

/**
* Class to provide access to the table tbl_assignment_submit
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package assignment
* @version 1
*/

class dbassignmentsubmit_old extends dbtable
{
    /**
    * Method to construct the class
    */
    public function init()
    {
        parent::init('tbl_assignment_submit');
        $this->table = 'tbl_assignment_submit';
        $this->assignTable = 'tbl_assignment';
    }

    /**
    * Method to insert a submitted assignment.
    * @param array $fields The assignment details
    * @return bool
    */
    public function addSubmit($fields, $id=NULL)
    {
        if($id){
            $this->update('id',$id,$fields);
            return $id;
        }else{
            $id = $this->insert($fields);
            return $id;
        }
        return FALSE;
    }

    /**
    * Method to update a submitted assignment.
    * A lecturers mark and comment are added to the relevant assignment after marking.
    * @return
    */
    public function updateSubmit($id, $fields)
    {
        $this->update('id',$id,$fields);
    }

    /**
    * Method to get a submitted assignment
    * @param string $filter
    * @param string $fields The required fields. Default = * (all);
    * @return array $data The submitted assignments
    */
    public function getSubmit($filter, $fields='*')
    {
        $sql = "SELECT $fields FROM ".$this->table;
        $sql .= " WHERE $filter";

        $data = $this->getArray($sql);

        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * added by otim samuel, sotim@dicts.mak.ac.ug: 13th Jan 2006
    * for specific use within the gradebook module
    * Method to get submitted assignments,
    * as a percentage of the total year's mark
    * @param string $filter
    * @param string $fields The required fields. Default = * (all);
    * @param string $tables The tables to be queried
    * @return array $data The submitted assignments
    */
    public function getSubmittedAssignments($filter, $fields='*', $tables='tbl_assignment_submit')
    {
        $sql = "SELECT $fields FROM ".$tables;
        $sql .= " WHERE $filter";

        $data = $this->getArray($sql);

        if(!empty($data)){
            return $data;
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get a list of assignments is the context.
    * Each assignment shows number of submissions, number marked and closing date.
    * @param string $context The current context
    */
    public function getContextSubmissions($context)
    {
        $sql = 'SELECT assign.id, assign.name, assign.closing_date, submit.datesubmitted, submit.mark ';
        $sql .= 'FROM '.$this->table.' AS submit ';
        $sql .= 'LEFT JOIN '.$this->assignTable.' as assign ON assign.id = submit.assignmentId ';
        $sql .= "WHERE context = '$context' ORDER BY assign.id";

        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to delete a submitted assignment
    * @param string $id The id of the assignment to delete
    */
    public function deleteSubmit($id)
    {
        $this->delete('id', $id);
    }

    /**
    * Method to get the name of an uploaded file.
    * The method accesses the table tbl_assignment_filestore.
    * @param string $userId The id of the student submitting the assignment.
    * @param string $fileId The id of the file uploaded
    * @return string $filename The name of the file uploaded
    */
    public function getFileName($userId, $fileId)
    {
        $sql = "SELECT filename FROM tbl_assignment_submit ";
        $sql .= "WHERE fileid='$fileid'";

        $data = $this->getArray($sql);
        return $data[0]['filename'];
    }
}
?>
