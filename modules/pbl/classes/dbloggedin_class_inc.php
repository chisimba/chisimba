<?php
/**
* Class dbLoggedIn extends dbTable.
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class for providing access to the loggedin table in the database.
 * The table contains information about users registered in the classrooms
 * and whether they are currently logged into the classroom.
 * The table also contains the private notes taken by a user during a session.
 *
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class dbLoggedIn extends dbTable
{
    /**
     * var $table The name of the table
     */
    private $table = "tbl_pbl_loggedin";

    /**
     * Constructor method to define the table and initialise objects.
     */
    public function init()
    {
        parent::init('tbl_pbl_loggedin');
    }

    /**
     * Method to register a user in a classroom.
     *
     * @param string $clid Id of classroom to register user in
     * @param string $stid User id from users table
     * @param string $avail 1 if the user is logged in, 0 if not
     * @return bool
     */
    public function addToClass($clid, $stid, $avail = "0")
    {
        $fields = array();
        $fields['classroomid'] = $clid;
        $fields['studentid'] = $stid;
        $fields['isavailable'] = $avail;
        if (!$this->insert($fields)){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    /**
     * Method to set the position of a user in the group: facilitator, chair, scribe or member.
     *
     * @param string $clid Id of classroom in which the user is logged in to
     * @param string $stid User id from users table
     * @param string $pos The position: c=chair; s=scribe; f=facilitator; n=other
     * @return
     */
    public function setPosition($clid, $stid, $pos = 'n')
    {
        $fields['position'] = $pos;

        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE classroomid='" . $clid . "' and studentid='" . $stid . "'";
        $result = $this->getArray($sql);

        if($result){
            $this->update('id',$result[0]['id'],$fields);
        }
    }

    /**
    * Method to get the position of a user in the group.
    * @param string $clid Id of classroom in which the user is logged in to
    * @param string $stid Id of the user
    * @return string $result The position of the user in the classroom.
    */
    public function getPosition($clid, $stid)
    {
        $sql = 'SELECT position FROM '.$this->table;
        $sql .= " WHERE classroomid='" . $clid . "' and studentid='" . $stid . "'";

        $result = $this->getArray($sql);

        if($result){
            return $result[0]['position'];
        }
        return FALSE;
    }

    /**
     * Method to set whether the current user is logged in to the current classroom.
     *
     * @param string $avail 1 if logged in, 0 if not
     * @return
     */
    public function setLoggedIn($avail = '0')
    {
        $sesClass = $this->getSession('classroom');
        $sesPblUserId = $this->getSession('pbl_user_id');
        $fields['isavailable'] = $avail;

        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE classroomid='" .$sesClass. "' and studentid='";
        $sql .= $sesPblUserId . "'";
        $result = $this->getArray($sql);

        if($result){
            $this->update('id',$result[0]['id'],$fields);
        }
    }

    /**
     * Method to check whether a user is logged into a classroom.
     *
     * @param string $clid Id of classroom
     * @param string $stid User id from users table
     * @return string $row 0 if not logged in, 1 if logged in
     */
    public function isLoggedIn($stid, $clid)
    {
        $sql = "select isavailable from " . $this->table . " where studentid='$stid' and classroomid='$clid'";
        $row = $this->getArray($sql);
        if (!$row){
            return FALSE;
        }
        return $row[0]['isavailable'];
    }

    /**
     * Method to move students from one classroom to another.
     *
     * @param string $id Id of classroom to be moved to
     * @param string $oldid Id of classroom currently registered in
     * @return
     */
    public function changeClassId($id, $oldid = -1)
    {
        $fields['classroomid'] = $id;

        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE classroomid='$oldid'";
        $result = $this->getArray($sql);

        if($result){
            $this->update('id',$result[0]['id'],$fields);
        }
    }

    /**
     * Method to deregister one or more users from a classroom.
     *
     * @param string $clid Id of classroom
     * @param string $stid User id from users table
     * @param string $multiple True if deregister entire classroom of users
     * @return
     */
    public function removeFromClass($multiple = FALSE, $classroomid = NULL, $studentid = NULL)
    {
        $sesClass = $this->getSession('classroom');
        $sesPblUserId = $this->getSession('pbl_user_id');

        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE ";

        if (!$multiple) {
            if (!$studentid) {
                $studentid = $sesPblUserId;
            }
            $sql .= "studentid='" . $studentid . "' and ";
        }
        if (!$classroomid) {
            $classroomid = $sesClass;
        }
        $sql .= "classroomid='" . $classroomid . "'";

        $result = $this->getArray($sql);

        if($result){
            foreach($result as $line){
                $this->delete('id',$line['id']);
            }
        }
    }

    /**
     * Method to get a list of users in the active classroom.
     *
     * @param string $filter The filter applied to the sql select
     * @return array $userid An array of the ids of the logged in users
     */
    public function findUserIds($filter = NULL)
    {
        $userid = array();
        $sql = "select studentid, position from " . $this->table;
        if ($filter){
            $sql .= " where $filter";
        }
        $userids = $this->getArray($sql);
        if (!$userids){
            return FALSE;
        }
        return $userids;
    }

    /**
     * Method to get a list of classrooms associated with a particular user.
     *
     * @param string $stid User id default to student
     * @return array $clids A list of classroom ids for the user
     */
    public function getClassId($stid = 'student')
    {
        $data = array();
        $classes = array();
        $sql = "select DISTINCT classroomid from " . $this->table . " where studentid='$stid'";
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            foreach($data as $val) {
                $classes[] = $val['classroomid'];
            }
        }
        return $classes;
    }

    /**
     * Method to determine whether a classroom is in use.
     *
     * @param string $clid Id of classroom to be checked
     * @return bool
     */
    public function isInUse($clid)
    {
        $array = array();
        $sql = "select * from " . $this->table . " where classroomid='$clid' and isavailable='1'";
        $array = $this->getArray($sql);
        if (!$array){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    /**
     * Method to get a record count.
     *
     * @param string $filter Filter on the sql request
     * @return string $count The record count
     */
    public function getCount($filter = NULL)
    {
        if ($filter){
            $filter = "where " . $filter;
        }
        $count = $this->getRecordCount($filter);
        return $count;
    }

    /**
     * Method to save the users private notes.
     *
     * @param string $notes The content of the notes
     * @return
     */
    public function saveNotes($notes)
    {
        $sesClass = $this->getSession('classroom');
        $sesPblUserId = $this->getSession('pbl_user_id');

        $newNote = $this->retrieveNotes("*");
        $fields = array();
        if (!$newNote) {
            $fields['studentid'] = $sesPblUserId;
            $fields['classroomid'] = $sesClass;
            $fields['notes'] = $notes;
            $this->insert($fields);
        } else {
            $fields['notes'] = $notes;

            $sql = 'SELECT id FROM '.$this->table;
            $sql .= " WHERE studentid='" .$sesPblUserId. "' and classroomid='";
            $sql .= $sesClass. "'";
            $result = $this->getArray($sql);

            if($result){
                $this->update('id',$result[0]['id'],$fields);
            }
        }
    }

    /**
     * Method to retrieve the users notes for current classroom.
     *
     * @param string $field The database field to search
     * @return array $rows Array containing the notes
     */
    public function retrieveNotes($field = 'notes')
    {
        $sesClass = $this->getSession('classroom');
        $sesPblUserId = $this->getSession('pbl_user_id');

        $sql = "select " . $field . " from " . $this->table . " where studentid='" .$sesPblUserId. "' and classroomid='" .$sesClass. "'";
        $rows = $this->getArray($sql);
        return $rows;
    }

    /**
     * Method to erase the users notes.
     *
     * @return
     */
    public function eraseNotes()
    {
        $sesClass = $this->getSession('classroom');
        $sesPblUserId = $this->getSession('pbl_user_id');

        // Set field to NULL
        $fields = array();
        $fields['notes'] = NULL;

        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE studentid='" . $sesPblUserId. "' and classroomid='";
        $sql .= $sesClass. "'";
        $result = $this->getArray($sql);

        if($result)
            $this->update('id',$result[0]['id'],$fields);
    }
}

?>