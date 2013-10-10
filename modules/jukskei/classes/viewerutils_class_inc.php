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
        $this->storyparser=$this->getObject('storyparser');
        $this->topics=$this->getObject('dbtopics');
        $this->articles=$this->getObject('dbarticles');
    }

    public function getHomePageContent() {

        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('storyparser');
        $id=$this->objDbSysconfig->getValue('DEFAULT_STORY_ID','jukskei');
        $data= $objStories->getStory($id);
        $content='';
        $topicsnavid=$this->objDbSysconfig->getValue('TOPICS_NAV_CATEGORY','jukskei');
        //$topicnavs=$this->storyparser->getStoryByCategory($topicsnavid);
        $topicsnavs=$this->topics->getTopics();
        $navbar='';

        foreach($topicsnavs as $nav) {
        // $menuOptions[]=    array('action'=>'viewstory','storyid'=>$nav['id'], 'text'=>$nav['title'], 'actioncheck'=>array(), 'module'=>'jukskei', 'status'=>'both');
            $link = new link ($this->uri(array('action'=>'viewstory','storyid'=>$nav['id'])));
            $link->link ='<b>'. $nav['title'].'</b>';
            $navbar.=$link->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $content='

<div id="contentwrapper" class="subcolumns">

       '.$this->getHomePageMedia().'

</div>
<div id="contentwrapper" class="subcolumns">

     <br/><font style="font-family:Arial;font-size:24;">  '.$navbar.'</font><br/><br/><br/>
<font style="color:#1A4048;> '.$this->objWashout->parseText($data['maintext']).'</font>


</div>';

        $articles='<div id="articles"><center><a href="http://www.wits.ac.za">www.wits.ac.za</a></center></div>';
        return $content.$articles;
    }

    public function getHomePageMedia() {

        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('storyparser');
        $id=$this->objDbSysconfig->getValue('DEFAULT_MEDIA_ID','jukskei');
        $data= $objStories->getStory($id);
        $content='';

        $content='

            <ul class="paneltabs">

            '.$this->objWashout->parseText($data['maintext']).'</ul>';

        return $content;
    }

    public function getTopicsContent($parentid) {

        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('storyparser');

        if($parentid == '') {
            $category=$this->objDbSysconfig->getValue('TOPIC_CATEGORY','jukskei');
            $data= $objStories->getHomePageContent($category);
            $parentid=$data[0]['id'];
        }
        //$topics=$objStories->getTopics($parentid);
        $topics=$this->topics->getTopics();
        $content='';
        $homepagetitlelinks='';
        foreach($topics as $topic) {
            $link=new link($this->uri(array("id"=>$topic['id'],'action'=>'viewtopic')));
            $link->link=$topic['title'];
            $homepagetitlelinks.=$link->show().'&nbsp;|&nbsp;';
        }
        $defaulttopicid=$topics[0]['id'];

        //$defaulttopiccontent=$objStories->getTopic($defaulttopicid);
        $defaulttopiccontent=$this->topics->getTopic($defaulttopicid);

        $content='
            <ul class="paneltabs">

            '.$defaulttopiccontent[0]['content'].'</ul>';

        return $content;
    }
    public function getTopicContent($id) {
        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('storyparser');
        //$topics=$objStories->getTopics($id);
        $topics=$this->topics->getTopics($id);
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

            '.$this->objWashout->parseText($defaulttopiccontent[0]['content']).'

            </ul>
            <br/>
              ';
        $content.='<div class="sectionstats">';
        $content.='<div class="subcolumns">';
        $content.='</div">';
        $content.='</div">';


        return $content;
    }

    public function getTopic($id) {

        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('storyparser');
        $data= $this->topics->getTopic($id);
        $topicsnavid=$this->objDbSysconfig->getValue('TOPICS_NAV_CATEGORY','jukskei');
        $topicnavs=$this->topics->getTopics();
        $link = new link ($this->uri(array('action'=>'home')));
        $link->link = 'HOME&nbsp;&nbsp;|&nbsp;&nbsp;';
        $navbar=$link->show();
        foreach($topicnavs as $nav) {
            $link = new link ($this->uri(array('action'=>'viewstory','storyid'=>$nav['id'])));
            $link->link = $nav['title'];
            $navbar.=$link->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        $articlenav='';
        $articles=$this->articles->getArticles($id);
        foreach($articles as $nav) {
            $link = new link ($this->uri(array('action'=>'viewarticle','storyid'=>$id,'articleid'=>$nav['id'])));
            $link->link = $nav['title'];
            $articlenav.=$link->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        $content='';
        $content='

            <ul class="paneltabs">

             <font style="font-family:Arial;font-size:20;">  '.$navbar.'</font><hr>
       
             Current topic: <font style="font-family:Arial;font-size:18;font-weight:bold;">'.$data['title'].'</font><br/>
            <font style="font-family:Arial;font-size:14;"> '.$this->objWashout->parseText($data['content']).'</font>

            </ul>
            <br/>
              ';
        $articles='<div id="articles"><center>'.$articlenav.'</center></div>';
        return $content.$articles;
    }
    public function getArticleContent($storyid,$articleid) {

        $objTrim = $this->getObject('trimstr', 'strings');
        $objStories=$this->getObject('storyparser');
        $data= $this->articles->getArticle($articleid);
        $storydata= $this->topics->getTopic($storyid);
        $storylink=new link ($this->uri(array('action'=>'viewstory','storyid'=>$storyid)));
        $storylink->link=$storydata['title'];
        $topicsnavid=$this->objDbSysconfig->getValue('TOPICS_NAV_CATEGORY','jukskei');
        $topicnavs=$this->topics->getTopics();
        $link = new link ($this->uri(array('action'=>'home')));
        $link->link = 'HOME&nbsp;&nbsp;|&nbsp;&nbsp;';
        $navbar=$link->show();
        foreach($topicnavs as $nav) {
            $link = new link ($this->uri(array('action'=>'viewstory','storyid'=>$nav['id'])));
            $link->link = $nav['title'];
            $navbar.=$link->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        $articlenav='';
        $articles=$this->articles->getArticles($storyid);
        foreach($articles as $nav) {
            $link = new link ($this->uri(array('action'=>'viewarticle','storyid'=>$storyid,'articleid'=>$nav['id'])));
            $link->link = $nav['title'];
            $articlenav.=$link->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        //articles
        $topcatid=$this->objDbSysconfig->getValue('TOP_NAV_CATEGORY','jukskei');
        $topnavs=$this->storyparser->getStoryByCategory($topcatid);
        $articles='<div id="articles">';
        foreach($topnavs as $nav) {
            $link=new link($this->uri(array('action'=>'viewstory','storyid'=>$nav['id'])));
            $link->link=$nav['title'];
            $articles.=$link->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
         $articles.='<br/><br/>'.$articlenav.'<br/></div>';
        $content='';
        $content='

          

             <font style="font-family:Arial;font-size:20;">  '.$navbar.'</font><hr>
             <b style="font-family:Arial;font-size:24;">Topic: '.$storylink->show().'</b><br/>
             <b style="font-family:Arial;font-size:24;">Article: '.$data['title'].'&nbsp;&nbsp;</b>
            <font style="font-family:Arial;font-size:24;color:#1A4048"'.$this->objWashout->parseText($data['content']).'</font>
             <center>'.$articles.'</center>
            
            <br/>
              ';

        return $content;
    }

}

?>
