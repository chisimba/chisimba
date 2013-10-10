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
class dbarticles  extends dbTable {

    public function init() {
        parent::init('tbl_jukskei_articles');
        $this->table = 'tbl_jukskei_articles';
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
