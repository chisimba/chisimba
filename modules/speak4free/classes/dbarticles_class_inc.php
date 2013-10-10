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
class dbarticles  extends dbTable {

    public function init() {
        parent::init('tbl_speak4free_articles');
        $this->table = 'tbl_speak4free_articles';
    }
    public function getArticles($topicid) {
        $sql="where topicid='$topicid'";
        return $this->getAll($sql);
    }

    public function saveArticle($title,$content,$topicid) {
        $data=array('title'=>$title,'content'=>$content,'topicid'=>$topicid);
        return $this->insert($data);
    }

    public function updateArticle($title,$content,$articleid) {
        $data=array('title'=>$title,'content'=>$content);
        return $this->update('id',$articleid, $data);
    }

    
    public function getArticle($id) {
        return $this->getRow('id',$id);
    }
    public function deleteArticle($id) {
        return $this->delete('id',$id);
    }
}
?>
