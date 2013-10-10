<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die('You cannot view this page directly');
}
// end security check

/**
* Controller class for the Essay Management module.
* Lecturers can create, edit and delete topics.
* They can create, edit and delete essays
* within each topic and download students'
* submitted essays for marking. They can mark
* essays and upload marked essays.
*
* @author Megan Watson
* @author Jeremy O'Connor
* @copyright (c) 2004, 2010 Avoir
* @package essayadmin
* @version $Id: controller.php 24138 2012-04-28 15:17:33Z dkeats $
*/

/**
* Controller class for the Essay Management module.
* @package essayadmin
*/

class essayadmin extends controller
{
    /**
    * Initialization method.
    */
    public function init()
    {
        $this->objConfig = $this->newObject('altconfig', 'config');
        $this->dbtopic = $this->getObject('dbessay_topics', 'essay');
        $this->dbessays = $this->getObject('dbessays', 'essay');
        $this->dbbook = $this->getObject('dbessay_book', 'essay');
        // Get instances of the html elements:
        $this->loadclass('htmltable', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadclass('form', 'htmlelements');
        $this->loadClass('layer', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('iframe', 'htmlelements');
        $this->loadClass('htmlHeading', 'htmlelements');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objDate = $this->newObject('datepicker', 'htmlelements');
		$this->objFile = $this->newObject('upload', 'filemanager');
        // Get an instance of the confirmation object
        $this->objConfirm = $this->getObject('confirm', 'utilities');
       	$this->objDateformat = $this->newObject('dateandtime','utilities');
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the user object
        $this->objUser = $this->getObject('user', 'security');
        // Get an instance of the context object
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
        $this->objHelp = $this->newObject('helplink', 'help');
        $this->objModules = $this->newObject('modules', 'modulecatalogue');
        //if(!$this->objModules->checkIfRegistered('Essay Management','essayadmin')){
        //    return $this->nextAction('notregistered', array(), 'redirect');
        //}
        // Check if the assignment module is registered and can be linked to.
        $this->assignment = $this->objModules->checkIfRegistered('assignment');
        if (!$this->objModules->checkIfRegistered('rubric')) {
            $this->rubric = FALSE;
        } else {
            $this->rubric = TRUE;
            $this->objRubric = $this->getObject('dbrubricassessments', 'rubric');
        }
        // Log this call if registered
        if ($this->objModules->checkIfRegistered('logger', 'logger')){
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
        }
        // Load the activity Streamer
        if (!$this->objModules->checkIfRegistered('activitystreamer'))
        {
        	$this->eventsEnabled = FALSE;
        } else {
        	$this->eventsEnabled = TRUE;
        	$this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
        	$this->eventDispatcher->addObserver ( array ($this->objActivityStreamer, 'postmade' ) );
        }
    }

    /**
    * The standard dispatch method for the module.
    * @return string The template
    */
    public function dispatch($action)
    {
        $this->setVar('pageSuppressXML',true);
		/**
		* management of zip files, added 27/mar/06
		* check if the essayadmin dir has been created
		* @author: otim samuel, sotim@dicts.mak.ac.ug
		*/
        //"usrfiles/essayadmin/"
		$essayadmindir = $this->objConfig->getcontentBasePath().'essayadmin/';
		if(!is_dir($essayadmindir)) {
			mkdir($essayadmindir, 0777);
		}
		$this->setVar('essayadmindir',$essayadmindir);
		/*
		* $essayadminDownloadLink is currently made up of
		* http://nextgen.mak.ac.ug/index.php?module=essayadmin
		* or the equivalent. required is to remove
		* index.php?module=essayadmin or its equivalent
		* $_SERVER['QUERY_STRING'] contains everthing after the ?
		* hence by appending this variable to index.php? and running
		* ereg_replace("index.php\?".$_SERVER['QUERY_STRING'],"",$essayadminDownloadLink)
		* add a \? cause the ? is taken as a regular expression
		* should give us http://nextgen.mak.ac.ug/ which can then be appended to
		* $essayadmindir and the required download file for an accurate download link
		*/
		$essayadminDownloadLink =
		    $this->objConfig->getsiteRoot()
		    .$this->objConfig->getcontentPath()
		    .'essayadmin/';
		$this->setVar('essayadminDownloadLink',$essayadminDownloadLink);
		//remove all zip files older than 24hrs, or 86,400 seconds
		//$this->objDbZip->deleteOldFiles();
        // Get user details
        $this->userId = $this->objUser->userId();
        $this->user = $this->objUser->fullname();
        // Check if in context, and get code & title
        if ($this->objContext->isInContext()) {
            $incontext = TRUE;
            $this->contextcode = $this->objContext->getContextCode();
            $this->context = $this->objContext->getTitle();
        } else {
            $incontext = FALSE;
        }

        if (!$this->objUser->isCourseAdmin($this->contextcode)) {
            return 'noaccess_tpl.php';
        }

        // Set variable references in templates
        $this->setVarByRef('contextcode', $this->contextcode);
        $this->setVarByRef('context', $this->context);

        //$topicid=$this->getParam('id');
        switch($action){
        case 'addtopic':
            // Add a new topic
            $heading = $this->objLanguage->languageText('mod_essayadmin_newtopicarea','essayadmin');
            $data = array();
            $this->setVarByRef('heading', $heading);
            $this->setVarByRef('data', $data);
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'topic_tpl.php';
        //break;
        // edit a topic
        case 'edit':
        case 'edittopic':
            // get topic id
            $id=$this->getParam('id');
            // get topic details
            $data = $this->dbtopic->getTopic($id);
            $heading = $this->objLanguage->languageText('mod_essayadmin_edittopicarea','essayadmin').': '.$data[0]['name'];
            $this->setVarByRef('heading',$heading);
            $this->setVarByRef('data',$data);
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'topic_tpl.php';
        //break;
        // save topic
        case 'savetopic':
            $id = $this->getParam('id', NULL);
            $fields = array();
            $fields['context']=$this->contextcode;
            $fields['name']=$this->getParam('topicarea', '');
            $fields['description']=$this->getParam('description', '');
            $fields['instructions']=$this->getParam('instructions', '');
            $fields['percentage']=$this->getParam('percentage', '');
            $fields['closing_date']= $this->getParam('closing_date');
            $force = $this->getParam('force',NULL);
            $bypass = $this->getParam('bypass',NULL);
            $fields['forceone'] = ($force == 'on')?'1':'0';
            $fields['bypass'] = ($bypass == 'on')?'1':'0';
            if(is_null($id)){
                $this->dbtopic->addTopic($fields);
                $id=$this->dbtopic->getLastInsertId();
            } else {
                $this->dbtopic->addTopic($fields,$id);
            }
            // set confirmation message
            $message = $this->objLanguage->languageText('mod_essayadmin_confirmtopicarea','essayadmin');
            $this->setSession('confirm', $message);
            //add to activity log
            if($this->eventsEnabled)
            {
                $message = $this->objUser->getsurname()." ".$this->objLanguage->languageText('mod_essayadmin_hasaddedessay', 'essayadmin').": ".$fields['name']." ".$this->objLanguage->languageText('mod_essayadmin_in', 'essayadmin')." ".$this->objContext->getContextCode();
                $this->eventDispatcher->post(
                    $this->objActivityStreamer,
                    "context",
                    array(
                        'title'=>$message,
                		'link'=>$this->uri(array()),
                		'contextcode'=>$this->objContext->getContextCode(),
                		'author'=>$this->objUser->fullname(),
                		'description'=>$message)
                );
            }
            return $this->nextAction('view', array('id' => $id, 'confirm' => 'yes'));
            //}
            //$this->nextAction('');
        break;
        // view essays within a topic
        case 'view':
            // get id of topic being viewed
            $topicAreaId = $this->getParam('id');
            // get table display[0]['essayid']
            //$content=; //$essays,$topic
            $this->setVar('content', $this->renderEssays($topicAreaId));
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'essay_tpl.php';
        //break;
        // delete a topic
        case 'delete':
        case 'deletetopic':
            // get topic id
            $topicAreaId=$this->getParam('id');
            $this->dbtopic->deleteTopic($topicAreaId);
            //$this->deleteEssay($id);
            $rows=$this->dbessays->getEssays($topicAreaId);
            if(!empty($rows)){
                foreach($rows as $item){
                    $essayId = $item['id'];
                    $this->dbessays->deleteEssay($essayId);
                    // delete bookings on essay
                    $this->dbbook->deleteBooking(NULL, "WHERE topicid='{$topicAreaId}' AND essayid='{$essayId}'");
                }
            }
            $back=$this->getParam('back');
            if($back){
                header("Location: ".$this->uri(array('action'=>'viewbyletter'),'assignmentadmin'));
                return NULL;
            }else{
                return $this->nextAction(NULL);
            }
        //break;
        case 'addessay':
            // Add essay
            // get topic id
            $topicAreaId=$this->getParam('id');
            // get topic data
            $topic=$this->dbtopic->getTopic($topicAreaId,'name, id');
            $heading = $this->objLanguage->languageText('mod_essayadmin_addessay','essayadmin').':&nbsp;'.$topic[0]['name'];
            $data=array();
            $this->setVarByRef('topicid',$topic[0]['id']);
            $this->setVarByRef('topicname',$topic[0]['name']);
            $this->setVarByRef('heading',$heading);
            $this->setVarByRef('data',$data);
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'addeditessay_tpl.php';
        //break;
        case 'editessay':
            // edit essay
            // get id of topic
            $topicAreaId=$this->getParam('id');
            // get id of essay
            $essay=$this->getParam('essay');
            // get topic data
            $topic=$this->dbtopic->getTopic($topicAreaId,'id, name');
            // get essay data
            $data=$this->dbessays->getEssay($essay);
            $heading = $this->objLanguage->languageText('mod_essayadmin_editessay','essayadmin').':&nbsp;'.$topic[0]['name'];
            $this->setVarByRef('topicid',$topic[0]['id']);
            $this->setVarByRef('topicname',$topic[0]['name']);
            $this->setVarByRef('heading',$heading);
            $this->setVarByRef('data',$data);
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'addeditessay_tpl.php';
        //break;
        case 'saveessay':
            // save essay
            //$confirm = NULL;
            //if($this->getParam('save')==$this->objLanguage->languageText('word_save')){
            $topicAreaId = $this->getParam('id', '');
            $id=$this->getParam('essay');

            $fields=array();
            $fields['topicid']=$topicAreaId;
            $fields['topic']=$this->getParam('essaytopic', '');
            $fields['notes']=$this->getParam('notes', '');
            // set confirmation message
            $message = $this->objLanguage->languageText('mod_essayadmin_confirmessay', 'essayadmin');
            $this->setSession('confirm', $message);
            //$confirm = ;
            //}
            return $this->nextAction('view',array('id'=>$topicAreaId, 'confirm' => 'yes'));
        //break;
        case 'deleteessay':
            // delete an essay
            // get topic id
            $topicAreaId=$this->getParam('id');
            // get essay id
            $essayId=$this->getParam('essay');
            //$this->deleteEssay($topicAreaId, $id);
            $this->dbessays->deleteEssay($essayId);
            // delete bookings on essay
            $this->dbbook->deleteBooking(NULL,"WHERE topicid='{$topicAreaId}' and essayid='{$essayId}'");
            return $this->nextAction('view',array('id'=>$topicAreaId));
        //break;
        case 'mark':
        //case 'viewmarktopic':
        case 'marktopic':
            // list student essay submissions
            // get topic id
            $topicAreaId=$this->getParam('id');
            $topicdata=$this->dbtopic->getTopic($topicAreaId,'id, name, closing_date');
            // get booked essays in topic
            $data=$this->dbbook->getBooking("WHERE topicid='{$topicAreaId}'");
            // get essay titles and student names for each booked essay
            foreach($data as $key=>$item){
                $essay=$this->dbessays->getEssay($item['essayid'],'topic');
                $data[$key]['essay']=$essay[0]['topic'];
                //$student=$this->objUser->fullname($item['studentid']);
                //$data[$key]['student']=$student;//[0]['fullname'];
                $data[$key]['studentNo']=$this->objUser->getStaffNumber($item['studentid']); //[0]['fullname'];
                $data[$key]['student']=$this->objUser->fullname($item['studentid']); //$student;//[0]['fullname'];
            }
            $this->setVar('heading', $this->objLanguage->code2Txt('mod_essayadmin_submittedessaysintopicarea','essayadmin', array('TOPICAREA'=>$topicdata[0]['name'])));
            $this->setVarByRef('topicdata',$topicdata);
            $this->setVarByRef('data',$data);
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'mark_essays_tpl.php';
        case 'download':
            $this->setVar('fileId', $this->getParam('fileid'));
            $this->setPageTemplate(NULL);
            $this->setLayoutTemplate(NULL);
            return 'download_tpl.php';
        //break;
        case 'upload':
            // Upload essay
            // Get topic area ID
            $topic=$this->getParam('id');
            // Get book ID
            $id=$this->getParam('book');
            // Get rubric ID
            $rubric=$this->getParam('rubric');
            //
            $message = $this->getSession('message','');
            $mark = $this->getSession('mark','0');
            $comment = $this->getSession('comment','');
            $this->unsetSession('message');
            $this->unsetSession('mark');
            $this->unsetSession('comment');
            $this->setVar('message', $message);
            $this->setVar('mark', $mark);
            $this->setVar('comment', $comment);
            //
            $this->setVarByRef('heading', $this->objLanguage->languageText('mod_essayadmin_markessay','essayadmin'));
            $this->setVar('topic', $topic);
            $this->setVar('book', $id);
            $this->setvar('rubric', $rubric);
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'upload_tpl.php';
        //break;
        case 'uploadsubmit':
            // Mark an essay and upload marked essay
            // Get topic ID
            $topic = $this->getParam('id');
            // Get book ID
        	$book = $this->getParam('book');
            $mark=$this->getParam('mark', '');
            $comment=$this->getParam('comment', '');
            // upload file to database, overwrite original file
            $fileDetails = $this->objFile->uploadFile('file');

            if ($fileDetails === FALSE){
                $message = $this->objLanguage->languageText('mod_essayadmin_uploadfailureunknown', 'essayadmin');
            } else if (!$fileDetails['success']) {
                switch ($fileDetails['reason']) {
                    case 'bannedfile':
                        $reason = $this->objLanguage->languageText('mod_essayadmin_fileupload_bannedfile', 'essayadmin');
                        break;
                    case 'partialuploaded':
                        $reason = $this->objLanguage->languageText('mod_essayadmin_fileupload_partialuploaded', 'essayadmin');
                        break;
                    case 'nouploadedfileprovided':
                        $reason = $this->objLanguage->languageText('mod_essayadmin_fileupload_nouploadedfileprovided', 'essayadmin');
                        break;
                    case 'doesnotmeetextension':
                        $reason = $this->objLanguage->languageText('mod_essayadmin_fileupload_doesnotmeetextension', 'essayadmin');
                        break;
                    case 'needsoverwrite':
                        $reason = $this->objLanguage->languageText('mod_essayadmin_fileupload_needsoverwrite', 'essayadmin');
                        break;
                    case 'filecouldnotbesaved':
                        $reason = $this->objLanguage->languageText('mod_essayadmin_fileupload_filecouldnotbesaved', 'essayadmin');
                        break;
                    default:
                        $reason = $this->objLanguage->languageText('mod_essayadmin_fileupload_unknownreason', 'essayadmin');
                }
                $message = $this->objLanguage->languageText('mod_essayadmin_uploadfailure', 'essayadmin')
                   .":&nbsp;" . $reason;
            } else {
                $fields = array(
                    'mark'=>$mark,
                    'comment'=>$comment,
                    'lecturerfileid'=>$fileDetails['fileid']
                );
                $this->dbbook->bookEssay($fields, $book);
                // display success message
                $message = NULL;
            	}
            if (!is_null($message)) {
    			$this->setSession('message',$message);
    			$this->setSession('mark',$mark);
    			$this->setSession('comment',$comment);
                return $this->nextAction('upload', array('id'=>$topic, 'book'=>$book));
            }
            else {
                return $this->nextAction('marktopic', array('id'=>$topic));
            }
        default:
            $this->setVar('content', $this->renderTopics());
            $this->setLayoutTemplate('essayadmin_layout_tpl.php');
            return 'essay_tpl.php';
        }
        return $template;
    }

    /**
    * Renders the topics.
    * @return string Rendered content.
    */
    function renderTopics()
    {
        // Get topic data
        $rs = $this->dbtopic->getTopic(NULL, NULL, "context='{$this->contextcode}'");
        $topics = array();
        if(!empty($rs)){
            foreach($rs as $key=>$item){
                $bookings = $this->dbbook->getBooking("WHERE topicid='".$item['id']."'", "COUNT(studentfileid) AS submitted, COUNT(mark) AS marked");
                $topics[$key]['id'] = $item['id'];
                $topics[$key]['name'] = $item['name'];
                $topics[$key]['closing_date'] = $item['closing_date'];
                $topics[$key]['bypass'] = $item['bypass'];
                $topics[$key]['percentage'] = $item['percentage'];
                $topics[$key]['marked'] = $bookings[0]['marked'];
                $topics[$key]['submitted'] = $bookings[0]['submitted'];
            }
        }
        $heading = $this->objLanguage->languageText('mod_essayadmin_name','essayadmin');
        $this->setVarByRef('heading', $heading);

        $strAddNewTopic = $this->objLanguage->languageText('mod_essayadmin_addnewtopicarea','essayadmin');
        // Icon for add new topic
        $objIcon = $this->objIcon;
        $objIcon->title = $strAddNewTopic;
        $iconAddTopic = $objIcon->getAddIcon($this->uri(array('action'=>'addtopic')));
        $heading .= '&nbsp;'.$iconAddTopic;

        $objLink = new link($this->uri(array('action'=>'addtopic')));
        $objLink->link = $strAddNewTopic;
        $linkAddNewTopic = $objLink->show();

        $objTable = $this->newObject('htmltable', 'htmlelements');
        $objTable->cellpadding = 2;
        $objTable->cellspacing = 2;

        $tableHeader = array();
        $tableHeader[] = $this->objLanguage->languageText('mod_essayadmin_topicarea','essayadmin');
        $tableHeader[] = $this->objLanguage->languageText('mod_essayadmin_percentyearmark', 'essayadmin');
        $tableHeader[] = $this->objLanguage->languageText('mod_essayadmin_closedate','essayadmin');
        $tableHeader[] = $this->objLanguage->languageText('mod_essayadmin_submitted','essayadmin').' / '.$this->objLanguage->languageText('mod_essayadmin_marked','essayadmin');
        $tableHeader[] = $this->objLanguage->languageText('mod_essayadmin_editdelete','essayadmin');
        $objTable->addHeader($tableHeader, 'heading');
        $i=0;
        if (!empty($topics)) {
            foreach ($topics as $topic) {
                $class = ($i++%2) ? 'even':'odd';
                $strViewEssays = $this->objLanguage->languageText('mod_essayadmin_viewessays','essayadmin');

                $objLink = new link($this->uri(array('action'=>'view', 'id'=>$topic['id'])));
                $objLink->link = $topic['name'];
                $objLink->title = $strViewEssays;
                $linkView = $objLink->show();

                $objIcon->title = $this->objLanguage->languageText('word_edit');
                $iconEdit = $objIcon->getEditIcon($this->uri(array('action'=>'edittopic', 'id'=>$topic['id'])));

                $objIcon->title = $this->objLanguage->languageText('word_delete');
                $objIcon->setIcon('delete');
                $this->objConfirm->setConfirm($objIcon->show(), $this->uri(array('action'=>'deletetopic', 'id'=>$topic['id'])), $this->objLanguage->code2Txt('mod_essayadmin_deletetopic','essayadmin', array('TOPIC'=>$topic['name'])));
                $iconDelete = $this->objConfirm->show();

                $objIcon->setIcon('paper');
                $objIcon->title = $strViewEssays;
                $objIcon->alt = $strViewEssays;
                $objLink = new link($this->uri(array('action'=>'view', 'id'=>$topic['id'])));
                $objLink->link = $objIcon->show();
                $iconView = $objLink->show();

                if ($topic['submitted'] == 0) {
                    $iconMark = '';
                } else {
                    $strMarkEssays = $this->objLanguage->languageText('mod_essayadmin_markessays','essayadmin');
                    $objIcon->setIcon('comment');
                    $objIcon->title = $strMarkEssays;
                    $objIcon->alt = $strMarkEssays;
                    $objLink = new link($this->uri(array('action'=>'marktopic', 'id'=>$topic['id'])));
                    $objLink->link = $objIcon->show();
                    $iconMark = $objLink->show();
                }

                $icons =
                    $iconEdit
                    .$iconDelete
                    .$iconView
                    .$iconMark;

                $percentage = $topic['percentage'];

//                $date = $this->objDateformat->formatDate($topic['closing_date']);
                if ($topic['bypass'] == '1') {
                    $date = '';
                } else {
                    $date = $this->objDateformat->formatDate($topic['closing_date']);
                }

                $markedSubmitted = $topic['submitted'].' / '.$topic['marked'];

                $objTable->startRow();
                $objTable->addCell($linkView, '', '', '', $class);
                $objTable->addCell($percentage, '', '', '', $class);
                $objTable->addCell($date, '', '', '', $class);
                $objTable->addCell($markedSubmitted, '', '', '', $class);
                $objTable->addCell($icons, '', '', '', $class);
                $objTable->endRow();
            }
        } else {
            $objTable->startRow();
            $objTable->addCell($this->objLanguage->code2Txt('mod_essayadmin_notopicareasavailable', 'essayadmin'),'','','','noRecordsMessage','colspan="5"');
            $objTable->endRow();
        }

        $links = '';
        $links .= $linkAddNewTopic;

        if ($this->assignment) {
            $objLink = new link($this->uri(array(), 'assignment'));
            $objLink->link = $this->objLanguage->languageText('mod_assignment_name','assignment');
            $links .= '<br />'.$objLink->show();
        }
        return
            $objTable->show()
            .$links;
    }

    /**
    * Renders the essays.
    * @param string $topicAreaId The topic area ID
    * @return string Rendered content
    */
    function renderEssays($topicAreaId) //$essays,$topic
    {
        // get topic name
        $topic=$this->dbtopic->getTopic($topicAreaId);
        // get essays in topic
        $essays=$this->dbessays->getEssays($topicAreaId);
        $head = $this->objLanguage->languageText('mod_essayadmin_topicarea','essayadmin').': '.$topic[0]['name'];
        $subhead=$this->objLanguage->languageText('mod_essayadmin_essays','essayadmin');
        $descriptionLabel=$this->objLanguage->languageText('mod_essayadmin_description','essayadmin');
        $instructionsLabel=$this->objLanguage->code2Txt('mod_essayadmin_instructions','essayadmin');
        $duedate=$this->objLanguage->languageText('mod_essayadmin_closedate','essayadmin');
        $view=$this->objLanguage->languageText('word_view');
        $title1=$this->objLanguage->languageText('word_edit');
        $title2=$this->objLanguage->languageText('word_delete');
        $title3=$this->objLanguage->languageText('mod_essayadmin_newessay','essayadmin');
        $topiclist=$this->objLanguage->languageText('mod_essayadmin_name','essayadmin').' '.$this->objLanguage->languageText('word_home');
        $viewSubmitted=$this->objLanguage->languageText('mod_essayadmin_viewbookedsubmitted','essayadmin');
        $assignLabel=$this->objLanguage->languageText('mod_assignment_name','essayadmin');
        $percentLbl=$this->objLanguage->languageText('mod_essayadmin_percentyrmark','essayadmin');
        $noEssays = $this->objLanguage->code2Txt('mod_essayadmin_noessaysintopicarea','essayadmin');

        // edit topic icon
        $this->objIcon->title=$title1;
        $topicEdit=$this->objIcon->getEditIcon($this->uri(array('action'=>'edittopic','id'=>$topic[0]['id'])));

        // delete topic icon
        $this->objIcon->title=$title2;
        $this->objIcon->setIcon('delete');
        $this->objConfirm->setConfirm(
            $this->objIcon->show(),
            $this->uri(
                array(
                    'action'=>'deletetopic',
                    'id'=>$topic[0]['id']
                )
            ),
            $this->objLanguage->code2Txt('mod_essayadmin_deletetopic', 'essayadmin', array('TOPIC'=>$topic[0]['name']))
        );
        $topicDelete=$this->objConfirm->show();

        $topicIcons = $topicEdit.$topicDelete;
        $head.='&nbsp;'.$topicIcons;
        //$formAction='marktopic';
        $this->setVarByRef('heading',$head);

        $str = '';
        // set confirm message if exists
        $confirm = $this->getParam('confirm');
        if($confirm == 'yes'){
            $msg = $this->getSession('confirm');
            $this->unsetSession('confirm');
            $objMsg = $this->newObject('timeoutmessage', 'htmlelements');
            $objMsg->setMessage($msg.'&nbsp;'.date('d/m/Y H:i'));
            $objMsg->setTimeOut(15000);
            $str .= '<p>'.$objMsg->show().'</p>';
        }

        // Display Topic data
        $objTable2 = new htmltable();
        $objTable2->cellpadding=2;
        $objTable2->cellspacing=2;

        $objTable2->startRow();
        $objTable2->addCell('<b>'.$descriptionLabel.'</b>','','','','even');
        $objTable2->addCell($topic[0]['description'],'','','','even');
        $objTable2->endRow();

        $objTable2->startRow();
        $objTable2->addCell('<b>'.$instructionsLabel.'</b>','','','','odd');
        $objTable2->addCell($topic[0]['instructions'],'','','','odd');
        $objTable2->endRow();

        $objTable2->startRow();
        $objTable2->addCell('<b>'.$percentLbl.'</b>','','','','even');
        $objTable2->addCell($topic[0]['percentage'].'%','','','','even');
        $objTable2->endRow();

        $date = $this->objDateformat->formatDate($topic[0]['closing_date']);
        $objTable2->startRow();
        $objTable2->addCell('<b>'.$duedate.'</b>','','','','odd');
        //$objTable2->addCell($topic[0]['closing_date'].' %','80%','','','even');
        $objTable2->addCell($date,'','','','odd');
        $objTable2->endRow();

        $objLayer = new layer;
        //$objLayer->border='';
        $objLayer->str = $objTable2->show();
        $str .=
            $objLayer->show();

        // Heading

        // add new essay icon
        $this->objIcon->title=$title3;
        $addicon=$this->objIcon->getAddIcon($this->uri(array('action'=>'addessay','id'=>$topic[0]['id'])));
        $subhead.= '&nbsp;'.$addicon;

        $objHead = new htmlHeading;
        $objHead->type=3;
        $objHead->str=$subhead;
        $str .=
            $objHead->show();

        // Display essay list in table
        $objTable = new htmltable();
        //$objTable->width='99%';
        $objTable->cellpadding=2;
        $objTable->cellspacing=2;

        $tableHeader = array();
        $tableHeader[] = '#';
        $tableHeader[] = $this->objLanguage->languageText('mod_essayadmin_essay','essayadmin');
        $tableHeader[] = $this->objLanguage->languageText('mod_essayadmin_notes','essayadmin');
        $tableHeader[] = '&nbsp;';
        $objTable->addHeader($tableHeader, 'heading');

        if(!empty($essays)){
            $i=0;
            foreach($essays as $essay){
                $class = ($i++%2)? 'even':'odd';

                // edit essay
                $objLink = new link($this->uri(array('action'=>'editessay','essay'=>$essay['id'],'id'=>$topic[0]['id'])));
                $objLink->link = $essay['topic'];
                $objLink->title = $title1;
                $view = $objLink->show();

                $this->objIcon->title=$title1;
                $this->objIcon->extra='';
                $edit=$this->objIcon->getEditIcon($this->uri(array('action'=>'editessay','essay'=>$essay['id'],'id'=>$topic[0]['id'])));
                // delete essay display confirmation
                $this->objIcon->title=$title2;
                $this->objIcon->setIcon('delete');
                $this->objConfirm->setConfirm(
                    $this->objIcon->show(),
                    $this->uri(
                        array(
                            'action'=>'deleteessay',
                            'essay'=>$essay['id'],
                            'id'=>$topic[0]['id']
                        )
                    ),
                    $this->objLanguage->code2Txt('mod_essayadmin_deleteessay', 'essayadmin', array('ESSAY'=>$essay['topic']))
                );
                $delete=$this->objConfirm->show();
                $icons=$edit.$delete;

                if(strlen($essay['notes']) > 100){
                    $pos = strpos($essay['notes'], ' ', 100);
                    $notes = substr($essay['notes'], 0, $pos).'...';
                }else{
                    $notes = $essay['notes'];
                }

                //$objTable->row_attributes=' height="25"';
                $objTable->startRow();
                $objTable->addCell($i, '','','',$class);
                $objTable->addCell($view,'','','',$class);
                $objTable->addCell($notes,'','','',$class);
                $objTable->addCell($icons,'','','',$class);
                $objTable->endRow();
            }
        }else{
            //$objTable->row_attributes=' height="15"';
            $objTable->startRow();
            $objTable->addCell($noEssays,'','','','noRecordsMessage','colspan="4"');
            $objTable->endRow();
        }

        $str .=
            $objTable->show();

        $links = '';

        $objLink = new link($this->uri(array('action'=>'addessay','id'=>$topic[0]['id'])));
        $objLink->link = $title3;
        $objLink->title = $title3;
        $links .= $objLink->show();

        $objLink = new link($this->uri(array('action'=>'marktopic', 'id'=>$topic[0]['id'])));
        $objLink->link = $viewSubmitted;
        $objLink->title = $viewSubmitted;
        $links .= '<br />'.$objLink->show();

        $strHome = $this->objLanguage->languageText('mod_essayadmin_home','essayadmin');
        $objLink = new link($this->uri(array()));
        $objLink->link = $strHome;
        $objLink->title = $strHome;
        $links .= '<br />'.$objLink->show();
        $str .= $links;
        return $str;
    }
}
?>