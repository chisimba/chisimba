<?php
/**
 *
 * Assignments
 *
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
 * @version   $Id: dbassignment_old_class_inc.php 23906 2012-04-03 21:16:57Z pwando $
 * @link      http://avoir.uwc.ac.za
 */


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} // end security check

/**
* Class to access table tbl_assignment
* @author Megan Watson
* @copyright (c) 2004 UWC
* @version 0.1
*/

class dbassignment_old extends dbtable
{
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_assignment');
        $this->table = 'tbl_assignment';
    }

    /**
    * Method to search assignments and return the results.
    * @param string $field The table field in which to search.
    * @param string $value The value to search for.
    * @param string $context The current context.
    * @return array $data The results of the search.
    */
    public function search($field, $value, $context)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE $field LIKE '$value%'";
        $sql .= " AND context='$context'";
        $sql .= ' ORDER BY closing_date, name';

        $data = $this->getArray($sql);

        if($data){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to insert a new assignment into the database or update an existing one.
    * @param array $fields The table fields and values to be inserted in the database
    * @param string $id The id of the assignment to be updated, default=NULL on insert
    * @return
    */
    public function addAssignment($fields, $id=NULL)
    {
        if(!empty($id)){
            $this->update('id',$id,$fields);
        }else{
           
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
    * Method to get an assignment from the database.
    * @param string $context The current context.
    * @param string $filter
    * @return array $data List of assignments
    */
    public function getAssignment($context, $filter=NULL)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE context='".$context."'";

        if($filter){
            $sql .= ' AND '.$filter;
        }
        $sql .= ' ORDER BY closing_date';
        $data = $this->getArray($sql);

        if($data){
            return $data;
        }
        return FALSE;
    }
    /**
    * Method to get an assignment from the database.
    * @param string $id The Unique Identifier.
    * @param string $filter
    * @return array $data List of assignments
    */
    public function getAssignmentById($id, $filter=NULL)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE id='".$id."'";

        if($filter){
            $sql .= ' AND '.$filter;
        }
        $sql .= ' ORDER BY closing_date';
        $data = $this->getArray($sql);

        if($data){
            return $data;
        }
        return FALSE;
    }
    /**
    * Method to delete an assignment.
    * @param string $id The id of the assignment to be deleted
    * @return
    */
    public function deleteAssignment($id)
    {
        $this->delete('id',$id);
    }
}// end of class
?>