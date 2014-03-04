<?php
/**
 *
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
 * @package   resdevelopment (Residence Development)
 * @author    Nguni Phakela, Mulalo Matshusa, Hleketani Mabasa
 *
 =
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

class resdevelopment extends controller {
  public function init() {
        $this->objStudents = $this->getObject('dbstudents');
        $this->objGroup = $this->getObject('dbgroup');
        $this->objAttendance = $this->getObject('dbattendance');
    }

    /**
     * Standard Dispatch Function for Controller
     * @param <type> $action
     * @return <type>
     */
    public function dispatch($action) {
        /*
    * Convert the action into a method (alternative to
    * using case selections)
        */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__'.$action;
        }
        else {
            return '__home';
        }
    }

    /**"
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /**
     * Method to show the Home Page of the Module
     */
    public function __home() {
        return "home_tpl.php";
    }

    /*
     * Method to show the page for adding student
     *
     */
    public function __addstudent() {
        return "student_tpl.php";
    }


     /*
     * Method to show the page for adding group
     *
     */
    public function __addgroup() {
        return "groupmanagement_tpl.php";
    }
     /*
     * Method to show the page for caputing attendance
     *
     */
    public function __captureattendance() {
        return "captureattendance_tpl.php";
    }
        public function __savestudent() {
        // save the student info
        $this->objStudents->saveStudentInfo($this->getParam('firstname'), $this->getParam('lastname'));
        $this->nextAction("addstudent");
    }

    public function __savegroup() {
        // save the student info
        $this->objGroup->saveGroupInfo($this->getParam('groupname'));
        $this->nextAction("addgroup");
    }

    public function __editstudent() {
        // save the student info
        $this->objStudents->editStudentInfo($this->getParam('id'), $this->getParam('firstname'), $this->getParam('lastname'));
        $this->nextAction("addstudent");
    }

    public function __editgroup() {
        // save the student info
        $this->objGroup->editGroupInfo($this->getParam('groupname'), $this->getParam('id'));
        $this->nextAction("addgroup");
    }
    
    public function __saveattendance() {
        // save the attandance info
        $this->objAttendance->saveAttendanceInfo($this->getParam('attendance'));
        $this->nextAction("captureattendance");
    }
  public function __editattendance() {
        // edit the attendance info
        $this->objAttendance->editAttendanceInfo($this->getParam('attendance'), $this->getParam('id'));
        $this->nextAction("captureattendance");
    }
      public function __deleteattendance() {
        // delete attendance from database
        $attendanceID = $this->getParam('id');
        $this->objAttendance->deleteAttendance($attendanceID);
        $this->nextAction("captureattendance");
    }
    public function __deletenames() {
        // delete student from database
        $studentID = $this->getParam('id');
        $this->objStudents->deleteStudent($studentID);
        $this->nextAction("addstudent");
    }
    public function __deletegroup() {
        // delete group from database
        $groupID = $this->getParam('id');
        $this->objGroup->deleteGroup($groupID);
        $this->nextAction("addgroup");
    }

     public function __deletestudent() {
        // delete group from database
        $studentID = $this->getParam('id');
        $this->objStudents->deleteStudent($studentID);
        $this->nextAction("addstudent");
    }
}