<?php
/**
* Class dbChat extends dbTable.
* @author Fernando Martinez
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
 * Class for providing access to the chat table in the database.
 * The table contains the students chat for each classroom.
 * A classrooms chat is deleted after 2 sessions.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class dbChat extends dbTable
{
    /**
     * instance of the facilitator object
     * var $tutor
     */
    public $tutor;

    /**
     * Variable containing the name of the database table
     * var $table;
     */
    private $table = 'tbl_pbl_chat';

    /**
     * Constructor method to define the table.
     */
    public function init()
    {
        parent::init('tbl_pbl_chat');
        $this->tutor = &$this->getObject('facilitate');
    }

    /**
     * Method to insert the user name before the chat string and insert the chat string into the database table.
     * The method checks for a '.' at the start of the chat string, this indicates that the string
     * should be parsed using the facilitate object.
     * The facilitators response is inserted into the database table after the chat string.
     *
     * @param string $str The users chat input
     * @return
     */
    public function say($str)
    {
        $npos = FALSE;
        if ($str[0] == '.'){
            $npos = TRUE;
        }

        $sesPblUser = $this->getSession('pbl_user');
        $sesPblUserId = $this->getSession('pbl_user_id');
        $sesClass = $this->getSession('classroom');


        // Insert chat string into database with the users name ahead
        $userstr = $sesPblUser . ": " . $str;
        $fields = array();
        $fields['classroomid'] = $sesClass;
        $fields['studentid'] = $sesPblUserId;
        $fields['msg'] = $userstr;
        $fields['entrydate'] = date('Y-m-d H:i');
        $fields['updated'] = date('Y-m-d H:i');
        $this->insert($fields);
        // if addressing the virtual facilitator: parse string and insert facilitator response
        if ($npos == TRUE) {
            $feedback = $this->tutor->parse($str);
            if (strlen($feedback) > 0) {
                $fields = array();
                $fields['classroomid'] = $sesClass;
                $fields['studentid'] = "1";
                $fields['msg'] = $feedback;
                $fields['entrydate'] = date('Y-m-d H:i');
                $fields['updated'] = date('Y-m-d H:i');
                $this->insert($fields);
            }
        }
    }

    /**
     * Function to output a line of text as the facilitator: the string is inserted into the database.
     *
     * @param string $str The facilitators chat input
     * @return
     */
    public function say2($str)
    {
        $sesClass = $this->getSession('classroom');

        $userstr = "facilitator: " . $str;
        $time = date("ymdHis", time());
        $fields = array();
        $fields['classroomid'] = $sesClass;
        $fields['studentid'] = "1";
        $fields['msg'] = $userstr;
        $fields['entrydate'] = $time;
        $fields['updated'] = date('Y-m-d H:i');
        $this->insert($fields);
    }

    /**
    * Method to display the chat log to the user.
    * @return
    */
    public function broadCast()
    {
        $rows=$this->getChat();
        $chat = '';
        if($rows){
            foreach($rows as $row){
                $chat .= $row['msg'].'<br />';
            }
        }
        return $chat;
    }

    /**
     * Method to get the chat from database.
     * Status = 0/1 means the chat is from the current session or restored from the previous session
     * @return array $rows The classrooms chat.
     */
    public function getChat()
    {
        $sesClass = $this->getSession('classroom');

        $sql = "select * from tbl_pbl_chat where classroomid='" . $sesClass . "' and (status='0' or status='1') order by entrydate";
        $rows = $this->getArray($sql);
        if($rows){
            return $rows;
        }else{
            return false;
        }
    }

    /**
     * Method to delete chat sessions that have expired.
     * Status = 1/2 means the chat is older than 2 sessions or was restored in the
     * previous session but is now viewed as old
     *
     * @param string $id The id of the classroom
     * @return bool
     */
    public function deleteChat($id, $all=FALSE)
    {
        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE classroomid='" . $id;
        if(!$all){
            $sql .= "' and (status='2' or status='1')";
        }
        $result = $this->getArray($sql);

        if($result){
            foreach($result as $line){
                $this->delete('id',$line['id']);
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Method to change the status of the chat from current status to the specified status.
     * 2=old, 1=restored, 0=current
     *
     * @param string $id The classroom id
     * @param string $status The status to change the sessions chat to
     * @param string $old The status to change the sessions chat from
     * @return bool
     */
    public function moveChat($id, $status = "2", $old = "0")
    {
        $fields = array();
        $fields['status'] = $status;

        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE classroomid='" . $id . "' and status='" . $old . "'";
        $result = $this->getArray($sql);

        if($result){
            foreach($result as $line){
                $this->update('id',$line['id'],$fields);
            }
            return TRUE;
        }
        return FALSE;
    }
}
?>