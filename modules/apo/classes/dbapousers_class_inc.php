<?php

/**
 * This class interfaces with db to store users
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

class dbapousers extends dbtable {

    var $tablename = "tbl_apo_users";
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
    }

    /*
     * adds new user record
     * @param <String> $name
     * @param <String> $role
     * @param <String> $email
     * @param <type> $telephone
     * @access public
     * @return none
     */

    public function addUser($record) {

        $date = date("Y m d");
        $userid = $this->objUser->userId();

        $data = array(
            'name' => $record['name'],
            'role' => $record['role'],
            'email' => $record['email'],
            'department' => $record['department'],
            'date_created' => 'NOW()',
            'userid' => $userid,
            'telephone' => $record['telephone'],
            'deleted' => 'N',
        );

        $this->insert($data);
    }

    /*
     * This method is used for editing information about a user in the database
     * @param $id the id of the field to be edited
     * @param $data All the fields that are to be updated for this user
     * @return none
     * @access public
     */

    public function editUser($id, $data) {
        $this->update("id", $id, $data);
    }

    /*
     * This method is used for deleting information about a user in the database
     * @param $id the id of the field to be deleted
     * @return none
     * @access public
     */

    public function deleteUser($id) {
        $this->delete("id", $id);
    }

    /* This method retrieve all the data for the different users.
     * @param none
     * @access public
     * @return array containing all the data for all the users
     */

    public function getUsers() {
        return $this->getAll();
    }

    /* This method retrieves data for a specific user.
     * @param <String> $id The key that is used to retrieve data for each user
     * @access public
     * @return array containing all the data for that users
     */

    public function getUser($id, $department) {
        return $this->getRow("id", $id);
    }

    /*
     * This method is used to get the number of users, either for a particular division,
     * or for all divisions
     * @param <String> $role The role that the user has
     * @access public
     * @return <String> $count The number of users
     */

    public function getDepartmentUser($departmentID){
        $sql = "select user.name, user.email, user.telephone, dept.name as department from tbl_apo_users user, tbl_apo_faculties dept where dept.id=user.department and user.department ='$departmentID' limit 1";

        return $this->getArray($sql);
    }

    public function getCommentsUsers($departmentID){
        $sql = "select user.role, user.name, user.email, user.telephone, dept.name as department from tbl_apo_users user, tbl_apo_faculties dept where dept.id=user.department and user.department ='$departmentID'";

        return $this->getArray($sql);
    }


    public function getNumUsers($role = NULL) {

        if (!empty($role)) {
            $this->getAll("role = '$role'");
        } else {
            return count($this->getUsers());
        }
    }

    /*
     * This method checks whether the user that is being created already exists
     * @param $name The name of the user that is being checked for existence.
     * @access public
     * @return boolean TRUE/FALSE which says whether the user exists or not
     */

    public function exists($name) {
        $sql =
                "select * from " . $this->tablename . " where name ='$name'";
        $rows = $this->getArray($sql);
        if (count($rows) > 0) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * returns all the users. Not efficient, need to use limits and include pagination
     * @return <type>
     */
    function getAllUsers() {
        $sql =
                "select * from tbl_users";
        return $this->getArray($sql);
    }

    function sendEmail($subject, $message, $recipientEmailAddress) {


        $objMail = $this->getObject('mailer', 'mail');
//send to multiple addressed
        $list = array($recipientEmailAddress);
       // $list = array($senderEmailAddress);
        $objMail->to = ($list);
//attach a pdf document
        //$path = '/home/palesa/Documents/Faith.odt';
       // $objMail->attach($path,'Faith');

// specify whom the email is coming from
        $objMail->from = $this->objUser->email();
//Give email subject and body
//$objMail->subject=$emaill;
        $objMail->subject = $subject;
        $objMail->body = $message;
        $objMail->AltBody = $message;
// send email
        $objMail->send();
    }

}

?>