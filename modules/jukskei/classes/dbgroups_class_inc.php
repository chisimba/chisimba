<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Description of storyparser_class_inc
 *
 * @author kim
 */
class dbgroups  extends dbTable {

    public function init() {
        parent::init('tbl_jukskei_groups');
        $this->table = 'tbl_jukskei_groups';
        $this->objUser=$this->getObject('user','security');
    }
    public function adduser($topicid,$userid=NULL) {
        if($userid == null) {
            $userid=$this->objUser->userId();
        }
        $data=array('topicid'=>$topicid,'userid'=>$userid);
        return $this->insert($data);
    }
    public function getMembers($topicid) {
        $sql=" where topicid = '$topicid'";
        return $this->getAll($sql);
    }
    public function getUsers() {
        $sql="select * from tbl_users";
        return $this->getArray($sql);
    }
    public function deleteMember($userid) {
        return $this->delete('userid',$userid);

    }
    public function getMyTopics($userid) {
        $sql=" where userid = '$userid'";
        return $this->getAll($sql);
    }
    public function deleteTopic($id) {
        $this->delete('topicid', $id);
    }
}
?>
