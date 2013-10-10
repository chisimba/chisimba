<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot  this page directly');
}
// end security check

define('ESSAY_CANBOOK', 1);
define('ESSAY_BOOKEDBYSTUDENT', 2);
define('ESSAY_BOOKED', 3);
define('ESSAY_CANNOTBOOK', 4);

/**
 * Controller class for the essay module.
 * Students are provided with functionality for booking an essay and uploading it for marking.
 * They are also able to download the marked essay.
 * @category Chisimba
 * @package essay
 * @author Megan Watson
 * @author Jeremy O'Connor
 * @copyright (c) 2004, 2010 Avoir
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version $Id: controller.php 23920 2012-04-05 19:01:06Z pwando $
 */
class essay extends controller {

    /**
     * Initialization method.
     */
    public function init() {        
        // Load scriptaclous since we can no longer guarantee it is there
        $scriptaculous = $this->getObject('scriptaculous', 'prototype');
        $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
        //$this->objModules = $this->newObject('modules','modulecatalogue');
        $this->objModules = $this->newObject('modules', 'modulecatalogue');
        if (!$this->objModules->checkIfRegistered('essayadmin')) {
            return $this->nextAction(NULL, array('error' => 'notincontext'), '_default');
        }
        $this->assignment = FALSE;
        if ($this->objModules->checkIfRegistered('Assignment Management', 'assignment')) {
            $this->assignment = TRUE;
        }
        // Get instances of the module classes
        $this->dbessays = $this->getObject('dbessays');
        $this->dbtopic = $this->getObject('dbessay_topics');
        $this->dbbook = $this->getObject('dbessay_book');
        $this->objEssayView = $this->newObject('manageviews_essay', 'essay');
        // Get instances of the html elements:
        // form, table, link, textinput, button, icon, layer, checkbox, textarea, iframe
        $this->loadclass('htmltable', 'htmlelements');
        $this->loadclass('layer', 'htmlelements');
        $this->loadclass('link', 'htmlelements');
        $this->loadclass('textinput', 'htmlelements');
        $this->loadclass('button', 'htmlelements');
        $this->loadclass('checkbox', 'htmlelements');
        $this->loadclass('textarea', 'htmlelements');
        $this->loadclass('iframe', 'htmlelements');
        $this->objLayer = new layer();
        $this->objLink = new link();
        $this->objInput = new textinput();
        $this->objButton = new button();
        $this->objCheck = new checkbox('dummy');
        $this->objText = new textarea();
        $this->objIframe = new iframe();
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objForm = $this->newObject('form', 'htmlelements');
        // Get an instance of the confirmation object
        $this->objConfirm = $this->newObject('confirm', 'utilities');
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the user object
        $this->objUser = $this->getObject('user', 'security');
        // Get an instance of the context object
        $this->objContext = $this->getObject('dbcontext', 'context');
        //Get an instance of registerfileusage in filemanager
        $this->objFileRegister = $this->getObject('registerfileusage', 'filemanager');
        // Get an instance of the filestore object and change the tables to essay specific tables
        //$this->objFile= $this->getObject('dbfile','filemanager');
        //$this->objFile->changeTables('tbl_essay_filestore','tbl_essay_blob');
        $this->objHelp = $this->newObject('helplink', 'help');
        //$this->objDate = $this->newObject('simplecal','datetime');
        $this->objDate = $this->newObject('datepicker', 'htmlelements');
        $this->objDateformat = $this->newObject('dateandtime', 'utilities');
        $this->objFile = $this->newObject('upload', 'filemanager');
        // Log this call if registered
        if (!$this->objModules->checkIfRegistered('logger', 'logger')) {
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
        }
    }

    /**
     * The standard dispatch method for the module.
     * @return string The template
     */
    public function dispatch($action) {        
        $this->setVar('pageSuppressXML', true);
        $this->userId = $this->objUser->userId();
        $this->user = $this->objUser->fullname();
        if ($this->objContext->isInContext()) {
            $this->contextcode = $this->objContext->getContextCode();
            $this->context = $this->objContext->getTitle();
            $incontext = TRUE;
        } else {
            $this->contextcode = 'root';
            $this->context = $this->objLanguage->languageText('word_inlobby', 'Lobby');
            $incontext = FALSE;
        }
        $this->setVarByRef('contextcode', $this->contextcode);
        $this->setVarByRef('context', $this->context);
        //$topicid=$this->getParam('id');
        switch ($action) {
            case 'view':
                // View essays within a topic area
                // Get topic area id
                $topicAreaId = $this->getParam('id');
                // Get table
                //display[0]['essayid']
                //ob_start();
                $this->setVar('content', $this->renderEssays($topicAreaId)); //$topicArea, $essays, $studentBooking, $allBookings, $topicArea[0]['forceone']=='1'
                //$this->setVar('buffer', ob_get_contents());
                //ob_end_clean();
                $this->setLayoutTemplate('essay_layout_tpl.php');
                return 'essay_tpl.php';
            //break;
            case 'shownotes':
                // Display notes in a pop up window
                $id = $this->getParam('essay');
                $data = $this->dbessays->getEssay($id, 'topic, notes');
                $this->setVar('heading', $this->objLanguage->languageText('mod_essay_notes', 'essay'));
                $this->setVar('topic', $data[0]['topic']);
                $this->setVar('notes', $data[0]['notes']);
                $this->setVar('leftNav', '');
                return 'notes_tpl.php';
            //break;
            case 'showcomment':
                // Display comment in a pop up window
                // Get essay id
                $essay = $this->getParam('essay');
                // Get booking id
                $id = $this->getParam('book');
                // Get comment form booking details
                $comment = $this->dbbook->getBooking("WHERE id='{$id}'", 'comment');
                $this->setVar('heading', $this->objLanguage->languageText('mod_essay_comment', 'essay'));
                $this->setVar('topic', $essay);
                $this->setVar('comment', $comment[0]['comment']);
                $this->setVar('leftNav', '');
                //$this->setPageTemplate('essay_page_tpl.php');
                return 'comment_tpl.php';
            //break;
            case 'bookessay':
                $topicAreaId = $this->getParam('id');
                $this->dbbook->bookEssay(
                        array(
                            'context' => $this->contextcode,
                            'topicid' => $topicAreaId,
                            'essayid' => $this->getParam('essay'),
                            'studentid' => $this->userId
                ));
                $this->setVar('content', $this->renderEssayTable($topicAreaId));
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                return 'essaytable_tpl.php';
            //return $this->nextAction('view', array('id'=>$this->getParam('id')));
            //break;
            case 'unbookessay':
                $topicAreaId = $this->getParam('id');
                $essayId = $this->getParam('essay');
                $studentId = $this->userId;
                $this->dbbook->deleteBooking(
                        NULL,
                        "WHERE
        		topicid='{$topicAreaId}'
        		AND essayid='{$essayId}'
        		AND studentid='{$studentId}'");
                $this->setVar('content', $this->renderEssayTable($topicAreaId));
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                return 'essaytable_tpl.php';
            //return $this->nextAction('view',array('id'=>$this->getParam('id')));
            //break;
            case 'viewallessays':
                // Display student's essays' details
                //ob_start();
                $data = $this->objEssayView->getStudentEssays();
                //$this->setVar('buffer', ob_get_contents());
                //ob_end_clean();
                //return "dump_tpl.php";
                $this->setVarByRef('data', $data);
                $this->setLayoutTemplate('essay_layout_tpl.php');
                return 'view_essays_tpl.php';
            //break;
            /*
              case 'viewessays':
              // display students essays details
              $data=$this->getStudentEssays();
              $this->setVarByRef('data',$data);
              $template='view_essays_tpl.php';
              break;
             */
            case 'uploadessay':
                // Display page to upload essay
                //$message = $this->getParam('msg');
                //$this->setVar('message', $message);
                $bookId = $this->getParam('bookid');
                $this->setVar('bookId', $bookId);
                $this->setLayoutTemplate('essay_layout_tpl.php');
                return 'upload_tpl.php';
            //break;
            case 'uploadsubmit':
                // Upload an essay for marking
                //--JO'C deprecated or a marked essay & marks & comment
                // Get booking ID
                $bookId = $this->getParam('bookid');
                // Get file ID
                $fileId = $this->getParam('file');
                $fields = array('studentfileid' => $fileId, 'submitdate' => date('Y-m-d H:i:s'));
                $this->dbbook->bookEssay($fields, $bookId);
                $this->objFileRegister->registerUse($fileId, 'essay', 'tbl_essay_book', $bookId, 'studentfileid', $this->contextcode, '', TRUE);
                return $this->nextAction('viewallessays');
            //$submit = $this->getParam('submit');
            //if ($submit == $this->objLanguage->languageText('word_exit')) {
            // Exit upload form
            //} else if ($submit == $this->objLanguage->languageText('mod_essay_upload', 'essay')) {
            // Upload essay and return to form
            // change the file name to fullname_studentId
            //$name = $this->user;
            //$studentid = $this->userId;
            // upload file to database into the filemanager database
            //$arrayfiledetails = $this->objFile->uploadFile('file');
            // Save file id and submit date to database
            // Display success message
            //$message = $this->objLanguage->languageText('mod_essay_uploadsuccess','essay');
            //return $this->nextAction('uploadessay', array('bookid'=>$bookId, 'msg'=>$message));
            //}
            //break;
            case 'download':
                $this->setPageTemplate(NULL);
                $this->setLayoutTemplate(NULL);
                return 'download_tpl.php';
                break;

            default:
                //ob_start();
                //$content = "OK";
                $this->setVar('content', $this->renderTopics());
                //$this->setVar('buffer', ob_get_contents());
                //ob_end_clean();
                $this->setLayoutTemplate('essay_layout_tpl.php');
                return 'topic_tpl.php';
        }
        //return $template;
    }

    /**
     * Method to render the topic areas page.
     * @return string The topic areas page
     */
    public function renderTopics() {
        // Set up htmlelements
        //$objLink = $this->objLink;
        //$objIcon = $this->objIcon;
        //$objLayer = $this->objLayer;
        // Set up language items
        //$subhead=$this->objLanguage->languageText('mod_essay_selecttopic','Select Topic');
        //$topicslabel=
        //$duedate=$this->objLanguage->languageText('mod_essay_closedate','essay');
        //$viewLabel=$this->objLanguage->languageText('word_view');
        //$essaysLabel=$this->objLanguage->languageText('mod_essay_essays','essay');
        //$title=$viewLabel.' '.$essaysLabel;
        //$heading=;
        //$submittedLabel=$this->objLanguage->languageText('mod_essay_submitted','Submitted');
        //$viewSubmitted=$this->objLanguage->languageText('mod_essay_viewbookedsubmitted','essay');
        //$assignmentLabel = ;
        //$percentLabel = $this->objLanguage->languageText('mod_essayadmin_percentyrmark','essayadmin');
        //$noTopics = ;

        $this->setVar('heading', $this->objLanguage->languageText('mod_essay_name', 'essay'));

        // Table
        $objTable = new htmltable();
        //$objTable->row_attributes=' height="25"';
        $objTable->cellpadding = 2;
        $objTable->cellspacing = 2;

        $tableHeader = array();
        $tableHeader[] = $this->objLanguage->languageText('mod_essay_topicarea', 'essay');
        $tableHeader[] = $this->objLanguage->languageText('mod_essay_percentyearmark', 'essay');
        $tableHeader[] = $this->objLanguage->languageText('mod_essay_closedate', 'essay');
        ;

        $objTable->addHeader($tableHeader, 'heading');
        // Get topic data
        $data = $this->dbtopic->getTopic(NULL, NULL, "context='{$this->contextcode}'");
        $topicData = array();
        // count number of marked essays & number of submitted essays in each topic
        if (!empty($data)) {
            foreach ($data as $key => $item) {
                $filter = "WHERE topicid='" . $item['id'] . "'";
                $fields = "COUNT(studentfileid) as submitted, COUNT(mark) as marked";
                $bookings = $this->dbbook->getBooking($filter, $fields);
                $topicData[$key]['id'] = $item['id'];
                $topicData[$key]['name'] = $item['name'];
                $topicData[$key]['date'] = $item['closing_date'];
                $topicData[$key]['bypass'] = $item['bypass'];
                $topicData[$key]['percentage'] = $item['percentage'];
                $topicData[$key]['marked'] = $bookings[0]['marked'];
                $topicData[$key]['submitted'] = $bookings[0]['submitted'];
            }
        }

        $i = 0;
        if (!empty($topicData)) {
            foreach ($topicData as $topic) {
                $class = ($i++ % 2) ? 'even' : 'odd';
                $percentage = $topic['percentage'];
                if ($topic['bypass'] == 1) {
                    $date = $this->objLanguage->languageText('mod_essay_n_a', 'essay');
                } else {
                    $date = $this->objDateformat->formatDate($topic['date']);
                }
                $objLink = new link($this->uri(array('action' => 'view', 'id' => $topic['id'])));
                $objLink->link = $topic['name'];
                $objLink->title = $this->objLanguage->languageText('mod_essay_viewessays', 'essay');
                $view = $objLink->show();
                $objTable->startRow();
                $objTable->addCell($view, '', '', '', $class);
                $objTable->addCell($percentage, '', '', '', $class);
                $objTable->addCell($date, '', '', '', $class);
                $objTable->endRow();
            }
        } else {
            $objTable->startRow();
            $objTable->addCell($this->objLanguage->code2Txt('mod_essay_notopics', 'essay'), '', '', '', 'noRecordsMessage', 'colspan="3"');
            $objTable->endRow();
        }
        $links = '';
        $objLink = new link($this->uri(array(
                            'action' => 'viewallessays'
                        )));
        $objLink->link = $this->objLanguage->languageText('mod_essay_viewbookedsubmitted', 'essay');
        $links .= $objLink->show();
        if ($this->assignment) {
            $objLink = new link($this->uri(array(''), 'assignment'));
            $objLink->link = $this->objLanguage->languageText('mod_assignment_name', 'assignment');
            $links .= '<br />' . $objLink->show();
        }
        $objLayer = $this->objLayer;
        $objLayer->border = 0;
        $objLayer->str =
                $objTable->show()
                . $links;
        return $objLayer->show();
    }

    /**
     * Method to render the essays associated with the given topic area.
     * @param array $topicArea The topic area data
     * @param array $essays The essay data
     * @param array $studentBooking Student's booking data
     * @param array $allBookings All bookings data
     * @param bool $forceOne Flag to indicate whether only one student can book an essay, if TRUE prevent another student booking it
     * @return string The essays page
     */
    public function renderEssays($topicAreaId) { //$topicArea, $essays, $studentBooking = TRUE, $allBookings = TRUE, $forceOne = FALSE
        // Get topic area name
        $topicArea = $this->dbtopic->getTopic($topicAreaId);
        /*
          // Get essays in topic area
          $essays = $this->dbessays->getEssays($topicAreaId);
          // Fetch current user's booking
          $studentBooking = $this->dbbook->getBooking("WHERE topicid='{$topicAreaId}' AND studentid='{$this->userId}'");
          // Check if 'force one student per essay' is set
          if ($topicArea[0]['forceone']=='0') {
          $allBookings = NULL;
          } else {
          // Fetch all bookings in topic area
          $allBookings = $this->dbbook->getBooking("WHERE topicid='{$topicAreaId}'");
          }
          $forceOne = $topicArea[0]['forceone']=='1';
          //$topicArea, $essays, $studentBooking, $allBookings, $topicArea[0]['forceone']=='1'
         */
        // Set up html elements
        //$objLink=$this->objLink;
        // set up language elements
        //$head=$this->objLanguage->languageText('mod_essay_essay', 'essay').' ';
        //$head.=;
        //$subhead=;
        //$descriptionLabel=;
        //$instructionsLabel=; //array('readonlys'=>'students')
        //$duedate=;
        //$view=$this->objLanguage->languageText('word_view');
        //$title=$view.' '.;
        //$title4=$this->objLanguage->languageText('mod_essay_book', 'essay').' '.$this->objLanguage->languageText('mod_essay_essay','essay');
        //$title5=;
        //$topiclist=$this->objLanguage->languageText('mod_essay_name', 'essay').' '.$this->objLanguage->languageText('word_home');
        //$viewSubmitted=;
        //$assignLabel=$this->objLanguage->languageText('mod_assignment_name', 'essay');
        //$percentLabel = ;
        //$explainBook = ;
        //$marklabel = ;
        //$noEssays = ;

        $str = '';

        $this->setVar('heading', $this->objLanguage->languageText('mod_essay_topicarea', 'essay') . ':&nbsp;' . $topicArea[0]['name']);

        $objTableTopicAreaInfo = new htmltable();
        //$objTableTopicAreaInfo->cellspacing=2;
        //$objTableTopicAreaInfo->cellpadding=2;

        $objTableTopicAreaInfo->startRow();
        $objTableTopicAreaInfo->addCell('<b>' . $this->objLanguage->languageText('mod_essay_description', 'essay') . ':</b>', '', '', '', '');
        $objTableTopicAreaInfo->addCell($topicArea[0]['description'], '', '', '', '');
        $objTableTopicAreaInfo->endRow();

        $objTableTopicAreaInfo->startRow();
        $objTableTopicAreaInfo->addCell('<b>' . $this->objLanguage->code2Txt('mod_essay_instructions', 'essay') . ':</b>', '', '', '', '');
        $objTableTopicAreaInfo->addCell($topicArea[0]['instructions'], '', '', '', '');
        $objTableTopicAreaInfo->endRow();

        $objTableTopicAreaInfo->startRow();
        $objTableTopicAreaInfo->addCell('<b>' . $this->objLanguage->languageText('mod_essay_percentyearmark', 'essay') . ':</b>', '', '', '', '');
        $objTableTopicAreaInfo->addCell($topicArea[0]['percentage'] . ' %', '', '', '', '');
        $objTableTopicAreaInfo->endRow();

        $objTableTopicAreaInfo->startRow();
        $objTableTopicAreaInfo->addCell('<b>' . $this->objLanguage->languageText('mod_essay_closedate', 'essay') . ':</b>', '', '', '', '');
        $objTableTopicAreaInfo->addCell($this->objDateformat->formatDate($topicArea[0]['closing_date']), '', '', '', '');
        $objTableTopicAreaInfo->endRow();

        //$objLayer=$this->objLayer;
        //$objLayer->border='';
        //$objLayer->str=$objTableTopicAreaInfo->show();
        //$str.=$objLayer->show();
        $str .= $objTableTopicAreaInfo->show();

        $str .= '<br />';

        $objHeading = $this->newObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText('mod_essay_essays', 'essay');
        $str .= $objHeading->show();

        $str .= '<p>' . $this->objLanguage->languageText('mod_essay_explainbook', 'essay') . '</p>';

        $javaScript = "<script language=\"JavaScript\" type=\"text/javascript\">
function book(uri)
{
    document.getElementById('wait').style.visibility = 'visible';
    new Ajax.Updater(
        'essaytable',
        uri,
        {onSuccess: _onSuccess}
    );
    return false;
}
function unbook(uri)
{
    document.getElementById('wait').style.visibility = 'visible';
    new Ajax.Updater(
        'essaytable',
        uri,
        {onSuccess: _onSuccess}
    );
    return false;
}
function _onSuccess()
{
    document.getElementById('wait').style.visibility = 'hidden';
}
</script>";
        $str .= $javaScript;

        $objIcon = $this->objIcon;
        $objIcon->setIcon('spinner');

        $objLayer = $this->newObject('layer', 'htmlelements');
        $objLayer->id = 'wait';
        $objLayer->visibility = 'hidden';
        $objLayer->addToStr($objIcon->show() . '&nbsp;' . $this->objLanguage->languageText('mod_essay_pleasewait', 'essay'));
        $str .= $objLayer->show();

        $objLayer = $this->newObject('layer', 'htmlelements');
        $objLayer->id = 'essaytable';
        $objLayer->addToStr($this->renderEssayTable($topicAreaId));
        $str .= $objLayer->show();

        $links = '';

        $objLink = new link($this->uri(array('action' => 'viewallessays', 'id' => $topic[0]['id'])));
        $text = $this->objLanguage->languageText('mod_essay_viewbookedsubmitted', 'essay');
        $objLink->link = $text;
        $objLink->title = $text;
        $links .= '<br />' . $objLink->show();

        $objLink = new link($this->uri(array()));
        $text = $this->objLanguage->languageText('mod_essay_essayhome', 'essay');
        $objLink->link = $text;
        $objLink->title = $text;
        $links .= '<br />' . $objLink->show();

        $str .= $links;

        $objLayer = $this->objLayer;
        $objLayer->border = 0;
        $objLayer->str = $str;

        return $objLayer->show();
    }

    /**
     * Method to render the essay table associated with the given topic area.
     * @param array $topicArea The topic area data
     * @param array $essays The essay data
     * @param array $studentBooking Student's booking data
     * @param array $allBookings All bookings data
     * @param bool $forceOne Flag to indicate whether only one student can book an essay, if TRUE prevent another student booking it
     * @return string The essay table
     */
    public function renderEssayTable($topicAreaId) {
        // Get topic area name
        $topicArea = $this->dbtopic->getTopic($topicAreaId);
        // Get essays in topic area
        $essays = $this->dbessays->getEssays($topicAreaId);
        // Fetch current user's booking
        $studentBooking = $this->dbbook->getBooking("WHERE topicid='{$topicAreaId}' AND studentid='{$this->userId}'");
        // Check if 'force one student per essay' is set
        $forceOne = $topicArea[0]['forceone'] == '1';
        if (!$forceOne) {
            $allBookings = NULL;
        } else {
            // Fetch all bookings in topic area
            $allBookings = $this->dbbook->getBooking("WHERE topicid='{$topicAreaId}'");
        }
        // Check if essay has been booked, submitted and marked
        if (empty($studentBooking[0]['essayid'])) {
            $studentessay = FALSE;
        } else {
            $studentessay = $studentBooking[0]['essayid'];
        }
        if (empty($studentBooking[0]['submitdate'])) {
            $essaysubmit = NULL;
        } else {
            $essaysubmit = $studentBooking[0]['submitdate'];
        }
        if (empty($studentBooking[0]['mark'])) {
            $essaymark = NULL;
        } else {
            $essaymark = $studentBooking[0]['mark'];
        }

        $str = '';

        /*
          $objLink = new link();
          $objLink->cssId = 'essaytableanchor';
          $str .= $objLink->show();
         */
        $str .= "<a id=\"essaytableanchor\"></a>";

        $objTable = new htmltable();
        $objTable->cellpadding = 2;
        $objTable->cellspacing = 2;

        $tableHeader = array();
        $tableHeader[] = '#';
        $tableHeader[] = $this->objLanguage->languageText('mod_essay_essay', 'essay');
        $tableHeader[] = $this->objLanguage->languageText('mod_essay_notes', 'essay');
        $tableHeader[] = $this->objLanguage->languageText('mod_essay_mark', 'essay');
        $tableHeader[] = '&nbsp;';
        $objTable->addHeader($tableHeader, 'heading');

        if (!empty($essays)) {
            $i = 0;
            foreach ($essays as $essay) {
                $class = ($i++ % 2) ? 'even' : 'odd';
                $id = $essay['id'];
                // Check if student booked essay
                if ($studentessay) {
                    if ($studentessay == $id) {
                        $booked = ESSAY_BOOKEDBYSTUDENT;
                    } else {
                        $booked = ESSAY_CANNOTBOOK;
                    }
                } else {
                    // If force one student per essay, check if essay is booked
                    if ($forceOne/* $allBookings */) {
                        $isBooked = FALSE;
                        if (!empty($allBookings)) {
                            foreach ($allBookings as $booking) {
                                if ($booking['essayid'] == $id) {
                                    $isBooked = TRUE;
                                    break;
                                }
                            }
                        }
                        if ($isBooked) {
                            $booked = ESSAY_BOOKED;
                        } else {
                            $booked = ESSAY_CANBOOK;
                        }
                    } else {
                        $booked = ESSAY_CANBOOK;
                    }
                }

                $objIcon = $this->objIcon;
                $objLink = $this->objLink;
                //$message = '';
                $message = '<br />';
                $mark = '';
                $icons = '';
                if ($booked == ESSAY_CANBOOK) {

                    $title = $this->objLanguage->languageText('mod_essay_bookessay', 'essay');

                    $objIcon->setIcon('bullet');
                    $objIcon->title = $title;
                    $objIcon->extra = '';

                    $objLink = new link($this->uri(array('action' => 'bookessay', 'essay' => $id, 'id' => $topicArea[0]['id'])));
                    $objLink->extra = "onclick=\"javascript: return book(this.href);\"";
                    $objLink->link = $essay['topic'];
                    $objLink->title = $title;
                    $multiLink = $objLink->show();

                    $objLink->link = $objIcon->show();
                    $objLink->title = $title;
                    $bookIcon = $objLink->show();

                    $icons .= $bookIcon;
                }
                if ($booked == ESSAY_BOOKEDBYSTUDENT) {
                    if (is_null($essaysubmit)) {
                        $title = $this->objLanguage->languageText('mod_essay_unbookessay', 'essay');

                        $objIcon->setIcon('bullet');
                        $objIcon->title = $title;
                        $objIcon->extra = '';

                        $objLink = new link($this->uri(array('action' => 'unbookessay', 'essay' => $id, 'id' => $topicArea[0]['id'])));
                        $objLink->extra = "onclick=\"javascript: return unbook(this.href);\"";
                        $objLink->link = $essay['topic'];
                        $objLink->title = $title;
                        $multiLink = $objLink->show();

                        $objLink->link = $objIcon->show();
                        $unbookIcon = $objLink->show();
                        $message .= $this->objLanguage->languageText('mod_essay_bookedby', 'essay') . ' ' . $this->user;
                        $icons .= $unbookIcon;
                    } else {
                        $multiLink = '<b>' . $essay['topic'] . '</b>';
                        if (is_null($essaymark)) {
                            $message = $this->objLanguage->languageText('mod_essay_statussubmitted', 'essay');
                        } else {
                            //$objMark = $this->getObject('markimage', 'utilities');
                            //$objMark->value = $essaymark;
                            //$objMark->percentage = TRUE;
                            //$objMark->fontsize = 15;
                            //$mark = $objMark->show(); //.'&nbsp;'.$essaymark.'&nbsp;%';
                            $mark = '<span style="color: red;">' . $essaymark . '&nbsp;%</span>';
                            $message = $this->objLanguage->languageText('mod_essay_statusmarked', 'essay');
                        }
                    }
                }
                if ($booked == ESSAY_CANNOTBOOK) {
                    $multiLink = '<b>' . $essay['topic'] . '</b>';
                    $message = $this->objLanguage->languageText('mod_essay_statuscannotbook', 'essay');
                }
                if ($booked == ESSAY_BOOKED) {
                    $multiLink = '<b>' . $essay['topic'] . '</b>';
                    $message = $this->objLanguage->languageText('mod_essay_statusalreadybooked', 'essay');
                }
                $maxLen = 10;
                if (strlen($essay['notes']) <= $maxLen) {
                    $notes = $essay['notes'];
                } else {
                    $notes = substr($essay['notes'], 0, $maxLen);
                    $pos = strrpos($notes, ' ', 0);
                    $notes = ($pos === FALSE ? $notes : substr($notes, 0, $pos)) . '...';
                    // Display notes for essay in a pop-up window
                    //$objIcon = $this->objIcon;
                    $objIcon->setIcon('notes');
                    $objIcon->title = $this->objLanguage->languageText('mod_essay_viewnotes', 'essay');
                    //$objLink = $this->objLink;
                    $objLink = new link('#');
                    $objLink->link = $objIcon->show();
                    $objLink->extra = "onclick=\"javascript: window.open('" . $this->uri(array('action' => 'shownotes', 'essay' => $id)) . "', 'essaynotes', 'width=400, height=200, scrollbars=1');\""; //height=\"18\" width=\"18\"
                    $notes .= $objLink->show();
                }
                $objTable->startRow();
                $objTable->addCell($i, '', '', '', $class);
                $objTable->addCell($multiLink . '&nbsp;' . $message, '', '', '', $class);
                $objTable->addCell($notes, '', '', '', $class);
                $objTable->addCell($mark, '', '', 'center', $class);
                $objTable->addCell($icons, '', '', '', $class);
                $objTable->endRow();
            }
        } else {
            $objTable->startRow();
            $objTable->addCell($this->objLanguage->languageText('mod_essay_noessaysintopicarea', 'essay'), '', '', '', 'noRecordsMessage', 'colspan="5"');
            $objTable->endRow();
        }
        $str .= $objTable->show();
        return $str;
    }

    /**
     * Method to get booked and submitted essays for a student.
     * @return array $data The students essays
     * */
    /* check class manageviews_essay
      public function getStudentEssays($contextcode=Null)
      {
      //import data
      // get student booked essays
      if(empty($contextcode)){
      $data=$this->dbbook->getBooking("where context='".$this->contextcode
      ."' and studentid='".$this->userId."'");
      }else{
      $data=$this->dbbook->getBooking("where context='".$contextcode
      ."' and studentid='".$this->userId."'");
      }
      if($data){
      foreach($data as $key=>$item){
      //var_dump($item);
      // get essay info: topic, num
      $essay=$this->dbessays->getEssay($item['essayid'],'id, topic');
      //var_dump($essay);

      $data[$key]['essay']=$essay[0]['topic'];
      //var_dump($data[$key]);


      // get topic info: closing date
      $topic=$this->dbtopic->getTopic($item['topicid'],'name, closing_date, bypass');

      $data[$key]['name']=$topic[0]['name'];
      $data[$key]['date']=$topic[0]['closing_date'];
      if($topic[0]['bypass']){
      $data[$key]['bypass']='YES';
      }else{
      $data[$key]['bypass']='NO';
      }

      // get booking info: check if submitted or marked
      if(!empty($item['studentfileid'])){
      $data[$key]['mark']=$item['mark'];
      }else{
      $data[$key]['mark']='submit';
      }
      //var_dump($data[$key]);
      }
      }
      //return data
      return $data;
      }
     */
}

?>