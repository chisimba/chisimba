<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of storyparser_class_inc
 *
 * @author kim
 */
class dbtopics  extends dbTable {

    public function init() {
        parent::init('tbl_stories');
        $this->table = 'tbl_stories';
    }
    public function getTopics() {
        return $this->getAll();
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

    public function getTopic($category) {
        return $this->getRow('category',$category);
    }
    public function deletetopic($id) {
        $groups=$this->getObject('dbgroups');
        $groups->deleteTopic($id);
        return $this->delete('id',$id);
    }
}
?>
