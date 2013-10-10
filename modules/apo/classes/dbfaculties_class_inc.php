<?php

/**
 * This class interfaces with db to store a faculties
 *  PHP version 5
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
 * @package   apo (document management system)
 * @author    Nguni Phakela
 * @copyright 2010

 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class dbfaculties extends dbtable {

    var $tablename = "tbl_apo_faculties";
    var $userid;

    /* This is the default constructor. All the objects and parameters that belong
     * to the class are initialized here.
     * @param none
     * @access public
     * @return none
     */
    public function init() {
        parent::init($this->tablename);
        $this->objUser = $this->getObject('user', 'security');
        $this->userutils = $this->getObject('userutils');
    }

    
    /*
     * adds new faculty record
     * @param <String> $faculty
     * @param <datetime> $date
     * @param <String> $contact
     * @param <type> $telephone
     * @access public
     * @return none
     */
    public function addFaculty($rec, $path) {

        $date = date("Y m d");
        $userid = $this->objUser->userId();
        $currentuserid = $userid;

        // using this user id, get the full name and compare it with contact person!
        $fullname = $this->objUser->fullname($userid);
        if (strcmp($fullname, $contact) == 0) {
            $contact = "";
        }
        if ($contact == NULL) {
            $contact = $this->objUser->fullname();
        }

        if($this->exists($rec['name'])){
             return FALSE;
        }
        $rec['name'] = str_replace("'", "\'", $rec['name']);
        
        $data = array(
            'name' => $rec['name'],
            'date_created' => $date,
            'userid' => $this->objUser->userId(),
            'contact_person' => $rec['contact'],
            'telephone' => $rec['telephone'],
            'path'=> $path,
            'deleted'=>'N',
            "level" => count(explode("/", $path))
        );

        $this->insert($data);
    }

    /*
     * This method is used for editing information about a faculty in the database
     * @param $id the id of the field to be edited
     * @param $data All the fields that are to be updated for this faculty
     * @return none
     * @access public
     */
    public function editFaculty($id, $data) {
        $this->update("id", $id, $data);
    }

    /*
     * This method is used for deleting information about a faculty in the database
     * @param $id the id of the field to be deleted
     * @return none
     * @access public
     */
    public function deleteFaculty($id) {
        $this->delete("id", $id);
    }

    /* This method retrieve all the data for the different faculties.
     * @param none
     * @access public
     * @return array containing all the data for all the faculties
     */
    public function getFaculties() {
        return $this->getAll();
    }

    public function getFaculty($id) {
        return $this->getRow("id", $id);
    }

    public function getFacultyByName($name) {
        return $this->getRow("name", $name);
    }
    /*
     * This method checks whether the faculty that is being created already exists
     * @param $name The name of the faculty that is being checked for existence.
     * @access public
     * @return boolean TRUE/FALSE which says whether the faculty exists or not
     */
    public function exists($name){
        $sql=
        "select * from ".$this->tablename." where name ='$name'";
        $rows=$this->getArray($sql);
        if(count($rows) > 0){
            return TRUE;
        }
        
        return FALSE;
    }
}
?>