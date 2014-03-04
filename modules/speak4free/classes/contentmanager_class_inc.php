<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
class contentmanager extends object {
/**
 * Setup display to ask the user to enter email address for the participant
 * who are to be invited.
 * Added by David Wafula
 * @return <type>
 */
/**
 *
 * @var $objLanguage String object property for holding the
 * language object
 * @access private
 *
 */
    public $objLanguage;

    /**
     *
     * @var $objUser String object property for holding the
     * user object
     * @access private
     *
     */
    public $objUser;

    /**
     *
     * @var $objUser String object property for holding the
     * cobnfiguration object
     * @access private
     *
     */
    public $objConfig;

    public function init() {
    // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        $this->groups=$this->getObject('dbgroups');
        $this->topics=$this->getObject('dbtopics');
        $this->articles=$this->getObject('dbarticles');
        // Instantiate the user object.
        $this->objUser = $this->getObject("user", "security");
        // Instantiate the config object
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objAltConfig = $this->getObject('altconfig','config');
        //$this->objDbSchedules=$this->getObject('dbschedules');
        // scripts
        $extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
        $extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
        $extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
        $maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/session.css').'"/>';
        $schedulejs = '<script language="JavaScript" src="'.$this->getResourceUri('js/schedule.js').'" type="text/javascript"></script>';

        $this->appendArrayVar('headerParams', $extbase);
        $this->appendArrayVar('headerParams', $extalljs);
        $this->appendArrayVar('headerParams', $extallcss);
        $this->appendArrayVar('headerParams', $maincss);
        $this->appendArrayVar('headerParams', $schedulejs);
    }
    public function showTopicList() {

    //where we render the 'popup' window
        $renderSurface='<div id="addsession-win" class="x-hidden">
        <div class="x-window-header">Add Session</div>
        </div>';
        $scheduleTitle='<h2>Topics</h2>';
        $scheduleTitle.='
          <p>Here you will find a listing of topics owned by you or of
          which you are a member.<br/>
          
         Select one to view the articles. You can start you own topic by clicking on the
         <font color="green"><b>Add Session</b></font> button.
         </p>
         ';
        //load class
        $this->loadclass('link','htmlelements');
        $objIcon= $this->newObject('geticon','htmlelements');

        $addButton = new button('add','Add Topic');
        $addButton->setId('add-topic');

        $btns='';
        if($this->objUser->isAdmin()) {
            $btns.=$addButton->show().'&nbsp;&nbsp;';
        }
        $content = $message;
        $content= '<div id="grouping-grid">'.$scheduleTitle.$btns.$renderSurface.'<br /><br /></div>';

        //data grid from db
        $dbdata=$this->groups->getMyTopics($this->objUser->userid());
        $total=count($dbdata);
        $data="";
        foreach($dbdata as $row) {
            $topicdata=$this->topics->getTitle($row['topicid']);

            $deleteLink=new link($this->uri(array('action'=>'deletetopic','topicid'=>$row['topicid'])));
            $objIcon->setIcon('delete');
            $delValJS="deleteTopic(\'".$row['topicid']."\');return false;";
            $objIcon->extra = 'onClick="'.$delValJS.'"';
            $deleteLink->link=$objIcon->show();

            $objIcon= $this->newObject('geticon','htmlelements');
            $editLink=new link($this->uri(array('action'=>'edittopic','topicid'=>$row['topicid'])));
            $objIcon->setIcon('edit');
            $editLink->link=$objIcon->show();

            $detailsLink=new link($this->uri(array('action'=>'topicmembers','topicid'=>$row['topicid'])));
            $detailsLink->link='Members';

            $previewLink=new link($this->uri(array('action'=>'viewstory','storyid'=>$row['topicid'])));
            $previewLink->link='Preview';

            $articleLink=new link($this->uri(array('action'=>'viewtopicarticles','topicid'=>$row['topicid'])));
            $articleLink->link=addslashes($topicdata['title']);

            $membersLink="";
            $deleteTxt="";
            if($this->objUser->isAdmin()) {
                $membersLink=$detailsLink->show();
                $deleteTxt=$deleteLink->show();
            }
            $data.="[";
            $data.= "'".$articleLink->show()."',";
            $data.="'".$membersLink."',";
            $data.="'".$previewLink->show()."',";
            $data.="'".$editLink->show().$deleteTxt."'";
            $data.="],";

        }

        $lastChar = $data[strlen($data)-1];
        $len=strlen($data);
        if($lastChar == ',') {
            $data=substr($data, 0, (strlen ($data)) - (strlen (strrchr($data,','))));
        }
        $submitUrl = $this->uri(array('action' => 'saveschedule'));

        $title='Title';
        $dateCreated='Date Created';
        $details='Details';

        $owner='Owner';
        $edit='Edit';


        $mainjs = "/*!realtime
                 * Ext JS Library 3.0.0
                 * Copyright(c) 2006-2009 Ext JS, LLC
                 * licensing@extjs.com
                 * http://www.extjs.com/license
                 */
                Ext.onReady(function(){

                    Ext.QuickTips.init();
                       var data=[$data];
                       showTopics(data);
                   });
            ";

        $content.= "<script type=\"text/javascript\">".$mainjs."</script>";
        $addtopicurl= $this->uri(array('action'=>'addtopic'));
        $addtopicjs = 'jQuery(document).ready(function() {
 jQuery("#add-topic").click(function() {

window.location=\''.str_replace('amp;','', $addtopicurl).'\';
});
});
';
        $addtopic.= "<script type=\"text/javascript\">".$addtopicjs."</script>";
        return $addtopic.$content;
    }


    public function showTopicMembersList($topicid) {
    //where we render the 'popup' window
        $renderSurface='<div id="addsession-win" class="x-hidden">
        <div class="x-window-header">Add Member</div>
        </div>';
        $scheduleTitle='<h4>Topic Details</h4>';
        //load class
        $this->loadclass('link','htmlelements');
        $objIcon= $this->newObject('geticon','htmlelements');


        $listButton = new button('add','Back to Topic List');
        $returnUrl = $this->uri(array('action' => 'storyadmin'));
        $listButton->setOnClick("window.location='$returnUrl'");
        //prints out add comment message
        if ($this->addCommentMessage) {
            $message = "<span id=\"commentSuccess\">".$this->objLanguage->languageText('mod_ads_commentSuccess', 'ads')."</span><br />";
            $this->addCommentMessage = false;
        } else $message = "";

        $content = $message;
        $content= '<div id="form-panel">'.$scheduleTitle.$listButton->show().'<br/></div>';
        $content.= $renderSurface.'</br><div id="grouping-grid"></div>';

        $dbdata=$this->groups->getMembers($topicid);
        $total=count($dbdata);
        $index=0;
        foreach($dbdata as $row) {
            $deleteLink=new link();

            $deleteLink->link($this->uri(array('action'=>'deletemember','userid'=>$row['userid'],'topicid'=>$topicid)));
            $objIcon->setIcon('delete');
            $deleteLink->link=$objIcon->show();

            $data.="[";
            $data.="'".$this->objUser->fullname($row['userid'])."',";
            $data.="'Default',";
            $data.="'".$deleteLink->show()."'";
            $data.="]";
            $index++;
            if($index <= $total-1) {
                $data.=',';
            }
        }

        $usrdata=$this->groups->getUsers();
        $total=count($usrdata);
        $index=0;
        $userlist="";
        foreach($usrdata as $row) {
            $userlist.="[";
            $userlist.="'".$row['userid']."',";
            $userlist.="'".$row['surname']." ".$row['firstname']."'";
            $userlist.="]";
            $index++;
            if($index <= $total-1) {
                $userlist.=',';
            }
        }

        $addmemberurl = $this->uri(array('action' => 'addmembertotopic','topicid'=>$topicid));

        $title='Name';
        $group='Group';
        $edit='Edit';

        $mainjs = "
                Ext.onReady(function(){
                var addmemberUrl='".str_replace("amp;", "", $addmemberurl)."';
                var userlist=[$userlist];
                initAddMember(userlist,addmemberUrl);
                var membersdata=[$data];
                showSessionDetails(membersdata);
                });
            ";

        $content.= "<div id=\"buttons-layer\"></div><script type=\"text/javascript\">".$mainjs."</script>";


        return $content;
    }


    public function showArticleList($topicid) {

    //where we render the 'popup' window
        $renderSurface='<div id="addsession-win" class="x-hidden">
        <div class="x-window-header">Add Session</div>
        </div>';

        $topicLink=new link($this->uri(array('action'=>'storyadmin')));
        $topicLink->link='Topic list';

        $scheduleTitle='<h2>Articles</h2>';
        $scheduleTitle.='<h3>'.$topicLink->show().'</h3>';
        $scheduleTitle.='
          <p>Here you will find a listing of articles for selected topic.<br/>

         </p>
         ';


        //load class
        $this->loadclass('link','htmlelements');
        $objIcon= $this->newObject('geticon','htmlelements');


        $addButton = new button('add','Add Article');
        $addButton->setId('add-article');


        $content = $message;
        $content= '<div id="grouping-grid">'.$scheduleTitle.$addButton->show().$renderSurface.'<br /><br /></div>';

        //data grid from db
        $dbdata=$this->articles->getArticles($topicid);
        $total=count($dbdata);
        $data="";
        foreach($dbdata as $row) {


            $deleteLink=new link($this->uri(array('action'=>'deletearticle','articleid'=>$row['id'],'topicid'=>$topicid)));
            $objIcon->setIcon('delete');
            $delValJS="deleteArticle(\'".$row['id']."\',\'".$topicid."\');return false;";
            $objIcon->extra = 'onClick="'.$delValJS.'"';
            $deleteLink->link=$objIcon->show();



            $objIcon= $this->newObject('geticon','htmlelements');
            $editLink=new link($this->uri(array('action'=>'editarticle','articleid'=>$row['id'],'topicid'=>$topicid)));
            $objIcon->setIcon('edit');
            $editLink->link=$objIcon->show();

            $link = new link ($this->uri(array('action'=>'viewarticle','storyid'=>$topicid,'articleid'=>$row['id'])));
            $link->link = addslashes($row['title']);

            $data.="[";
            $data.= "'".$link->show()."',";
            $data.="'".$editLink->show().'&nbsp;&nbsp;&nbsp;'.$deleteLink->show()."'";
            $data.="],";

        }

        $lastChar = $data[strlen($data)-1];
        $len=strlen($data);
        if($lastChar == ',') {
            $data=substr($data, 0, (strlen ($data)) - (strlen (strrchr($data,','))));
        }


        $title='Title';
        $dateCreated='Date Created';
        $details='Details';

        $owner='Owner';
        $edit='Edit';


        $mainjs = "/*!realtime
                 * Ext JS Library 3.0.0
                 * Copyright(c) 2006-2009 Ext JS, LLC
                 * licensing@extjs.com
                 * http://www.extjs.com/license
                 */
                Ext.onReady(function(){

                    Ext.QuickTips.init();
                       var data=[$data];
                       showArticles(data);
                   });
            ";

        $content.= "<script type=\"text/javascript\">".$mainjs."</script>";
        $addarticleurl= $this->uri(array('action'=>'addarticle','topicid'=>$topicid));
        $addtopicjs = 'jQuery(document).ready(function() {
 jQuery("#add-article").click(function() {

window.location=\''.str_replace('amp;','', $addarticleurl).'\';
});
});
';
        $addtopic.= "<script type=\"text/javascript\">".$addtopicjs."</script>";

        return $addtopic.$content;
    }

}
?>