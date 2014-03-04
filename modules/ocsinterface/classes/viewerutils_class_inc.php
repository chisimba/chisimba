<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
class viewerutils extends object {

    public function init() {
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->loadClass('link', 'htmlelements');
        $this->objWashout = $this->getObject('washout', 'utilities');
    }
    public function getHomePageContent() {

        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('ocsstoryparser');
        $storyid=$this->objDbSysconfig->getValue('DEFAULT_STORY_ID','ocsinterface');
        $data= $objStories->getStory($storyid);
        $content='';

        $content='
          
            
            '.$this->objWashout->parseText($data['maintext']).'
            
            <br/>
              ';

        return $content;
    }
    public function getTopicsContent($parentid) {


        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('ocsstoryparser');

        if($parentid == '') {
            $category=$this->objDbSysconfig->getValue('TOPIC_CATEGORY','ocsinterface');
            $data= $objStories->getHomePageContent($category);
            $parentid=$data[0]['id'];
        }
        $topics=$objStories->getTopics($parentid);
        $content='';
        $homepagetitlelinks='';
        foreach($topics as $topic) {
            $link=new link($this->uri(array("id"=>$topic['id'],'action'=>'viewtopic')));
            $link->link=$topic['title'];
            $homepagetitlelinks.=$link->show().'&nbsp;|&nbsp;';
        }
        $defaulttopicid=$topics[0]['id'];

        $defaulttopiccontent=$objStories->getTopic($defaulttopicid);
        ;

        $content='
            <h4>'.$homepagetitlelinks.'</h4>

            <ul class="paneltabs">

          '.$this->objWashout->parseText($defaulttopiccontent[0]['description']).'

            </ul>
            <br/>
              ';
        $content.='<div class="sectionstats">';
        $content.='<div class="subcolumns">';
        $content.='</div">';
        $content.='</div">';
        return $content;
    }
    public function getTopicContent($id) {


        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('ocsstoryparser');
        $topics=$objStories->getTopics($id);
        $content='';
        $navlinks='';
        foreach($topics as $topic) {
            $link=new link($this->uri(array("id"=>$topic['id'],'action'=>'viewtopic')));
            $link->link=$topic['title'];
            $navlinks.=$link->show().'&nbsp;|&nbsp;';
        }
        $defaulttopicid=$topics[0]['id'];

        if(count($topics) < 1) {
            $defaulttopicid=$id;
        }
        $parentdata=$objStories->getParent($defaulttopicid);
        $grandparentdata=$objStories->getParent($parentdata[0]['id']);
        $parentlink=new link($this->uri(array("parentid"=>$grandparentdata[0]['id'],'action'=>'viewtopics')));
        $parentlink->link=$parentdata[0]['title'];
        $defaulttopiccontent=$objStories->getTopic($defaulttopicid);


        $content='
            <h2>'.$parentlink->show().'</h2><h4>'.$navlinks.'</h4>

            <ul class="paneltabs">

            '.$this->objWashout->parseText($defaulttopiccontent[0]['description']).'

            </ul>
            <br/>
              ';
        $content.='<div class="sectionstats">';
        $content.='<div class="subcolumns">';
        $content.='</div">';
        $content.='</div">';
        return $content;
    }

    public function getArticlesContent($topic, $title) {

        $this->loadClass('link', 'htmlelements');
        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('ocsstoryparser');

        $title == '' ?'Living off the river':$title;
        $articlecontent=$objStories->getStoryByTitle($title,'juk_articles');
        $content='';
        $topiclink1=new link($this->uri(array("title"=>'Living off the river','action'=>'viewtopic')));
        $topiclink1->link='Living off the river';

        $topiclink2=new link($this->uri(array("title"=>"ocsinterfaces past and present",'action'=>'viewtopic')));
        $topiclink2->link="ocsinterface's past and present";

        $topiclink3=new link($this->uri(array("title"=>'Environment and water','action'=>'viewtopic')));
        $topiclink3->link='Environment and water';



        $homepagetitle=$link1->show().'&nbsp;|&nbsp;'.$link2->show().'&nbsp;|&nbsp;'.$link3->show();
        $content='
            <h4>'.$homepagetitle.'</h4>

            <ul class="paneltabs">

            '.$articlecontent.'

            </ul>
            <br/>
              ';
        $content.='<div class="sectionstats">';
        $content.='<div class="subcolumns">';
        $content.='</div">';
        $content.='</div">';
        return $content;
    }
    public function createCell($colType) {
        $str='<div class="'.$colType.'">
              <div class="subcl">
              <div class="sectionstats_content">

              <div class="statslistcontainer">

              <ul class="statslist">

              <li class="sectionstats_first">
              cell content can go in here
            cell content can go in here
cell content can go in here
cell content can go in here
cell content can go in here
 <br/><br/></br><br/><br/><br/><br/><br/><br/>
              </li>

              </ul>
 <div class="clear"></div>

              </div>
              </div>
              </div>
              </div>';
        return $str;
    }

    public function getContent($id) {

        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('ocsstoryparser');
        $data= $objStories->getStory($id);

        $content='';
        $content='

          
            '.$this->objWashout->parseText($data['maintext']).'

        
            <br/>
              ';

        return $content;
    }


}

?>
