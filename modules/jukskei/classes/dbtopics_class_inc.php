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
class dbtopics  extends dbTable {

    public function init() {
        parent::init('tbl_jukskei_topics');
        $this->table = 'tbl_jukskei_topics';
    }
    public function getTopics() {
        return $this->getAll('where active = "1"');
    }

    public function savetopic($title,$content,$active) {
        $data=array('title'=>$title,'content'=>$content,'active'=>$active);
        return $this->insert($data);
    }

    public function updatetopic($title,$content,$topicid,$active) {
        $data=array('title'=>$title,'content'=>$content,'active'=>$active);
        return $this->update('id',$topicid, $data);
    }
    public function getTitle($topicid) {
        return $this->getRow('id',$topicid);
    }

    public function getTopic($id) {
        return $this->getRow('id',$id);
    }
    public function deletetopic($id) {
        $groups=$this->getObject('dbgroups');
        $groups->deleteTopic($id);
        return $this->delete('id',$id);
    }
}
?>
