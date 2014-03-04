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
class storyparser  extends dbTable {

    public function init() {
        parent::init('tbl_stories');
        $this->table = 'tbl_stories';
    }
    public function getTopics($parentid) {
        $sql="select * from tbl_cms_sections where parentid='$parentid' and trash='0' and published='1'";
        $data = $this->getArray($sql);
        return $data;
    }
    public function getStory($id) {
        $data = $this->getRow('id',$id);
        return $data;
    }
    public function getArticleByAbstract($abs) {
        $sql=" where abstract = '$abs'";
        $data = $this->getAll($sql);
        return $data;
    }
    public function getStoryByCategory($category) {
        $sql=" where category = '$category'";
        $data = $this->getAll($sql);
        return $data;
    }
    public function getTopic($id) {
        $sql="select * from tbl_cms_sections where id='$id'";
        $data = $this->getArray($sql);
        return $data;
    }
    public function getParent($id) {
        $sql="select * from tbl_cms_sections where id='$id'";
        $data = $this->getArray($sql);
        $parentData=$this->getTopic($data[0]['parentid']);
        return $parentData;
    }
    public function getHomePageContent($category) {
        $objWashout = $this->getObject('washout', 'utilities');
        $sql="select * from tbl_cms_sections where  title = '$category'";
        $data = $this->getArray($sql);
        return $data;
    }

    public function getTopicTitles($category) {
        $objWashout = $this->getObject('washout', 'utilities');
        $sql="select * from tbl_cms_sections where  title = $category";
        $data = $this->getArray($sql);
        return $data;
    }

    public function getArticleTitles($category,$article) {
        $objWashout = $this->getObject('washout', 'utilities');
        $sql="select * from $this->table where  category like '$category%_$article%' and isActive='1'";

        $data = $this->getArray($sql);
        return $data;
    }
}
?>
