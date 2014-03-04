<?php
/* ----------- tutorialsdisplay class extends object ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Display class for tutorials module
* @author Kevin Cyster
*/

class tutorialsdisplay extends object
{
    /**
    * @var object $objIcon: The geticon class of the htmlelements module
    * @access private
    */
    private $objIcon;

    /**
    * @var object $objPopupcal: The datepickajax class in the popupcalendar module
    * @access private
    */
    private $objPopupcal;

    /**
    * @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

    /**
    * @var object $objUser: The user class of the security module
    * @access private
    */
    private $objUser;

    /**
    * @var object $objDatetime: The dateandtime class of the utilities module
    * @access private
    */
    private $objDatetime;

    /**
    * @var object $objContext: The dbcontexr class in the context module
    * @access private
    */
    private $objContext;

    /**
    * @var string $userId: The user id of the current logged in user
    * @access private
    */
    private $userId;

    /**
    * @var boolean $isLecturer: TRUE if the user is a lecturer in the current context
    * @access private
    */
    private $isLecturer;

    /**
    * @var string $contextCode: The context code if the user is in a context
    * @access public
    */
    private $contextCode;

    /**
    * @var string $menuText: The menu text for the context the user is in
    * @access public
    */
    private $menuText;

    /**
    * @var object $objDbTutorials: The dbtutorilas class in the turorials module
    * @access private
    */
    private $objDbTutorials;

    /**
    * @var object $objModules: The modulesadmin class in the modulelist module
    * @access private
    */
    private $objModules;

    /**
    * @var boolean $assignment: TRUE if the assignmentadmin module is registered, FALSE if not
    * @access public
    */
    private $assignment;

    /**
    * @var object $objEditor: The htmlarea class in the htmlelements module
    * @access public
    */
    private $objEditor;

    /**
    * @var object $objMsg: The timeoutmessage class in the htmlelements module
    * @access public
    */
    private $objMsg;

    /**
    * @var object $objTabbedbox: The tabbedbox class in the htmlelements module
    * @access public
    */
    private $objTabbedbox;

    /**
    * @var object $objGroupadmin: The groupadminmodel class in the groupadmin module
    * @access public
    */
    private $objGroupadmin;

    /**
    * @var object $objConfig: The dbsysconfig class in the sysconfig module
    * @access public
    */
    public $objConfig;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // load html element classes
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('layer','htmlelements');
        $this->loadClass('checkbox','htmlelements');
        $this->loadClass('label','htmlelements');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objPopupcal = $this->newObject('datepickajax', 'popupcalendar');
        $this->objEditor = $this->newObject('htmlarea','htmlelements');
        $this->objMsg = $this->newObject('timeoutmessage','htmlelements');
        $this->objTabbedbox = $this->newObject('tabbedbox','htmlelements');
        $this->objGroupadmin = $this->newObject('groupadminmodel','groupadmin');

        // system classes
        $this->objLanguage = $this->getObject('language','language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objDatetime = $this->getObject('dateandtime', 'utilities');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->objConfig = $this->getObject('altconfig', 'config');

        // system variables
        $this->userId = $this->objUser->userId();
        $this->isLecturer = $this->objUser->isContextLecturer();
        $this->isStudent = $this->objUser->isContextStudent();
        $this->contextCode = $this->objContext->getContextCode();
        $this->menuText = $this->objContext->getMenuText($this->contextCode);

        // tutorials classes
        $this->objDbTutorials = $this->getObject('dbtutorials', 'tutorials');

        // assessment modules
        $this->assignment = FALSE;
        if($this->objModules->checkIfRegistered('Assignment Management', 'assignmentadmin')){
            $this->assignment = TRUE;

        }
        $css = '<link id="calender_css" type="text/css" rel="stylesheet" href="'.$this->objConfig->getModuleURI().$this->getParam('module').'/resources/tutorials.css" />';
        $this->appendArrayVar('headerParams', $css);
    }

    /**
    * Method to output the lecturer tutorial home page
    *
    * @access public
    * @return string $content: The template output string
    */
    public function showLecturerHome()
    {
        // get data
        $tutorials = $this->objDbTutorials->getContextTuts($this->contextCode);
        $instructions = $this->objDbTutorials->getInstructions();

        // set up language elements
        $lblStudents = $this->objLanguage->languageText('word_students');
        $array = array();
        $array['coursename'] = $this->menuText;
        $lblHeading = $this->objLanguage->code2Txt('mod_tutorials_administration', 'tutorials', $array);
        $lblAdd = $this->objLanguage->languageText('mod_tutorials_add', 'tutorials');
        $lblEdit = $this->objLanguage->languageText('mod_tutorials_edit', 'tutorials');
        $array = array();
        $array['readonlys'] = $lblStudents;
        $lblAddInstructions = $this->objLanguage->code2Txt('mod_tutorials_instructions', 'tutorials', $array);
        $lblName = $this->objLanguage->languageText('word_name');
        $lblQuestions = $this->objLanguage->languageText('word_questions');
        $lblStatus = $this->objLanguage->languageText('phrase_activitystatus');
        $lblPercentage = $this->objLanguage->languageText('word_percentage');
        $lblMark = $this->objLanguage->languageText('phrase_totalmark');
        $lblNoRecords = $this->objLanguage->languageText('mod_tutorials_norecords', 'tutorials');
        $lblAssignments = $this->objLanguage->languageText('mod_assignmentadmin_name');
        $lblConfirm = $this->objLanguage->languageText('mod_tutorials_deleteconfim', 'tutorials');
        $array['readonlys'] = $lblStudents;
        $lblList = $this->objLanguage->code2Txt('mod_tutorials_liststudents', 'tutorials', $array);
        $lblModerate = $this->objLanguage->languageText('phrase_moderatetutorial');
        $lblInstructions = $this->objLanguage->languageText('word_instructions');

        // set up add tutorial icon
        $this->objIcon->title = $lblAdd;
        $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
            'action' => 'tutorial',
        ), 'tutorials'));

        // set up tutorial instruction icon
        $this->objIcon->title = $lblAddInstructions;
        $this->objIcon->extra = '';
        $icoInstructions = $this->objIcon->getLinkedIcon($this->uri(array(
            'action' => 'instructions',
        )), 'configure');

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading.'&#160;'.$icoAdd.'&#160;'.$icoInstructions;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // display instructions
        if($instructions != FALSE){
            // set up delete icon
            $deleteArray = array(
                'action' => 'deleteinstructions',
            );
            $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'tutorials', $lblConfirm);

            // tabbed box
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel('<b>'.$lblInstructions.'</b>&#160;'.$icoDelete);
            $this->objTabbedbox->addBoxContent($instructions['instructions']);
            $content .= $this->objTabbedbox->show();
        }

        // set up links
        $this->objLink = new link($this->uri(array(
            'action'=>'tutorial',
        ), 'tutorials'));
        $this->objLink->link = $lblAdd;
        $lnkAdd = $this->objLink->show();

        if($this->assignment){
            $this->objLink = new link($this->uri(array(), 'assignmentadmin'));
            $this->objLink->link = $lblAssignments;
            $lnkAssignments = $this->objLink->show();

            $links = $lnkAdd.'&#160;|&#160;'.$lnkAssignments;
        }else{
            $links = $lnkAdd;
        }

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';

        $this->objTable->startRow();
        $this->objTable->addCell($lblName, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblQuestions, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblStatus, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblPercentage, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblMark, '', '', '', 'tuts-header', '');
        $this->objTable->addCell('', '', '', '', 'tuts-header', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';

        if($tutorials == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', 'colspan="6"');
            $this->objTable->endRow();
        }else{
            foreach($tutorials as $tutorial){
                $status = $this->tutStatus($tutorial['id']);

                // get data
                $questions = $this->objDbTutorials->getQuestions($tutorial['id']);
                if(empty($questions)){
                    $count = 0;
                }else{
                    $count = count($questions);
                }

                // set up view link
                $this->objLink = new link($this->uri(array(
                    'action' => 'view',
                    'id' => $tutorial['id'],
                ), 'tutorials'));
                $this->objLink->link = $tutorial['name'];
                $lnkName = $this->objLink->show();

                // set up edit icon
                $this->objIcon->title=$lblEdit;
                $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'tutorial',
                    'id' => $tutorial['id'],
                ), 'tutorials'));

                // set up delete icon
                $deleteArray = array(
                    'action' => 'deletetutorial',
                    'id' => $tutorial['id'],
                );
                $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'tutorials', $lblConfirm);

                // set up list students icon
                if($status['value'] != 1){
                    $this->objIcon->title = $lblList;
                    $this->objIcon->extra = '';
                    $icoList = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'liststudents',
                        'id' => $tutorial['id'],
                    )), 'comment');
                }else{
                    $icoList = '';
                }

                // set up moderate Icon
                if($status['value'] >= 6 and $tutorial['tutorial_type'] == 1){
                    $this->objIcon->title = $lblModerate;
                    $this->objIcon->extra = '';
                    $icoModerate = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'moderate',
                        'id' => $tutorial['id'],
                    )), 'options');
                }else{
                    $icoModerate = '';
                }

                $this->objTable->startRow();
                $this->objTable->addCell($lnkName, '', '', '', '', '');
                $this->objTable->addCell($count, '', '', '', '', '');
                $this->objTable->addCell($status['text'], '', '', '', '', '');
                $this->objTable->addCell($tutorial['percentage'], '', '', '', '', '');
                $this->objTable->addCell($tutorial['total_mark'], '', '', '', '', '');
                $this->objTable->addCell($icoEdit.'&#160;'.$icoDelete.'&#160;'.$icoList.'&#160;'.$icoModerate, '', '', '', '', '');
                $this->objTable->endRow();
            }
        }
        $content .= $this->objTable->show();

        $this->objTable = new htmltable();
        $this->objTable->startRow();
        $this->objTable->addCell($links, '', '', 'center', '' ,'');
        $this->objTable->endRow();

        $content .= $this->objTable->show();

        return $content;
    }

    /**
    * Method to output the tutorial status
    *
    * @access private
    * @param array $id: The id of the tutorial
    * @return array $status: The status output array
    */
    public function tutStatus($id, $isStudent = FALSE)
    {
        // get data
        $tut = $this->objDbTutorials->getTutorial($id);
        $type = $tut['tutorial_type'];
        $date = strtotime(date('Y-m-d H:i:s'));
        $open = $tut['answer_open'];
        $close = $tut['answer_close'];
        if($isStudent == TRUE){
            $late = $this->objDbTutorials->getLate($tut['id'], $this->userId);
            if($late != FALSE){
                $open = $late['answer_open'];
                $close = $late['answer_close'];
            }
        }
        $answerOpen = strtotime($open);
        $answerClose = strtotime($close);
        $markOpen = strtotime($tut['marking_open']);
        $markClose = strtotime($tut['marking_close']);
        $modOpen = strtotime($tut['moderation_open']);
        $modClose = strtotime($tut['moderation_close']);

        // set up language elements
        $lblNotOpen = $this->objLanguage->languageText('mod_tutorials_notopen', 'tutorials');
        $array = array();
        $array['date'] = $this->objDatetime->formatDate($open, FALSE);
        $lblAnswerOpen = $this->objLanguage->code2Txt('mod_tutorials_aopen', 'tutorials', $array);
        $lblAnswer = $this->objLanguage->languageText('mod_tutorials_answer', 'tutorials');
        $array = array();
        $array['date'] = $this->objDatetime->formatDate($close, FALSE);
        $lblAnswerClose = $this->objLanguage->code2Txt('mod_tutorials_aclose', 'tutorials', $array);
        $array = array();
        $array['date'] = $this->objDatetime->formatDate($tut['marking_open'], FALSE);
        $lblMarkOpen = $this->objLanguage->code2Txt('mod_tutorials_mopen', 'tutorials', $array);
        $lblMark = $this->objLanguage->languageText('mod_tutorials_mark', 'tutorials');
        $array = array();
        $array['date'] = $this->objDatetime->formatDate($tut['marking_close'], FALSE);
        $lblMarkClose = $this->objLanguage->code2Txt('mod_tutorials_mclose', 'tutorials', $array);
        $array = array();
        $array['date'] = $this->objDatetime->formatDate($tut['moderation_open'], FALSE);
        $lblModOpen = $this->objLanguage->code2Txt('mod_tutorials_modopen', 'tutorials', $array);
        $lblMod = $this->objLanguage->languageText('mod_tutorials_mod', 'tutorials');
        $array = array();
        $array['date'] = $this->objDatetime->formatDate($tut['moderation_close'], FALSE);
        $lblModClose = $this->objLanguage->code2Txt('mod_tutorials_modclose', 'tutorials', $array);
        $lblCompleted = $this->objLanguage->languageText('word_completed');

        $status = array();
        if($type == 0){
            if($date < $answerOpen){
                $content = '<font class="error">'.$lblNotOpen.'</font>';
                $content .= '<br />'.$lblAnswerOpen;
                $value = 1;
            }elseif($date >= $answerOpen and $date < $answerClose){
                $content = '<font class="error">'.$lblAnswer.'</font>';
                $content .= '<br />'.$lblAnswerClose;
                $value = 2;
            }else{
                $content = '<font class="error">'.$lblCompleted.'</font>';
                $value = 7;
            }
        }else{
            if($date < $answerOpen){
                $content = '<font class="error">'.$lblNotOpen.'</font>';
                $content .= '<br />'.$lblAnswerOpen;
                $value = 1;
            }elseif($date >= $answerOpen and $date < $answerClose){
                $content = '<font class="error">'.$lblAnswer.'</font>';
                $content .= '<br />'.$lblAnswerClose;
                $content .= '<br />'.$lblMarkOpen;
                $value = 2;
            }elseif($date >= $answerClose and $date < $markOpen){
                $content = '<font class="error">'.$lblNotOpen.'</font>';
                $content .= '<br />'.$lblMarkOpen;
                $value = 3;
            }elseif($date >= $markOpen and $date < $markClose){
                $content = '<font class="error">'.$lblMark.'</font>';
                $content .= '<br />'.$lblMarkClose;
                $content .= '<br />'.$lblModOpen;
                $value = 4;
            }elseif($date >= $markClose and $date < $modOpen){
                $content = '<font class="error">'.$lblNotOpen.'</font>';
                $content .= '<br />'.$lblModOpen;
                $value = 5;
            }elseif($date >= $modOpen and $date < $modClose){
                $content = '<font class="error">'.$lblMod.'</font>';
                $content .= '<br />'.$lblModClose;
                $value = 6;
            }else{
                $content = '<font class="error">'.$lblCompleted.'</font>';
                $value = 7;
            }
        }
        $status['text'] = '<b>'.$content.'</b>';
        $status['value'] = $value;

        return $status;
    }

    /**
    * Method to output the add/edit tutorial page
    *
    * @access public
    * @param string $id: The id of the tutorial to edit if applicable
    * @return string $content: The template output string
    */
    public function showAddEditTut($id)
    {
        // get data
        if(empty($id)){
            $name = '';
            $type = 0;
            $style = 'style="display: none" ';
            $percentage = 0;
            $answerOpen = date('Y-m-d H:i');
            $answerClose = date('Y-m-d H:i');
            $markOpen = date('Y-m-d H:i');
            $markClose = date('Y-m-d H:i');
            $moderateOpen = date('Y-m-d H:i');
            $moderateClose = date('Y-m-d H:i');
            $penalty = 0;
            $description = '';
        }else{
            $tutorial = $this->objDbTutorials->getTutorial($id);
            $name = $tutorial['name'];
            $type = $tutorial['tutorial_type'];
            if($type == 0){
                $style = 'style="display: none" ';
            }else{
                $style = '';
            }
            $percentage = $tutorial['percentage'];
            $answerOpen = $tutorial['answer_open'];
            $answerClose = $tutorial['answer_close'];
            $markOpen = $tutorial['marking_open'];
            $markClose = $tutorial['marking_close'];
            $moderateOpen = $tutorial['moderation_open'];
            $moderateClose = $tutorial['moderation_close'];
            $penalty = $tutorial['penalty'];
            $description = $tutorial['description'];
        }

        // set up language elements
        $lblAdd = $this->objLanguage->languageText('mod_tutorials_add', 'tutorials');
        $lblEdit = $this->objLanguage->languageText('mod_tutorials_edit', 'tutorials');
        $lblName = $this->objLanguage->languageText('word_name');
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblSelect = $this->objLanguage->languageText('phrase_selectdate');
        $lblStandard = $this->objLanguage->languageText('word_standard');
        $lblInteractive = $this->objLanguage->languageText('word_interactive');
        $lblDescription = $this->objLanguage->languageText('word_description');
        $lblPercentage = $this->objLanguage->languageText('mod_tutorials_percentage', 'tutorials');
        $lblType = $this->objLanguage->languageText('mod_tutorials_type', 'tutorials');
        $lblNameRequired = $this->objLanguage->languageText('mod_tutorials_namerequired', 'tutorials');
        $lblPercentRequired = $this->objLanguage->languageText('mod_tutorials_percentrequired', 'tutorials');
        $lblPercentNumeric = $this->objLanguage->languageText('mod_tutorials_numericpercent', 'tutorials');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnhome', 'tutorials');
        $lblAnswerOpen = $this->objLanguage->languageText('mod_tutorials_answerstart', 'tutorials');
        $lblAnswerClose = $this->objLanguage->languageText('mod_tutorials_answerclose', 'tutorials');
        $lblMarkOpen = $this->objLanguage->languageText('mod_tutorials_markstart', 'tutorials');
        $lblMarkClose = $this->objLanguage->languageText('mod_tutorials_markclose', 'tutorials');
        $lblModerateOpen = $this->objLanguage->languageText('mod_tutorials_moderatestart', 'tutorials');
        $lblModerateClose = $this->objLanguage->languageText('mod_tutorials_moderateclose', 'tutorials');
        $lblPenalty = $this->objLanguage->languageText('mod_tutorials_penalty', 'tutorials');
        $lblPenaltyMax = $this->objLanguage->languageText('mod_tutorials_penaltymax', 'tutorials');
        $lblPenaltyRequired = $this->objLanguage->languageText('mod_tutorials_penaltyrequired', 'tutorials');
        $lblPenaltyNumeric = $this->objLanguage->languageText('mod_tutorials_penaltynumeric', 'tutorials');
        $lblPenaltyLess = $this->objLanguage->languageText('mod_tutorials_penaltyless', 'tutorials');
        $lblPenaltyGreater = $this->objLanguage->languageText('mod_tutorials_penaltygreater', 'tutorials');

        // set up page heading
        if(empty($id)){
            $lblHeading = $lblAdd;
        }else{
            $lblHeading = $lblEdit;
        }
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up htmlelements
        $this->objInput = new textinput('name', $name, '', '70');
        $inpName = $this->objInput->show();

        $this->objDrop = new dropdown('type');
        $this->objDrop->addOption(0, $lblStandard.'&#160;');
        $this->objDrop->addOption(1, $lblInteractive.'&#160;');
        $this->objDrop->setselected($type);
        $this->objDrop->extra = 'onchange="if(this.value == \'0\'){Element.hide(\'markOpen\');Element.hide(\'markClose\');Element.hide(\'moderateOpen\');Element.hide(\'moderateClose\');Element.hide(\'penalty\');adjustLayout();}else{Element.show(\'markOpen\');Element.show(\'markClose\');Element.show(\'moderateOpen\');Element.show(\'moderateClose\');Element.show(\'penalty\');adjustLayout();}"';
        $drpType = $this->objDrop->show();

        $this->objInput = new textinput('percentage', $percentage, '', '4');
        $this->objInput->extra='MAXLENGTH=5';
        $inpPercentage = $this->objInput->show();

        $this->objText = new textarea('description', $description, '5', '68');
        $txtDescription = $this->objText->show();

        $inpAnswerOpen = $this->objPopupcal->show('answerOpen', 'yes', 'no', $answerOpen);
        $inpAnswerClose = $this->objPopupcal->show('answerClose', 'yes', 'no', $answerClose);
        $inpMarkOpen = $this->objPopupcal->show('markOpen', 'yes', 'no', $markOpen);
        $inpMarkClose = $this->objPopupcal->show('markClose', 'yes', 'no', $markClose);
        $inpModerateOpen = $this->objPopupcal->show('moderateOpen', 'yes', 'no', $moderateOpen);
        $inpModerateClose = $this->objPopupcal->show('moderateClose', 'yes', 'no', $moderateClose);

        $this->objInput = new textinput('penalty', $penalty, '', '4');
        $this->objInput->extra='MAXLENGTH=5';
        $inpPenalty = $this->objInput->show();

        $this->objButton=new button('submit',$lblSubmit);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
        $btnCancel = $this->objButton->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
        $this->objTable->cellspacing = '2';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblName.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpName, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblType.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($drpType, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblPercentage.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpPercentage, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblAnswerOpen.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpAnswerOpen, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblAnswerClose.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpAnswerClose, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = $style.'id="markOpen" onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblMarkOpen.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpMarkOpen, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = $style.'id="markClose" onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblMarkClose.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpMarkClose, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = $style.'id="moderateOpen" onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblModerateOpen.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpModerateOpen, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = $style.'id="moderateClose" onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblModerateClose.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpModerateClose, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = $style.'id="penalty" onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblPenalty.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($inpPenalty.' <b><font class="error">'.$lblPenaltyMax.'</font></b>', '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblDescription.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($txtDescription, '', '', '', '', '');
        $this->objTable->endRow();

        $tblDisplay = $this->objTable->show();

        // set up forms
        $this->objForm=new form('frmTutorials',$this->uri(array(
            'action'=>'savetutorial',
            'id' => $id,
        ), 'tutorials'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $this->objForm->addRule('name', $lblNameRequired, 'required');
        $this->objForm->addRule('percentage', $lblPercentRequired, 'required');
        $this->objForm->addRule('percentage', $lblPercentNumeric, 'floatingpoint');
        $this->objForm->addRule('penalty', $lblPenaltyRequired, 'required');
        $this->objForm->addRule('penalty', $lblPenaltyNumeric, 'floatingpoint');
        $this->objForm->addRule(array(
            'name' => 'penalty',
            'minnumber' => 0,
        ), $lblPenaltyLess, 'minnumber');
        $this->objForm->addRule(array(
            'name' => 'penalty',
            'maxnumber' => 33.33,
        ), $lblPenaltyGreater, 'maxnumber');

        $content .= $this->objForm->show();

        $this->objForm=new form('frmCancel',$this->uri(array(), 'tutorials'));
        $content .= $this->objForm->show();

        $this->objLink = new link($this->uri(array(),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to output the tutorial instructions page
    *
    * @access public
    * @return string $content: The template output string
    */
    public function showInstructions()
    {
        // get data
        $instructions = $this->objDbTutorials->getInstructions();
        $date = $this->objDatetime->formatDate(date('Y-m-d H:i:s'));

        // set up language elements
        $array['coursename'] = $this->menuText;
        $lblHeading = $this->objLanguage->code2Txt('mod_tutorials_administration', 'tutorials', $array);
        $lblStudents = $this->objLanguage->languageText('word_students');
        $array = array();
        $array['readonlys'] = $lblStudents;
        $lblInstructions = $this->objLanguage->code2Txt('mod_tutorials_instructions', 'tutorials', $array);
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $array = array();
        $array['date'] = $date;
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnhome', 'tutorials');

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblInstructions;
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        // set up htmlelements
        $this->objEditor->init('instructions', $instructions['instructions'], '300px', '70%', NULL);
        $this->objEditor->setDefaultToolBarSetWithoutSave();
        $edtInstructions = $this->objEditor->show();

        $this->objButton=new button('submit',$lblSubmit);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
        $btnCancel = $this->objButton->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';

        $this->objTable->startRow();
        $this->objTable->addCell($edtInstructions, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('', '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($btnSubmit.'&#160;'.$btnCancel, '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // set up forms
        $this->objForm=new form('frmInstructions',$this->uri(array(
            'action' => 'saveinstructions',
        ), 'tutorials'));
        $this->objForm->addToForm($tblDisplay);
        $content .= $this->objForm->show();

        $this->objForm=new form('frmCancel',$this->uri(array(), 'tutorials'));
        $content .= $this->objForm->show();

        $this->objLink = new link($this->uri(array(), 'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to output the view tutorial page
    *
    * @access public
    * @param string $id: The tutorial to view
    * @return string $content: The template output string
    */
    public function showLecturerView($id)
    {
        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $answerOpen = $this->objDatetime->formatDate($tutorial['answer_open']);
        $answerClose = $this->objDatetime->formatDate($tutorial['answer_close']);
        $markOpen = $this->objDatetime->formatDate($tutorial['marking_open']);
        $markClose = $this->objDatetime->formatDate($tutorial['marking_close']);
        $moderateOpen = $this->objDatetime->formatDate($tutorial['moderation_open']);
        $moderateClose = $this->objDatetime->formatDate($tutorial['moderation_close']);
        $questions = $this->objDbTutorials->getQuestions($id);
        $status = $this->tutStatus($id);

        // set up language elements
        $lblView = $this->objLanguage->languageText('mod_tutorials_view', 'tutorials');
        $lblEdit = $this->objLanguage->languageText('mod_tutorials_edit', 'tutorials');
        $lblQuestions = $this->objLanguage->languageText('word_questions');
        $lblTutorials = $this->objLanguage->languageText('word_tutorials');
        $lblName = $this->objLanguage->languageText('word_name');
        $lblStandard = $this->objLanguage->languageText('word_standard');
        $lblInteractive = $this->objLanguage->languageText('word_interactive');
        $lblDescription = $this->objLanguage->languageText('word_description');
        $lblPercentage = $this->objLanguage->languageText('mod_tutorials_percentage', 'tutorials');
        $lblType = $this->objLanguage->languageText('mod_tutorials_type', 'tutorials');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnhome', 'tutorials');
        $lblAnswerOpen = $this->objLanguage->languageText('mod_tutorials_answerstart', 'tutorials');
        $lblAnswerClose = $this->objLanguage->languageText('mod_tutorials_answerclose', 'tutorials');
        $lblMarkOpen = $this->objLanguage->languageText('mod_tutorials_markstart', 'tutorials');
        $lblMarkClose = $this->objLanguage->languageText('mod_tutorials_markclose', 'tutorials');
        $lblModerateOpen = $this->objLanguage->languageText('mod_tutorials_moderatestart', 'tutorials');
        $lblModerateClose = $this->objLanguage->languageText('mod_tutorials_moderateclose', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblNo = $this->objLanguage->languageText('word_no');
        $lblAllocated = $this->objLanguage->languageText('phrase_allocatedmark');
        $lblNoRecords = $this->objLanguage->languageText('mod_tutorials_norecords', 'tutorials');
        $lblTotal = $this->objLanguage->languageText('phrase_totalmark');
        $lblAdd = $this->objLanguage->languageText('mod_tutorials_addquestion', 'tutorials');
        $lblConfirm = $this->objLanguage->languageText('mod_tutorials_deleteconfim', 'tutorials');
        $lblAllConfirm = $this->objLanguage->languageText('mod_tutorials_allconfim', 'tutorials');
        $lblDown = $this->objLanguage->languageText('phrase_movedown');
        $lblUp = $this->objLanguage->languageText('phrase_moveup');
        $lblStatus = $this->objLanguage->languageText('phrase_activitystatus');
        $lblImport = $this->objLanguage->languageText('phrase_importquestions');
        $lblPenalty = $this->objLanguage->languageText('mod_tutorials_penalty', 'tutorials');

        if($tutorial['tutorial_type'] == 0){
            $type = $lblStandard;
        }else{
            $type = $lblInteractive;
        }

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblView;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // display table
        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
        $this->objTable->cellspacing = '2';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblName.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($tutorial['name'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblType.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($type, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblPercentage.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($tutorial['percentage'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblTotal.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($tutorial['total_mark'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblAnswerOpen.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($answerOpen, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblAnswerClose.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($answerClose, '', '', '', '', '');
        $this->objTable->endRow();
        if($tutorial['tutorial_type'] == 1){
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblMarkOpen.'&#160:</b>', '33%', '', '', 'tuts-header', '');
            $this->objTable->addCell($markOpen, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblMarkClose.'&#160:</b>', '33%', '', '', 'tuts-header', '');
            $this->objTable->addCell($markClose, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblModerateOpen.'&#160:</b>', '33%', '', '', 'tuts-header', '');
            $this->objTable->addCell($moderateOpen, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblModerateClose.'&#160:</b>', '33%', '', '', 'tuts-header', '');
            $this->objTable->addCell($moderateClose, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblPenalty.'&#160:</b>', '33%', '', '', 'tuts-header', '');
            $this->objTable->addCell($tutorial['penalty'], '', '', '', '', '');
            $this->objTable->endRow();
        }
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblDescription.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell(nl2br($tutorial['description']), '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblStatus.'&#160:</b>', '33%', '', '', 'tuts-header', '');
        $this->objTable->addCell($status['text'], '', '', '', '', '');
        $this->objTable->endRow();
        $tblTutorials = $this->objTable->show();

        // set up icons
        $this->objIcon->title=$lblEdit;
        $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
            'action' => 'tutorial',
            'id' => $tutorial['id'],
        ), 'tutorials'));

        // tabbed box
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel('<b>'.$lblTutorials.'</b>&#160;'.$icoEdit);
        $this->objTabbedbox->addBoxContent($tblTutorials);
        $content .= $this->objTabbedbox->show();

        // display table
        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
        $this->objTable->cellspacing = '2';

        $this->objTable->startRow();
        $this->objTable->addCell($lblNo, '5%', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblQuestion, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblAllocated.'<br />('.$lblTotal.':&#160;'.$tutorial['total_mark'].')', '15%', '', '', 'tuts-header', '');
        $this->objTable->addCell('', '10%', '', '', 'tuts-header', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\'" onmouseout="this.className=\'\'"';

        if($questions == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', 'colspan="4"');
            $this->objTable->endRow();
        }else{
            foreach($questions as $question){

                // set up edit icon
                $this->objIcon->title=$lblEdit;
                $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'questions',
                    'tutId' => $question['tutorial_id'],
                    'id' => $question['id'],
                ), 'tutorials'));

                // set up delete icon
                $deleteArray = array(
                    'action' => 'deletequestion',
                    'tutId' => $question['tutorial_id'],
                    'id' => $question['id'],
                );
                $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'tutorials', $lblConfirm);

                // set up move down icon
                $this->objIcon->title = $lblDown;
                $this->objIcon->extra = '';
                $icoDown = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'movequestions',
                    'tutId' => $question['tutorial_id'],
                    'id' => $question['id'],
                    'dir' => 'down',
                )), 'mvdown');

                // set up move up icon
                $this->objIcon->title = $lblUp;
                $this->objIcon->extra = '';
                $icoUp = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'movequestions',
                    'tutId' => $question['tutorial_id'],
                    'id' => $question['id'],
                    'dir' => 'up',
                )), 'mvup');

                if(count($questions) > 1){
                    if($question['question_order'] == 1){
                        $icoMove = $icoDown.'&#160;&#160;&#160;';
                    }elseif($question['question_order'] == count($questions)){
                        $icoMove = $icoUp;
                    }else{
                        $icoMove = $icoDown.'&#160;'.$icoUp;
                    }
                }else{
                    $icoMove = '';
                }

                $this->objTable->startRow();
                $this->objTable->addCell($question['question_order'], '', '', '', '', '');
                $this->objTable->addCell($question['question'], '', '', '', '', '');
                $this->objTable->addCell($question['question_value'], '', '', '', '', '');
                $this->objTable->addCell($icoMove.'&#160;'.$icoEdit.'&#160;'.$icoDelete, '', '', 'right', '', '');
                $this->objTable->endRow();
            }
        }
        $tblQuestions = $this->objTable->show();

        // set up add questions icon
        $this->objIcon->title = $lblAdd;
        $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
            'action' => 'questions',
            'tutId' => $id,
        ), 'tutorials'));

        // set up delete icon
        if($questions != FALSE){
            $deleteArray = array(
                'action' => 'deleteall',
                'tutId' => $id,
            );
            $icoDelete = '&#160;'.$this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'tutorials', $lblAllConfirm);
        }else{
            $icoDelete = '';
        }

        // set up import questions icon
        $this->objIcon->title = $lblImport;
        $this->objIcon->extra = '';
        $icoImport = $this->objIcon->getLinkedIcon($this->uri(array(
            'action' => 'import',
            'id' => $id,
        )), 'importcvs');

        // tabbed box
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel('<b>'.$lblQuestions.'</b>&#160;'.$icoAdd.$icoDelete.'&#160;'.$icoImport);
        $this->objTabbedbox->addBoxContent($tblQuestions);
        $content .= $this->objTabbedbox->show();

        // return link
        $this->objLink = new link($this->uri(array(), 'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to output the add/edit questions page
    *
    * @access public
    * @param string $tutId: The id of the tutorial the question is for
    * @param string $id: The id of the question to edit if applicable
    * @param string $e: The type of error if applicable
    * @param string $q: The question text
    * @param string $m: The model answer text
    * @param string $w: The question value
    * @return string $content: The template output string
    */
    public function showAddEditQuestions($tutId, $id, $e = NULL, $q = NULL, $m = NULL, $w = NULL)
    {
        // get data
        $questions = $this->objDbTutorials->getQuestions($tutId);
        $count = ($questions == FALSE) ? 1: count($questions) + 1;
        if(empty($id)){
            $question = '';
            $model = '';
            $worth = 0;
            $number = $count;
        }else{
            $arrQuestion = $this->objDbTutorials->getQuestionById($id);
            $question = $arrQuestion['question'];
            $model = $arrQuestion['model_answer'];
            $worth = $arrQuestion['question_value'];
            $number = $arrQuestion['question_order'];
        }
        if($e == 'question'){
            $question = $q;
            $model = $m;
            $worth = $w;
        }elseif($e == 'model'){
            $question = $q;
            $model = $m;
            $worth = $w;
        }

        // set up language elements
        $lblAdd = $this->objLanguage->languageText('mod_tutorials_addquestion', 'tutorials');
        $lblEdit = $this->objLanguage->languageText('mod_tutorials_editquestion', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblModel = $this->objLanguage->languageText('phrase_modelanswer');
        $lblWorth = $this->objLanguage->languageText('phrase_questionworth');
        $lblSubmitAdd = $this->objLanguage->languageText('mod_tutorials_submitnew', 'tutorials');
        $lblQuestionRequired = $this->objLanguage->languageText('mod_tutorials_questionrequired', 'tutorials');
        $lblModelRequired = $this->objLanguage->languageText('mod_tutorials_modelrequired', 'tutorials');
        $lblWorthRequired = $this->objLanguage->languageText('mod_tutorials_worrhrequired', 'tutorials');
        $lblWorthNumeric = $this->objLanguage->languageText('mod_tutorials_worthnumeric', 'tutorials');
        $lblWorthGreater = $this->objLanguage->languageText('mod_tutorials_worthgtzero', 'tutorials');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnview', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');

        if($e == 'question'){
            $body = 'alert("'.$lblQuestionRequired.'")';
            $this->appendArrayVar('bodyOnLoad', $body);
        }elseif($e == 'model'){
            $body = 'alert("'.$lblModelRequired.'")';
            $this->appendArrayVar('bodyOnLoad', $body);
        }

        // set up page heading
        if(empty($id)){
            $lblHeading = $lblAdd;
        }else{
            $lblHeading = $lblEdit;
        }
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up htmlelements
        $this->objEditor->init('question', $question, '500px', '100%', NULL);
        $this->objEditor->setDefaultToolBarSetWithoutSave();
        $edtQuestion = $this->objEditor->show();

        $this->objEditor->init('model', $model, '500px', '100%', NULL);
        $this->objEditor->setDefaultToolBarSetWithoutSave();
        $edtModel = $this->objEditor->show();

        $this->objInput = new textinput('worth', $worth);
        $inpWorth = $this->objInput->show();

        $this->objButton=new button('submit',$lblSubmit);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('submitAdd',$lblSubmitAdd);
        $this->objButton->setToSubmit();
        if(empty($id)){
            $btnSubmitAdd = $this->objButton->show().'&#160';
        }else{
            $btnSubmitAdd = '';
        }

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
        $btnCancel = $this->objButton->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblQuestion.'&#160:</b><br />'.$edtQuestion, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblModel.'&#160:</b><br />'.$edtModel, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblWorth.'&#160:</b><br />'.$inpWorth, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = '';
        $this->objTable->startRow();
        $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($btnSubmit.'&#160;'.$btnSubmitAdd.$btnCancel, '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // set up forms
        $this->objForm=new form('frmQuestions',$this->uri(array(
            'action' => 'savequestion',
            'tutId' => $tutId,
            'id' => $id,
        ), 'tutorials'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addRule('worth', $lblWorthRequired, 'required');
        $this->objForm->addRule('worth', $lblWorthNumeric, 'numeric');
        $this->objForm->addRule(array(
            'name' => 'worth',
            'minnumber' => 1,
        ), $lblWorthGreater, 'minnumber');

        $tabContent = $this->objForm->show();

        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'view',
            'id' => $tutId,
        ), 'tutorials'));
        $tabContent .= $this->objForm->show();

        // tabbed box
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel('<b>'.$lblQuestion.':</b>&#160;'.$number);
        $this->objTabbedbox->addBoxContent($tabContent);
        $content .= $this->objTabbedbox->show();

        $this->objLink = new link($this->uri(array(
            'action' => 'view',
            'id' => $tutId,
        ),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to output the student tutorial home page
    *
    * @access public
    * @return string $content: The template output string
    */
    public function showStudentHome()
    {
        // get data
        $tutorials = $this->objDbTutorials->getContextTuts($this->contextCode);
        $instructions = $this->objDbTutorials->getInstructions();

        // set up language elements
        $lblStudents = $this->objLanguage->languageText('word_students');
        $lblHeading = $this->objLanguage->languageText('mod_tutorials_name', 'tutorials');
        $lblName = $this->objLanguage->languageText('word_name');
        $lblQuestions = $this->objLanguage->languageText('word_questions');
        $lblStatus = $this->objLanguage->languageText('phrase_activitystatus');
        $lblPercentage = $this->objLanguage->languageText('word_percentage');
        $lblTotal = $this->objLanguage->languageText('phrase_totalmark');
        $lblNoRecords = $this->objLanguage->languageText('mod_tutorials_norecords', 'tutorials');
        $lblAssignment = $this->objLanguage->languageText('mod_assignment_name');
        $lblConfirm = $this->objLanguage->languageText('mod_tutorials_deleteconfim', 'tutorials');
        $lblAnswer = $this->objLanguage->languageText('mod_tutorials_answertut', 'tutorials');
        $lblMark = $this->objLanguage->languageText('mod_tutorials_marktut', 'tutorials');
        $lblAnswerComplete = $this->objLanguage->languageText('mod_tutorials_answercomplete', 'tutorials');
        $lblMarkComplete = $this->objLanguage->languageText('mod_tutorials_markcomplete', 'tutorials');
        $lblInstructions = $this->objLanguage->languageText('word_instructions');
        $lblMarkObtained = $this->objLanguage->languageText('phrase_markobtained');
        $lblLeft = $this->objLanguage->languageText('mod_tutorials_studentsleft', 'tutorials');
        $lblNotReady = $this->objLanguage->languageText('mod_tutorials_notready', 'tutorials');
        $lblUnavailable = $this->objLanguage->languageText('mod_tutorials_markingunavailable', 'tutorials');
        $lblNoAccess = $this->objLanguage->code2Txt('mod_tutorials_noaccess', 'tutorials', NULL);

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading.': '.$this->menuText;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        if($instructions != FALSE){
            // tabbed box
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel('<b>'.$lblInstructions.'</b>');
            $this->objTabbedbox->addBoxContent($instructions['instructions']);
            $content .= $this->objTabbedbox->show();
        }

        // set up links
        if($this->assignment){
            $this->objLink = new link($this->uri(array(), 'assignment'));
            $this->objLink->link = $lblAssignment;
            $lnkAssignments = $this->objLink->show();

            $links = $lnkAssignments;
        }else{
            $links = '';
        }

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';

        $this->objTable->startRow();
        $this->objTable->addCell($lblName, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblQuestions, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblStatus, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblPercentage, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblTotal, '', '', '', 'tuts-header', '');
        $this->objTable->addCell('', '', '', '', 'tuts-header', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        if($tutorials == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', 'colspan="6"');
            $this->objTable->endRow();
        }else{
            if($this->isStudent == TRUE){
                foreach($tutorials as $tutorial){
                    // get data
                    $status = $this->tutStatus($tutorial['id'], TRUE);
                    $questions = $this->objDbTutorials->getQuestions($tutorial['id']);
                    $results = $this->objDbTutorials->getResult($tutorial['id'], $this->userId);
                    $markedBy = $this->objDbTutorials->countCompletedMarked($tutorial['id'], $this->userId);
                    $markedFor = $this->objDbTutorials->getMarkedStudents($tutorial['id'], $this->userId);
                    $lecturer = $this->objDbTutorials->checkLecturerMarked($tutorial['id'], $this->userId);
                    $modComplete = $this->objDbTutorials->moderationComplete($tutorial['id']);

                    $qCount = empty($questions) ? 0 : count($questions);

                    // set up view link
                    $this->objLink = new link($this->uri(array(
                        'action' => 'view',
                        'id' => $tutorial['id'],
                    ), 'tutorials'));
                    $this->objLink->link = $tutorial['name'];
                    $lnkView = $this->objLink->show();

                    // set up answer link
                    $this->objLink = new link($this->uri(array(
                        'action' => 'answer',
                        'id' => $tutorial['id'],
                    ), 'tutorials'));
                    $this->objLink->link = $lblAnswer;
                    $lnkAnswer = $this->objLink->show();

                    // set up mark link
                    $this->objLink = new link($this->uri(array(
                        'action' => 'mark',
                        'id' => $tutorial['id'],
                    ), 'tutorials'));
                    $this->objLink->link = $lblMark;
                    $lnkMark = $this->objLink->show();

                    $link = '';
                    $name = $tutorial['name'];
                    switch($status['value']){
                        case 2:
                            if($results['has_submitted'] == 0){
                                if($questions != FALSE){
                                    $link = $lnkAnswer;
                                }
                            }else{
                                $link = $lblAnswerComplete;
                            }
                            break;
                        case 4:
                            if($results['has_submitted'] == 1){
                                if($markedFor < 3){
                                    $link = $lnkMark;
                                    $link .= '<br /><b>'.$lblLeft.': '.(3 - $markedFor).'</b>';
                                }else{
                                    $link = $lblMarkComplete;
                                }
                            }else{
                                $link = $lblUnavailable;
                            }
                            break;
                        case 6:
                        case 7:
                            $lecturer = $this->objDbTutorials->checkLecturerMarked($tutorial['id'], $this->userId);
                            if($markedBy == 3 or $lecturer == TRUE){
                                $name = $lnkView;
                                $mark = round(($results['mark_obtained'] / $tutorial['total_mark']) * 100, 0);
                                $late = $this->objDbTutorials->getLate($tutorial['id'], $this->userId);
                                if($late == FALSE){
                                    $penalty = ($tutorial['penalty'] * (3 - $markedFor));
                                    $mark = $mark - round(($mark * ($penalty / 100)), 0);
                                }
                                $mark = $mark.'%';
                                $link = $lblMarkObtained.' - '.$mark;
                            }elseif($results['has_submitted'] != 1){
                                $link = $lblMarkObtained.' - 0%';
                            }else{
                                $link = $lblNotReady;
                            }
                            break;
                    }

                    $this->objTable->startRow();
                    $this->objTable->addCell($name, '', '', '', '', '');
                    $this->objTable->addCell($qCount, '', '', '', '', '');
                    $this->objTable->addCell($status['text'], '', '', '', '', '');
                    $this->objTable->addCell($tutorial['percentage'], '', '', '', '', '');
                    $this->objTable->addCell($tutorial['total_mark'], '', '', '', '', '');
                    $this->objTable->addCell($link, '', '', '', '', '');
                    $this->objTable->endRow();
                }
            }else{
                $this->objTable->startRow();
                $this->objTable->addCell($lblNoAccess, '', '', '', 'noRecordsMessage', 'colspan="6"');
                $this->objTable->endRow();
            }
        }
        $this->objTable->row_attributes = '';
        $this->objTable->startRow();
        $this->objTable->addCell('', '', '', '', '' ,'colspan="6"');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($links, '', '', 'center', '' ,'colspan="6"');
        $this->objTable->endRow();

        $content .= $this->objTable->show();

        return $content;
    }

    /**
    * Method to output the answer tutorial page
    *
    * @access public
    * @param string $id: The id of the tutorial to answer
    * @param string $order: The question number to answer
    * @return string $content: The template output string
    */
    public function showAnswer($id, $order)
    {
        // add highlight labels
        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $questions = $this->objDbTutorials->getQuestions($id);
        $question = $questions[$order - 1];
        $answer = $this->objDbTutorials->getAnswer($question['id'], $this->userId);

        // set up language elements
        $lblHeading = $this->objLanguage->languageText('mod_tutorials_answertut', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblMark = $this->objLanguage->languageText('word_mark');
        $lblNext = $this->objLanguage->languageText('word_next');
        $lblPrevious = $this->objLanguage->languageText('word_previous');
        $lblSubmit = $this->objLanguage->languageText('phrase_submitformarking');
        $lblExit = $this->objLanguage->languageText('phrase_saveandexit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblAnswer = $this->objLanguage->languageText('word_answer');
        $lblOf = $this->objLanguage->languageText('word_of');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblConfirm = $this->objLanguage->languageText('phrase_submissionconfirmed');
        $lblConfirmSubmission = $this->objLanguage->languageText('mod_tutorials_confirmsubmission', 'tutorials');

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $string = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $string .= $header;

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblQuestion.':</b><br />'.$question['question'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblMark.':</b><br />'.$question['question_value'], '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // tabbed box
        $label = '<b>'.$lblQuestion.'  '.$order.'  '.strtolower($lblOf).'  '.count($questions).'</b>';
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel($label);
        $this->objTabbedbox->addBoxContent($tblDisplay);
        $string .= $this->objTabbedbox->show();

        // set up form elements
        $this->objInput = new textinput('inpSubmit', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        $this->objEditor->init('answer', $answer['answer'], '500px', '100%', NULL);
        $this->objEditor->setDefaultToolBarSetWithoutSave();
        $edtAnswer = $this->objEditor->show();

        $this->objButton=new button('next',$lblNext);
        $this->objButton->setToSubmit();
        $btnNext = $this->objButton->show();

        $this->objButton=new button('previous',$lblPrevious);
        $this->objButton->setToSubmit();
        $btnPrevious = $this->objButton->show();

        $this->objButton=new button('submitbutton',$lblSubmit);
        $this->objButton->extra = 'onclick="if($(\'input_inpConfirm\').checked){$(\'input_inpSubmit\').value=\'submit\';$(\'form_frmAnswer\').submit();}else{alert(\''.$lblConfirmSubmission.'\');$(\'input_inpConfirm\').focus();return false;}"';
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('exit',$lblExit);
        $this->objButton->setToSubmit();
        $btnExit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();

        $this->objCheck = new checkbox('inpConfirm');
        $this->objCheck->setValue('yes');
        $chkConfirm = $this->objCheck->show();

        $this->objLabel = new label($lblConfirm, 'input_inpConfirm');
        $lblCheck = $this->objLabel->show();

        if(count($questions) == 1){
            $buttons = $btnSubmit.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }elseif($order == 1){
            $buttons = $btnNext.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }elseif($order == count($questions)){
            $buttons = $btnPrevious.'&#160;'.$btnSubmit.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }else{
            $buttons = $btnNext.'&#160;'.$btnPrevious.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell($edtAnswer, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = '';
        $this->objTable->startRow();
        $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $this->objTable->startRow();
        if($order == count($questions)){
            $this->objTable->startRow();
            $this->objTable->addCell($chkConfirm.'&#160;'.$lblCheck, '', '', '', '', 'colspan="2"');
            $this->objTable->endRow();
        }
        $this->objTable->addCell($buttons, '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // set up forms
        $this->objForm=new form('frmAnswer',$this->uri(array(
            'action' => 'saveanswer',
            'id' => $id,
            'qId' => $question['id'],
            'order' => $order,
        ), 'tutorials'));
        $this->objForm->addToForm($inpHidden.$tblDisplay);
        $tabContent = $this->objForm->show();

        $this->objForm=new form('frmCancel',$this->uri(array(), 'tutorials'));
        $tabContent .= $this->objForm->show();

        // tabbed box
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel('<b>'.$lblAnswer.'</b>');
        $this->objTabbedbox->addBoxContent($tabContent);
        $string .= $this->objTabbedbox->show();

        $string .= $this->_showStudentLinks($id, 'answer');

        $this->objLayer = new layer();
        $this->objLayer->padding = '10px';
        $this->objLayer->addToStr($string);
        $content = $this->objLayer->show();

        return $content;
    }

    /**
    * Method to list links for a student
    *
    * @access private
    * @param string $id: The tutorial id
    * @param string $action: The action of the link
    * @return array|string $list: The links on success | NULL on failure
    */
    private function _showStudentLinks($id, $action)
    {
        // get data
        $data = $this->objDbTutorials->getStudentLinkData($id);

        // set up language elements
        $lblGoto = $this->objLanguage->languageText('mod_tutorials_gotoquestion', 'tutorials');

        if($data != FALSE){
            $links = '';
            foreach($data as $key => $line){
                $this->objLink = new link($this->uri(array(
                    'action' => $action,
                    'id' => $id,
                    'order' => $line['question_order'],
                ), 'tutorials'));
                $this->objLink->link = $line['question_order'];
                $links .= $this->objLink->show();
                if(count($data) != ($key + 1)){
                    $links .= '&#160;|&#160;';
                }
            }
            return '<b>'.$lblGoto.': </b>'.$links;
        }
        return '';
    }

    /**
    * Method to list links for a marker
    *
    * @access private
    * @param string $id: The tutorial id
    * @param string $studentId: The id of the student
    * @return array|string $list: The links on success | NULL on failure
    */
    private function _showMarkerLinks($id, $studentId)
    {
        // get data
        $data = $this->objDbTutorials->getMarkerLinkData($id, $studentId);

        // set up language elements
        $lblGoto = $this->objLanguage->languageText('mod_tutorials_gotoquestion', 'tutorials');

        if($data != FALSE){
            $links = '';
            foreach($data as $key => $line){
                if($this->isLecturer == FALSE){
                    $this->objLink = new link($this->uri(array(
                        'action' => 'mark',
                        'id' => $id,
                        'order' => $line['question_order'],
                    ), 'tutorials'));
                }else{
                    $this->objLink = new link($this->uri(array(
                        'action' => 'mark',
                        'id' => $id,
                        'studentId' => $studentId,
                        'order' => $line['question_order'],
                    ), 'tutorials'));
                }
                $this->objLink->link = $line['question_order'];
                $links .= $this->objLink->show();
                if(count($data) != ($key + 1)){
                    $links .= '&#160;|&#160;';
                }
            }
            return '<b>'.$lblGoto.': </b>'.$links;
        }
        return '';
    }

    /**
    * Method to show the list of students
    *
    * @param string $id: The id of the tutorial
    * @param bool $status: The status of results export of applicable
    * @return string $content: The template output string
    */
    public function showStudentList($id, $status)
    {
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $groupId = $this->objGroupadmin->getLeafId(array(
            $this->contextCode,
            'Students',
        ));
        $students = $this->objGroupadmin->getGroupUsers($groupId, array(
            'userid',
            'firstname',
            'surname',
        ));
        $date = date('Y-m-d H:i:s');

        // set up language elements
        $lblStudents = $this->objLanguage->languageText('word_students');
        $array['readonlys'] = $lblStudents;
        $lblList = $this->objLanguage->code2Txt('mod_tutorials_liststudents', 'tutorials', $array);
        $lblStudentNo = $this->objLanguage->languageText('mod_tutorials_studentno', 'tutorials');
        $lblName = $this->objLanguage->languageText('phrase_firstname');
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblNoRecords = $this->objLanguage->languageText('mod_tutorials_norecords', 'tutorials');
        $lblMark = $this->objLanguage->languageText('word_mark');
        $lblMarkTut = $this->objLanguage->languageText('mod_tutorials_marktut', 'tutorials');
        $lblStatus = $this->objLanguage->languageText('word_status');
        $lblSubmitted = $this->objLanguage->languageText('word_submitted');
        $lblNotStarted = $this->objLanguage->languageText('phrase_notstarted');
        $lblNotSubmitted = $this->objLanguage->languageText('word_started');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnhome', 'tutorials');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblLate = $this->objLanguage->languageText('mod_tutorials_late', 'tutorials');
        $lblOpen = $this->objLanguage->languageText('word_open');
        $lblClose = $this->objLanguage->languageText('word_close');
        $lblNotMarked = $this->objLanguage->languageText('phrase_notmarked');
        $lblMarking = $this->objLanguage->languageText('word_marking');
        $lblMarked = $this->objLanguage->languageText('word_marked');
        $lblCompleted = $this->objLanguage->languageText('word_completed');
        $lblAnswer = $this->objLanguage->languageText('mod_tutorials_answerlist', 'tutorials');
        $lblExport = $this->objLanguage->languageText('mod_tutorials_exportresults', 'tutorials');
        $array['date'] = $this->objDatetime->formatDate($date, FALSE);
        $lblTrue = $this->objLanguage->code2Txt('mod_tutorials_true', 'tutorials', $array);
        $lblFalse = $this->objLanguage->languageText('mod_tutorials_false', 'tutorials');
        $lblArchive = $this->objLanguage->languageText('mod_tutorials_archive', 'tutorials');
        $lblArchiveConfirm = $this->objLanguage->languageText('mod_tutorials_confirmarchive', 'tutorials');

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblList;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up answer list icon
        $this->objIcon->title = $lblAnswer;
        $this->objIcon->extra = '';
        $icoList = $this->objIcon->getLinkedIcon($this->uri(array(
            'action' => 'answerlist',
            'id' => $id,
        )), 'onlineresume');

        // set up export icon
        $this->objIcon->title = $lblExport;
        $this->objIcon->extra = '';
        if($tutorial['tutorial_type'] == 0){
            $icoExport = $this->objIcon->getLinkedIcon($this->uri(array(
                'action' => 'doexport',
                'id' => $id,
            )), 'exportcvs');
        }else{
            $icoExport = $this->objIcon->getLinkedIcon($this->uri(array(
                'action' => 'export',
                'id' => $id,
            )), 'exportcvs');
        }

        $icon = '';
        if($tutorial['tutorial_type'] == 1){
            $icon = ' '.$icoList;
        }
        $icon .= ' '.$icoExport;

        // set up archive icon
        $this->objIcon->title = $lblArchive;
        $this->objIcon->setIcon('folder');
        $this->objLink = new link($this->uri(array(
            'action' => 'archive',
            'id' => $id,
        )));
        $this->objLink->link = $this->objIcon->show();
        $this->objLink->extra = 'onclick="javascript:if(!confirm(\''.$lblArchiveConfirm.'\')){return false;};"';
        $icoArchive = $this->objLink->show();
        $icon .= ' '.$icoArchive;

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'].$icon;
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        if($status === '1'){
            $this->objMsg->setMessage($lblTrue);
            $this->objMsg->setTimeOut(10000);
            $content .= '<p>'.$this->objMsg->show().'</p>';
        }elseif($status === '0'){
            $this->objMsg->setMessage($lblFalse);
            $this->objMsg->setTimeOut(10000);
            $content .= '<p>'.$this->objMsg->show().'</p>';
        }

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "folderList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';

        $this->objTable->startRow();
        $this->objTable->addCell($lblStudentNo, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblName, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblStatus ,'', '', '', 'tuts-header', '');
        if($tutorial['tutorial_type'] == 1){
            $this->objTable->addCell($lblMarking ,'', '', '', 'tuts-header', '');
            $this->objTable->addCell($lblMarked ,'', '', '', 'tuts-header', '');
        }
        $this->objTable->addCell($lblMark ,'', '', '', 'tuts-header', '');
        $this->objTable->addCell($lblLate, '', '', '', 'tuts-header', '');
        $this->objTable->addCell('' ,'', '', '', 'tuts-header', '');
        $this->objTable->endRow();

        if($students == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', 'colspan="9"');
            $this->objTable->endRow();
        }else{
            foreach($students as $student){
                $result = $this->objDbTutorials->getResult($id, $student['userid']);
                $late = $this->objDbTutorials->getLate($id, $student['userid']);
                $markedBy = $this->objDbTutorials->countCompletedMarked($id, $student['userid']);
                $markedFor = $this->objDbTutorials->getMarkedStudents($id, $student['userid']);
                $lecturer = $this->objDbTutorials->checkLecturerMarked($id, $student['userid']);

                if($late != FALSE){
                    $answerOpen = $this->objDatetime->formatDate($late['answer_open']);
                    $answerClose = $this->objDatetime->formatDate($late['answer_close']);
                    $lateText = '<b>'.$lblOpen.':</b> '.$answerOpen;
                    $lateText .= '<br /><b>'.$lblClose.':</b> '.$answerClose;
                }else{
                    $lateText = '';
                }

                // set up marking link
                $this->objIcon->title = $lblLate;
                $this->objIcon->extra = '';
                $lnkLate = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'late',
                    'id' => $id,
                    'studentId' => $student['userid'],
                )), 'clock');

                $this->objIcon->title = $lblMarkTut;
                $this->objIcon->extra = '';
                $lnkMark = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'mark',
                    'id' => $id,
                    'studentId' => $student['userid'],
                )), 'greentick');

                if($result == FALSE){
                    $lnkMark = '';
                    $mark = '0%';
                    $status = '<font class="error"><b>'.$lblNotStarted.'</b></font>';
                }else{
                    if($result['has_submitted'] == 0){
                        $lnkMark = '';
                        $status = '<font class="warning">'.$lblNotSubmitted.'</font>';
                        $mark = '0%';
                    }else{
                        $status = $lblSubmitted;
                        if($result['mark_obtained'] != NULL){
                            $mark = round(($result['mark_obtained'] / $tutorial['total_mark']) * 100, 0).'%';
                            if($tutorial['tutorial_type'] == 1){
                                if($markedBy < 3 and $lecturer == FALSE){
                                    $mark = '<font class="error"><b>'.$mark.'</b></font>';
                                }else{
                                    $mark = round(($result['mark_obtained'] / $tutorial['total_mark']) * 100, 0);
                                    $late = $this->objDbTutorials->getLate($id, $student['userid']);
                                     if($late == FALSE){
                                        $penalty = ($tutorial['penalty'] * (3 - $markedFor));
                                        $mark = $mark - round($mark * ($penalty / 100), 0);
                                    }
                                    $mark = $mark.'%';
                                }
                            }
                        }else{
                            $mark = '<font class="error"><b>'.$lblNotMarked.'</b></font>';
                        }
                    }
                }

                $this->objTable->startRow();
                $this->objTable->addCell($student['userid'], '', '', '', '', '');
                $this->objTable->addCell($student['firstname'], '', '', '', '', '');
                $this->objTable->addCell($student['surname'], '', '', '', '', '');
                $this->objTable->addCell($status, '', '', '', '', '');
                if($tutorial['tutorial_type'] == 1){
                    $array = array();
                    $array['count'] = $markedFor;
                    $lblMarking = $this->objLanguage->code2Txt('mod_tutorials_marking', 'tutorials', $array);
                    if($markedFor < 3){
                        $lblMarking = '<font class="error">'.$lblMarking.'</font>';
                    }
                    $lblMarked = '';
                    if($lecturer != FALSE){
                        $lblMarked = '<font class="warning">'.$lblCompleted.'</font><br />';
                    }
                    $array = array();
                    $array['count'] = $markedBy;
                    $marked = $this->objLanguage->code2Txt('mod_tutorials_marked', 'tutorials', $array);
                    if($markedBy < 3 and $lecturer == FALSE){
                        $lblMarked = '<font class="error">'.$marked.'</font>';
                    }else{
                        $lblMarked .= $marked;
                    }

                    $this->objTable->addCell($lblMarking, '', '', '', '', '');
                    $this->objTable->addCell($lblMarked, '', '', '', '', '');
                }
                $this->objTable->addCell($mark, '', '', 'right', '', '');
                $this->objTable->addCell($lateText, '', '', 'center', '', '');
                $this->objTable->addCell($lnkLate.' '.$lnkMark, '', '', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
        $content .= $tblDisplay;

        $this->objLink = new link($this->uri(array(),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to show the marking page
    *
    * @param string $id: The id of the tutorial
    * @param string $studentId: The id of the student being marked
    * @param string $order: The question to be marked
    * @param string $e: The type of error if applicable
    * @param string $c: The comment text
    * @param string $m: The mark given
    * @param bool $isStudent: TRUE if the user is a student | FALSE if not
    * @return string $content: The template output string
    */
    public function showMarking($id, $studentId, $order, $e, $c, $m, $isStudent = FALSE)
    {
        // add highlight labels
        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $questions = $this->objDbTutorials->getQuestions($id);
        $question = $questions[$order - 1];
        $answer = $this->objDbTutorials->getAnswer($question['id'], $studentId);
        $name = $this->objUser->fullname($studentId);
        $marking = $this->objDbTutorials->getUsersMarkingForAnswer($answer['id'], $this->userId);
        if($marking == FALSE){
            $comment = '';
            $mark = '';
        }else{
            $comment = $marking['markers_comment'];
            $mark = $marking['mark'];
        }
        if($e == TRUE){
            $comment = $c;
            $mark = $m;
        }

        // set up language elements
        $lblHeading = $this->objLanguage->languageText('mod_tutorials_marktut', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblMarkAllocated = $this->objLanguage->languageText('phrase_allocatedmark');
        $lblMark = $this->objLanguage->languageText('word_mark');
        $lblMarking = $this->objLanguage->languageText('word_marking');
        $lblNext = $this->objLanguage->languageText('word_next');
        $lblPrevious = $this->objLanguage->languageText('word_previous');
        $lblSubmit = $this->objLanguage->languageText('phrase_submitmarking');
        $lblExit = $this->objLanguage->languageText('phrase_saveandexit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblAnswer = $this->objLanguage->languageText('word_answer');
        $lblOf = $this->objLanguage->languageText('word_of');
        $lblModel = $this->objLanguage->languageText('phrase_modelanswer');
        $lblStudent = $this->objLanguage->code2Txt('word_student');
        $lblStudentNo = $this->objLanguage->languageText('mod_tutorials_studentno', 'tutorials');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblComment = $this->objLanguage->languageText('word_comment');
        $lblNumeric = $this->objLanguage->languageText('mod_tutorials_marknumeric', 'tutorials');
        $lblMarkRequired = $this->objLanguage->languageText('mod_tutorials_markrequired', 'tutorials');
        $lblCommentRequired = $this->objLanguage->languageText('mod_tutorials_commentrequired', 'tutorials');
        $array = array();
        $array['mark'] = $question['question_value'];
        $lblRange = $this->objLanguage->code2Txt('mod_tutorials_markrrange', 'tutorials', $array);
        $lblConfirm = $this->objLanguage->languageText('phrase_submissionconfirmed');
        $lblConfirmSubmission = $this->objLanguage->languageText('mod_tutorials_confirmsubmission', 'tutorials');

        if($e == TRUE){
            $body = 'alert("'.$lblCommentRequired.'")';
            $this->appendArrayVar('bodyOnLoad', $body);
        }

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $string = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $string .= $header;

        if($isStudent == FALSE){
            // set up page heading
            $this->objHeading = new htmlHeading();
            $this->objHeading->str = ucfirst($lblStudent).': '.$name;
            $this->objHeading->type = 3;
            $header = $this->objHeading->show();
            $string .= $header;

            // set up page heading
            $this->objHeading = new htmlHeading();
            $this->objHeading->str = $lblStudentNo.': '.$studentId;
            $this->objHeading->type = 3;
            $header = $this->objHeading->show();
            $string .= $header;
        }

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblQuestion.':</b><br />'.$question['question'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblMarkAllocated.':</b><br />'.$question['question_value'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblModel.':</b><br />'.$question['model_answer'], '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // tabbed box
        $label = '<b>'.$lblQuestion.'  '.$order.'</b>';
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel($label);
        $this->objTabbedbox->addBoxContent($tblDisplay);
        $string .= $this->objTabbedbox->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblAnswer.':</b><br />'.$answer['answer'], '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // tabbed box
        $label = '<b>'.$lblAnswer.'</b>';
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel($label);
        $this->objTabbedbox->addBoxContent($tblDisplay);
        $string .= $this->objTabbedbox->show();

        // set up form elements
        $this->objInput = new textinput('inpSubmit', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        $this->objEditor->init('comment', $comment, '300px', '100%', NULL);
        $this->objEditor->setDefaultToolBarSetWithoutSave();
        $edtComment = $this->objEditor->show();

        $this->objDrop = new dropdown('mark');
        for($i = 0; $i <= $question['question_value']; $i++){
            $this->objDrop->addOption($i, $i.'&#160;');
        }
        $this->objDrop->setSelected($mark);
        $drpMark = $this->objDrop->show();

        $this->objButton=new button('next',$lblNext);
        $this->objButton->setToSubmit();
        $btnNext = $this->objButton->show();

        $this->objButton=new button('previous',$lblPrevious);
        $this->objButton->setToSubmit();
        $btnPrevious = $this->objButton->show();

        $this->objButton=new button('submitbutton',$lblSubmit);
        $this->objButton->extra = 'onclick="if($(\'input_inpConfirm\').checked){$(\'input_inpSubmit\').value=\'submit\';$(\'form_frmMark\').submit();}else{alert(\''.$lblConfirmSubmission.'\');$(\'input_inpConfirm\').focus();return false;}"';
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('exit',$lblExit);
        $this->objButton->setToSubmit();
        $btnExit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
        $btnCancel = $this->objButton->show();

        $this->objCheck = new checkbox('inpConfirm');
        $this->objCheck->setValue('yes');
        $chkConfirm = $this->objCheck->show();

        $this->objLabel = new label($lblConfirm, 'input_inpConfirm');
        $lblCheck = $this->objLabel->show();

        if(count($questions) == 1){
            $buttons = $btnSubmit.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }elseif($order == 1){
            $buttons = $btnNext.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }elseif($order == count($questions)){
            $buttons = $btnPrevious.'&#160;'.$btnSubmit.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }else{
            $buttons = $btnNext.'&#160;'.$btnPrevious.'&#160;'.$btnExit.'&#160;'.$btnCancel;
        }

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblMark.':</b><br />'.$drpMark, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblComment.':</b><br />'.$edtComment, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = '';
        $this->objTable->startRow();
        $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        if($order == count($questions)){
            $this->objTable->startRow();
            $this->objTable->addCell($chkConfirm.'&#160;'.$lblCheck, '', '', '', '', 'colspan="2"');
            $this->objTable->endRow();
        }
        $this->objTable->startRow();
        $this->objTable->addCell($buttons, '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // set up forms
        $this->objForm=new form('frmMark',$this->uri(array(
            'action' => 'savemarking',
            'id' => $id,
            'aId' => $answer['id'],
            'sId' => $studentId,
            'order' => $order,
        ), 'tutorials'));
        $this->objForm->addToForm($inpHidden.$tblDisplay);
        $tabContent = $this->objForm->show();

        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'liststudents',
            'id' => $id,
        ), 'tutorials'));
        $tabContent .= $this->objForm->show();

        // tabbed box
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel('<b>'.$lblMarking.'</b>');
        $this->objTabbedbox->addBoxContent($tabContent);
        $string .= $this->objTabbedbox->show();

        $string .= $this->_showMarkerLinks($id, $studentId);

        $this->objLayer = new layer();
        $this->objLayer->padding = '10px';
        $this->objLayer->addToStr($string);
        $content = $this->objLayer->show();

        return $content;
    }

    /**
    * Method to show the student view page
    *
    * @param string $id: The id of the tutorial
    * @return string $content: The template output string
    */
    public function showStudentView($id, $order, $e = FALSE)    {
        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $questions = $this->objDbTutorials->getQuestions($id);
        $question = $questions[$order - 1];
        $answer = $this->objDbTutorials->getAnswer($question['id'], $this->userId);
        $marking = $this->objDbTutorials->getMarkingForStudentAnswer($answer['id'], $this->userId);
        $result = $this->objDbTutorials->getResult($id, $this->userId);
        $status = $this->tutStatus($id, TRUE);
        $lecturer = $this->objDbTutorials->checkLecturerMarked($id, $this->userId);
        $markedBy = $this->objDbTutorials->countCompletedMarked($id, $this->userId);
        $modComplete = $this->objDbTutorials->moderationComplete($id);
        $late = $this->objDbTutorials->getLate($id, $this->userId);
        $markedFor = $this->objDbTutorials->getMarkedStudents($id, $this->userId);

        // set up language elements
        $lblView = $this->objLanguage->languageText('mod_tutorials_view', 'tutorials');
        $lblMarkObtained = $this->objLanguage->languageText('phrase_markobtained');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnhome', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblMarkAllocated = $this->objLanguage->languageText('phrase_allocatedmark');
        $lblMark = $this->objLanguage->languageText('word_mark');
        $lblMarking = $this->objLanguage->languageText('word_marking');
        $lblAnswer = $this->objLanguage->languageText('word_answer');
        $lblOf = $this->objLanguage->languageText('word_of');
        $lblModel = $this->objLanguage->languageText('phrase_modelanswer');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblComment = $this->objLanguage->languageText('phrase_markerscomment');
        $lblStudent = $this->objLanguage->code2Txt('word_student');
        $lblRequest = $this->objLanguage->languageText('mod_tutorials_request', 'tutorials');
        $lblReason = $this->objLanguage->languageText('mod_tutorials_reason', 'tutorials');
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblSubmitted = $this->objLanguage->languageText('mod_tutorials_modsubmitted', 'tutorials');
        $lblModerator = $this->objLanguage->languageText('word_moderator');
        $lblModComment = $this->objLanguage->languageText('phrase_moderatorscomment');
        $lblNotReady = $this->objLanguage->languageText('mod_tutorials_notready', 'tutorials');
        $lblPenalty = $this->objLanguage->languageText('word_penalty');
        $lblFinal = $this->objLanguage->languageText('word_final');

        if($e == TRUE){
            $body = 'alert("'.$lblReason.'")';
            $this->appendArrayVar('bodyOnLoad', $body);
        }

        if($status['value'] < 6){
            $mark = '<font class="error">'.$lblNotReady.'</font>';
        }else{
            if($lecturer == TRUE or $markedBy == 3){
                $mark = round(($result['mark_obtained'] / $tutorial['total_mark']) * 100, 0);
            }else{
                $mark = '0';
            }
        }

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblView;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblMarkObtained.': '.$mark.'%';
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        if($tutorial['tutorial_type'] == 1 and $tutorial['penalty'] > 0 and $late == FALSE and $markedFor < 3){
            // set up page heading
            $penalty = ($tutorial['penalty'] * (3 - $markedFor));
            $penalty = round($mark * ($penalty / 100), 0);
            $this->objHeading = new htmlHeading();
            $this->objHeading->str = $lblPenalty.': '.$penalty.'%';
            $this->objHeading->type = 3;
            $header = $this->objHeading->show();
            $content .= $header;

            // set up page heading
            $mark = $mark - $penalty;
            $this->objHeading = new htmlHeading();
            $this->objHeading->str = $lblFinal.' '.strtolower($lblMark).': '.$mark.'%';
            $this->objHeading->type = 3;
            $header = $this->objHeading->show();
            $content .= $header;
        }

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblQuestion.':</b><br />'.$question['question'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblMarkAllocated.':</b><br />'.$question['question_value'], '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblModel.':</b><br />'.$question['model_answer'], '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // tabbed box
        $label = '<b>'.$lblQuestion.'  '.$order.'  '.strtolower($lblOf).'  '.count($questions).'</b>';
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel($label);
        $this->objTabbedbox->addBoxContent($tblDisplay);
        $content .= $this->objTabbedbox->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblAnswer.':</b><br />'.$answer['answer'], '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // tabbed box
        $label = '<b>'.$lblAnswer.'</b>';
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel($label);
        $this->objTabbedbox->addBoxContent($tblDisplay);
        $content .= $this->objTabbedbox->show();

        if($tutorial['tutorial_type'] == 0){
            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblComment.':</b><br />'.$marking[0]['markers_comment'], '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblMark.':</b><br />'.$marking[0]['mark'], '', '', '', '', '');
            $this->objTable->endRow();
            $display = $this->objTable->show();
        }else{
            $display = '';
            foreach($marking as $key => $line){
                // set up display table
                $this->objTable = new htmltable();
                $this->objTable->cellspacing = '2';
                $this->objTable->cellpadding = '5';
                $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

                $this->objTable->startRow();
                $this->objTable->addCell('<b>'.$lblMark.':</b><br />'.$line['mark'], '', '', '', '', '');
                $this->objTable->endRow();
                $this->objTable->startRow();
                if($line['is_moderator'] == 0){
                    $this->objTable->addCell('<b>'.$lblComment.':</b><br />'.$line['markers_comment'], '', '', '', '', '');
                }else{
                    $this->objTable->addCell('<b>'.$lblModComment.':</b><br />'.$line['markers_comment'], '', '', '', '', '');

                }
                $this->objTable->endRow();
                $tblDisplay = $this->objTable->show();

                // tabbed box
                $this->objTabbedbox=new tabbedbox();
                if($line['is_moderator'] == 0){
                    $this->objTabbedbox->addTabLabel('<b>'.ucfirst($lblStudent).' '.($key + 1).'</b>');
                }else{
                    $this->objTabbedbox->addTabLabel('<b>'.$lblModerator.'</b>');
                }
                $this->objTabbedbox->addBoxContent($tblDisplay);
                $tabDisplay = $this->objTabbedbox->show();

                if(($lecturer == FALSE and $line['is_moderator'] == 0) or $line['is_moderator'] == 1){
                    $this->objLayer = new layer();
                    $this->objLayer->padding = '10px';
                    if($line['is_moderator'] == 0){
                        $this->objLayer->background_color = '';
                    }else{
                        $this->objLayer->background_color = 'rgb(100,255,100)';
                    }
                    $this->objLayer->addToStr($tabDisplay);
                    $display .= $this->objLayer->show();
                }
            }
        }

        // tabbed box
        $this->objTabbedbox=new tabbedbox();
        $this->objTabbedbox->addTabLabel('<b>'.$lblMarking.'</b>');
        $this->objTabbedbox->addBoxContent($display);
        $content .= $this->objTabbedbox->show();

        if($status['value'] == 6 and empty($answer['moderation_reason'])){
            if($lecturer == FALSE){
                // set up htmlelements
                $this->objEditor->init('reason', '', '300px', '60%', NULL);
                $this->objEditor->setDefaultToolBarSetWithoutSave();
                $edtReason = $this->objEditor->show();

                $this->objButton=new button('submit',$lblSubmit);
                $this->objButton->setToSubmit();
                $btnSubmit = $this->objButton->show();

                $this->objButton=new button('cancel',$lblCancel);
                $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
                $btnCancel = $this->objButton->show();

                // set up display table
                $this->objTable = new htmltable();
                $this->objTable->cellspacing = '2';
                $this->objTable->cellpadding = '5';
                $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

                $this->objTable->startRow();
                $this->objTable->addCell('<b>'.$lblReason.'</b><br />'.$edtReason, '', '', '', '', '');
                $this->objTable->endRow();
                $this->objTable->row_attributes = '';
                $this->objTable->startRow();
                $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
                $this->objTable->endRow();
                $this->objTable->startRow();
                $this->objTable->addCell($btnSubmit.' '.$btnCancel, '', '', '', '', 'colspan="2"');
                $this->objTable->endRow();
                $tblDisplay = $this->objTable->show();

                // set up forms
                $this->objForm=new form('frmAnswer',$this->uri(array(
                    'action' => 'saverequest',
                    'id' => $id,
                    'aId' => $answer['id'],
                    'order' => $order,
                ), 'tutorials'));
                $this->objForm->addToForm($tblDisplay);
                $tabContent = $this->objForm->show();

                $this->objForm=new form('frmCancel',$this->uri(array(
                    'action' => 'view',
                    'id' => $id,
                    'order' => $order,
                ), 'tutorials'));
                $tabContent .= $this->objForm->show();

                $this->objTabbedbox=new tabbedbox();
                $this->objTabbedbox->addTabLabel('<b>'.$lblRequest.'</b>');
                $this->objTabbedbox->addBoxContent($tabContent);
                $content .= $this->objTabbedbox->show();
            }
        }elseif($status['value'] >= 6 and !empty($answer['moderation_reason'])){
            if($answer['moderation_complete'] != 1){
                // set up display table
                $this->objTable = new htmltable();
                $this->objTable->cellspacing = '2';
                $this->objTable->cellpadding = '5';
                $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

                $this->objTable->startRow();
                $this->objTable->addCell('<b>'.$lblSubmitted.':</b>', '', '', '', 'noRecordsMessage', '');
                $this->objTable->endRow();
                $this->objTable->row_attributes = '';
                $tblDisplay = $this->objTable->show();

                $this->objTabbedbox=new tabbedbox();
                $this->objTabbedbox->addTabLabel('<b>'.$lblRequest.'</b>');
                $this->objTabbedbox->addBoxContent($tblDisplay);
                $content .= $this->objTabbedbox->show();
            }
        }

        $content .= $this->_showStudentLinks($id, 'view');

        $this->objLink = new link($this->uri(array(),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br /><br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to show the late submissions page
    *
    * @param string $id: The id of the tutorial
    * @param string $studentId: The id of the student
    * @return string $content: The template output string
    */
    public function showLate($id, $studentId, $mode)
    {
        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $late = $this->objDbTutorials->getLate($id, $studentId);
        $name = $this->objUser->fullname($studentId);
        $result = $this->objDbTutorials->getResult($id, $studentId);

        if($late != FALSE){
            $answerOpen = $late['answer_open'];
            $answerClose = $late['answer_close'];
        }else{
            $answerOpen = date('Y-m-d H:i');
            $answerClose = date('Y-m-d H:i');
        }

        // set up language elements
        $lblLate = $this->objLanguage->languageText('mod_tutorials_late', 'tutorials');
        $lblStudent = $this->objLanguage->code2Txt('word_student');
        $lblStudentNo = $this->objLanguage->languageText('mod_tutorials_studentno', 'tutorials');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblAnswerOpen = $this->objLanguage->languageText('mod_tutorials_latestart', 'tutorials');
        $lblAnswerClose = $this->objLanguage->languageText('mod_tutorials_lateclose', 'tutorials');
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblSelect = $this->objLanguage->languageText('phrase_selectdate');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnlist', 'tutorials');
        $lblConfirm = $this->objLanguage->languageText('mod_tutorials_deleteconfim', 'tutorials');
        $lblAdd = $this->objLanguage->languageText('mod_tutorials_addlate', 'tutorials');
        $lblEdit = $this->objLanguage->languageText('mod_tutorials_editlate', 'tutorials');
        $lblNoRecords = $this->objLanguage->languageText('mod_tutorials_norecords', 'tutorials');

        $icons = '';
        if($late != FALSE){
            if($mode == 'edit'){
                $icons = '';
            }else{
                if($result == FALSE){
                    // set up edit icon
                    $this->objIcon->title=$lblEdit;
                    $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
                        'action' => 'late',
                        'id' => $id,
                        'studentId' => $studentId,
                        'mode' => 'edit',
                    ), 'tutorials'));
                    $icons = $icoEdit;

                    // set up delete icon
                    $deleteArray = array(
                        'action' => 'deletelate',
                        'tutId' => $id,
                        'id' => $late['id'],
                    );
                    $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'tutorials', $lblConfirm);
                    $icons .= ' '.$icoDelete;
                }
            }
        }else{
            if($mode == 'add'){
                $icons = '';
            }else{
                // set up add icon
                $this->objIcon->title = $lblAdd;
                $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
                    'action' => 'late',
                    'id' => $id,
                    'studentId' => $studentId,
                    'mode' => 'add',
                ), 'tutorials'));
                $icons = $icoAdd;
            }
        }

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblLate.' '.$icons;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = ucfirst($lblStudent).': '.$name;
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblStudentNo.': '.$studentId;
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        if($late == FALSE and $mode != 'add'){
            // display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', 'left', 'noRecordsMessage', '');
            $this->objTable->endRow();

            $content .= $this->objTable->show();
        }elseif(!empty($mode)){// set up htmlelements
            $inpAnswerOpen = $this->objPopupcal->show('answerOpen', 'yes', 'no', $answerOpen);
            $inpAnswerClose = $this->objPopupcal->show('answerClose', 'yes', 'no', $answerClose);

            $this->objButton=new button('submit',$lblSubmit);
            $this->objButton->setToSubmit();
            $btnSubmit = $this->objButton->show();

            $this->objButton=new button('cancel',$lblCancel);
            $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
            $btnCancel = $this->objButton->show();

            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblAnswerOpen.'&#160:</b>', '33%', '', '', '', '');
            $this->objTable->addCell($inpAnswerOpen, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblAnswerClose.'&#160:</b>', '33%', '', '', '', '');
            $this->objTable->addCell($inpAnswerClose, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->row_attributes = '';
            $this->objTable->startRow();
            $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell($btnSubmit.'&#160;'.$btnCancel, '', '', '', '', 'colspan="2"');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // set up forms
            $this->objForm=new form('frmLate',$this->uri(array(
                'action'=>'savelate',
                'id' => $id,
                'studentId' => $studentId,
            ), 'tutorials'));
            $this->objForm->addToForm($tblDisplay);
            $content .= $this->objForm->show();

            $this->objForm=new form('frmCancel',$this->uri(array(
                'action' => 'liststudents',
                'id' => $id,
            ), 'tutorials'));
            $content .= $this->objForm->show();
        }else{
            $answerOpen = $this->objDatetime->formatDate($late['answer_open']);
            $answerClose = $this->objDatetime->formatDate($late['answer_close']);

            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblAnswerOpen.'&#160:</b>', '33%', '', '', '', '');
            $this->objTable->addCell($answerOpen, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblAnswerClose.'&#160:</b>', '33%', '', '', '', '');
            $this->objTable->addCell($answerClose, '', '', '', '', '');
            $this->objTable->endRow();
            $content .= $this->objTable->show();
        }

        $this->objLink = new link($this->uri(array(
            'action' => 'liststudents',
            'id' => $id,
        ),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to show the import questions page
    *
    * @access public
    * @param string $id: The id of the tutorial
    * @return string $content: The template output string
    */
    public function showImport($id)
    {
        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);

        // set up language elements
        $lblImport = $this->objLanguage->languageText('phrase_importquestions');
        $lblOverwrite = $this->objLanguage->languageText('mod_tutorials_overwrite', 'tutorials');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblYes = $this->objLanguage->languageText('word_yes');
        $lblNo = $this->objLanguage->languageText('word_no');
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnview', 'tutorials');

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblImport;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        // set up htmlelements
        $this->objRadio = new radio('overwrite');
        $this->objRadio->addOption('1', $lblYes);
        $this->objRadio->addOption('2', $lblNo);
        $this->objRadio->setSelected('1');
        $this->objRadio->setBreakSpace('<br />');
        $radOverwrite = $this->objRadio->show();

        $this->objInput = new textinput('file', '', 'file', '70');
        $inpImport = $this->objInput->show();

        $this->objButton=new button('submit',$lblSubmit);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
        $btnCancel = $this->objButton->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblOverwrite.'</b>', '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell($radOverwrite, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($inpImport, '', '', '', '', '');
        $this->objTable->endRow();

        $this->objTable->row_attributes = '';
        $this->objTable->startRow();
        $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($btnSubmit.'&#160;'.$btnCancel, '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // set up forms
        $this->objForm=new form('frmImport',$this->uri(array(
            'action'=>'saveimport',
            'id' => $id,
        ), 'tutorials'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->extra = 'enctype="multipart/form-data"';
        $content .= $this->objForm->show();

        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'view',
            'id' => $id,
        ), 'tutorials'));
        $content .= $this->objForm->show();

        $this->objLink = new link($this->uri(array(
           'action' => 'view',
            'id' => $id,
        ),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to do the actual question import
    *
    * @access public
    * @param string $id: The id of the tutorial
    * @param array $file: The array of file data
    * @param string $overwrite: indicator if the questions must be overwritten
    */
    public function doImport($id, $file, $overwrite)
    {
        if($overwrite == 1){
            $this->objDbTutorials->deleteTutorialQuestions($id);
        }
        $fp = fopen($file['file']['tmp_name'], 'r');
        while($line = fgetcsv($fp, 1024, ","))
        {
            $array[] = $line;
        }
        fclose($fp);
        if(!empty($array)){
            foreach($array as $key => $line){
                $questionId = $this->objDbTutorials->addQuestion($id, $line[0], $line[1], $line[2]);
            }
        }
        unlink($file['file']['tmp_name']);
    }

    /**
    * Method to show the moderation page
    *
    * @param string $id: The id of the tutorial
    * @param string $e: The type of error if applicable
    * @param string $c: The comment text
    * @param string $m: The mark given
    * @return string $content: The template output string
    */
    public function showModerate($id, $e, $c, $m)
    {
        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $moderation = $this->objDbTutorials->getModerationRequests($id);
        $name = $this->objUser->fullname($moderation[0]['student_id']);
        $studentId = $moderation[0]['student_id'];
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $question = $this->objDbTutorials->getQuestionById($moderation[0]['question_id']);
        $answer = $moderation[0];
        $marking = $this->objDbTutorials->getMarkingForStudentAnswer($moderation[0]['id'], $studentId);
        if($e == TRUE){
            $comment = $c;
            $mark = $m;
        }else{
            $comment = '';
            $mark = '';
        }

        // set up language elements
        $lblModerate = $this->objLanguage->languageText('phrase_moderatetutorial');
        $lblStudent = $this->objLanguage->code2Txt('word_student');
        $lblStudentNo = $this->objLanguage->languageText('mod_tutorials_studentno', 'tutorials');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblLeft = $this->objLanguage->languageText('mod_tutorials_left', 'tutorials');
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnhome', 'tutorials');
        $lblNoRecords = $this->objLanguage->languageText('mod_tutorials_norecords', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblMarkAllocated = $this->objLanguage->languageText('phrase_allocatedmark');
        $lblMark = $this->objLanguage->languageText('word_mark');
        $lblMarking = $this->objLanguage->languageText('word_marking');
        $lblAnswer = $this->objLanguage->languageText('word_answer');
        $lblModel = $this->objLanguage->languageText('phrase_modelanswer');
        $lblComment = $this->objLanguage->languageText('phrase_markerscomment');
        $lblModeration = $this->objLanguage->languageText('word_moderation');
        $lblModComment = $this->objLanguage->languageText('phrase_moderatorscomment');
        $lblModMark = $this->objLanguage->languageText('phrase_moderatorsmark');
        $lblNumeric = $this->objLanguage->languageText('mod_tutorials_marknumeric', 'tutorials');
        $lblMarkRequired = $this->objLanguage->languageText('mod_tutorials_markrequired', 'tutorials');
        $lblCommentRequired = $this->objLanguage->languageText('mod_tutorials_commentrequired', 'tutorials');
        $array = array();
        $array['mark'] = $question['question_value'];
        $lblRange = $this->objLanguage->code2Txt('mod_tutorials_markrrange', 'tutorials', $array);
        $lblReason = $this->objLanguage->languageText('mod_tutorials_modreason', 'tutorials');

        if($e == TRUE){
            $body = 'alert("'.$lblCommentRequired.'");document.getElementById("comment___Frame").focus();';
            $this->appendArrayVar('bodyOnLoad', $body);
        }

        // set up page heading
        if($moderation != FALSE){
            $heading = $lblModerate.' - '.$lblLeft.': '.count($moderation);
        }else{
            $heading = $lblModerate;
        }
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $heading;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        if($moderation != FALSE){
            // set up page heading
            $this->objHeading = new htmlHeading();
            $this->objHeading->str = ucfirst($lblStudent).': '.$name;
            $this->objHeading->type = 3;
            $header = $this->objHeading->show();
            $content .= $header;

            // set up page heading
            $this->objHeading = new htmlHeading();
            $this->objHeading->str = $lblStudentNo.': '.$studentId;
            $this->objHeading->type = 3;
            $header = $this->objHeading->show();
            $content .= $header;
        }

        if($moderation == FALSE){
            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', '');
            $this->objTable->endRow();
            $content .= $this->objTable->show();


        }else{
            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblQuestion.':</b><br />'.$question['question'], '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblMarkAllocated.':</b><br />'.$question['question_value'], '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblModel.':</b><br />'.$question['model_answer'], '', '', '', '', '');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // tabbed box
            $label = '<b>'.$lblQuestion.'  '.$question['question_order'].'</b>';
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel($label);
            $this->objTabbedbox->addBoxContent($tblDisplay);
            $content .= $this->objTabbedbox->show();

            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblAnswer.':</b><br />'.$answer['answer'], '', '', '', '', '');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // tabbed box
            $label = '<b>'.$lblStudent.'&#160;'.strtolower($lblAnswer).'</b>';
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel($label);
            $this->objTabbedbox->addBoxContent($tblDisplay);
            $content .= $this->objTabbedbox->show();

            $display = '';
            $students = '';
            $marks = '';
            foreach($marking as $key => $line){
                $name = $this->objUser->fullName($line['marker_id']);
                $students .= $line['marker_id'];
                $marks .= $line['mark'];
                if(count($marking) != ($key + 1)){
                    $students .= '|';
                    $marks .= '|';
                }
                // set up display table
                $this->objTable = new htmltable();
                $this->objTable->cellspacing = '2';
                $this->objTable->cellpadding = '5';
                $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

                $this->objTable->startRow();
                $this->objTable->addCell('<b>'.$lblComment.':</b><br />'.$line['markers_comment'], '', '', '', '', '');
                $this->objTable->endRow();
                $this->objTable->startRow();
                $this->objTable->addCell('<b>'.$lblMark.':</b><br />'.$line['mark'], '', '', '', '', '');
                $this->objTable->endRow();
                $tblDisplay = $this->objTable->show();

                // tabbed box
                $this->objTabbedbox=new tabbedbox();
                $this->objTabbedbox->addTabLabel('<b>'.ucfirst($lblStudent).' '.($key + 1).'</b><br />'.$name.'<br />'.$line['marker_id']);
                $this->objTabbedbox->addBoxContent($tblDisplay);
                $tabDisplay = $this->objTabbedbox->show();

                $this->objLayer = new layer();
                $this->objLayer->padding = '10px';
                $this->objLayer->addToStr($tabDisplay);
                $display .= $this->objLayer->show();
            }

            // tabbed box
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel('<b>'.$lblMarking.'</b>');
            $this->objTabbedbox->addBoxContent($display);
            $content .= $this->objTabbedbox->show();

            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '2';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'none\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell($answer['moderationReason'], '', '', '', '', '');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // tabbed box
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel('<b>'.$lblReason.'</b>');
            $this->objTabbedbox->addBoxContent($tblDisplay);
            $content .= $this->objTabbedbox->show();

            // set up form elements
            $this->objEditor->init('comment', $comment, '300px', '60%', NULL);
            $this->objEditor->setDefaultToolBarSetWithoutSave();
            $edtComment = $this->objEditor->show();

            $this->objDrop = new dropdown('mark');
            for($i = 0; $i <= $question['question_value']; $i++){
                $this->objDrop->addOption($i, $i.'&#160;');
            }
            $this->objDrop->setSelected($mark);
            $drpMark = $this->objDrop->show();

            $this->objButton=new button('submit',$lblSubmit);
            $this->objButton->setToSubmit();
            $btnSubmit = $this->objButton->show();

            $this->objButton=new button('cancel',$lblCancel);
            $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
            $btnCancel = $this->objButton->show();

            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblModMark.':</b><br />'.$drpMark, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblModComment.':</b><br />'.$edtComment, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->row_attributes = '';
            $this->objTable->startRow();
            $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell($btnSubmit.'&#160;'.$btnCancel, '', '', '', '', 'colspan="2"');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // set up forms
            $this->objForm = new form('frmMod',$this->uri(array(
                'action' => 'savemod',
                'id' => $id,
                'aId' => $answer['id'],
                'sId' => $studentId,
                'emailList' => $students,
                'marks' => $marks,
                'order' => $question['question_order'],
            ), 'tutorials'));
            $this->objForm->addToForm($tblDisplay);

            $tabContent = $this->objForm->show();
            $this->objForm=new form('frmCancel',$this->uri(array(), 'tutorials'));
            $tabContent .= $this->objForm->show();

            // tabbed box
            $this->objTabbedbox = new tabbedbox();
            $this->objTabbedbox->addTabLabel('<b>'.$lblModeration.'</b>');
            $this->objTabbedbox->addBoxContent($tabContent);
            $content .= $this->objTabbedbox->show();
        }

        $this->objLink = new link($this->uri(array(),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br /><br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to show the answers list page
    *
    * @param string $id: The id of the tutorial
    * @param string $order: The question order number
    * @param string $num: The start number of the answers
    * @return string $content: The template output string
    */
    public function showAnswerList($id, $order, $num)
    {
        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $questions = $this->objDbTutorials->getQuestions($id);
        $question = $questions[$order - 1];
        $answers = $this->objDbTutorials->getAnswers($question['id'], $num);

        // set up language elements
        $lblList = $this->objLanguage->languageText('mod_tutorials_answerlist', 'tutorials');
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblNext = $this->objLanguage->languageText('word_next');
        $lblPrevious = $this->objLanguage->languageText('word_previous');
        $lblFirst = $this->objLanguage->languageText('word_first');
        $lblLast = $this->objLanguage->languageText('word_last');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnlist', 'tutorials');
        $lblNoRecords = $this->objLanguage->languageText('mod_tutorials_norecords', 'tutorials');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblModel = $this->objLanguage->languageText('phrase_modelanswer');
        $lblMarkAllocated = $this->objLanguage->languageText('phrase_allocatedmark');
        $lblAnswers = $this->objLanguage->languageText('word_answers');
        $lblAnswer = $this->objLanguage->languageText('word_answer');
        $lblOf = $this->objLanguage->languageText('word_of');

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblList;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        if($questions == FALSE){
            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', '');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // tabbed box
            $label = '<b>'.$lblQuestion.'</b>';
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel($label);
            $this->objTabbedbox->addBoxContent($tblDisplay);
            $content .= $this->objTabbedbox->show();

            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', '');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // tabbed box
            $label = '<b>'.$lblAnswers.'</b>';
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel($label);
            $this->objTabbedbox->addBoxContent($tblDisplay);
            $content .= $this->objTabbedbox->show();
        }else{
            // set up navigation icons
            if($order == 1){
                // first
                $this->objIcon->title = $lblFirst;
                $this->objIcon->extra = '';
                $this->objIcon->setIcon('first_grey');
                $icons = $this->objIcon->show();

                // previous
                $this->objIcon->title = $lblPrevious;
                $this->objIcon->extra = '';
                $this->objIcon->setIcon('prev_grey');
                $icons .= '&#160;'.$this->objIcon->show();

                // next
                $this->objIcon->title = $lblNext;
                $this->objIcon->extra = '';
                $icoNext = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => ($order + 1),
                )), 'next');
                $icons .= '&#160;'.$icoNext;

                // last
                $this->objIcon->title = $lblLast;
                $this->objIcon->extra = '';
                $icoLast = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => count($questions),
                )), 'last');
                $icons .= '&#160;'.$icoLast;
            }elseif($order == count($questions)){
                // first
                $this->objIcon->title = $lblFirst;
                $this->objIcon->extra = '';
                $icoFirst = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => 1,
                )), 'first');
                $icons = '&#160;'.$icoFirst;

                // previous
                $this->objIcon->title = $lblPrevious;
                $this->objIcon->extra = '';
                $icoPrevious = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => ($order - 1),
                )), 'prev');
                $icons .= '&#160;'.$icoPrevious;

                // next
                $this->objIcon->title = $lblNext;
                $this->objIcon->extra = '';
                $this->objIcon->setIcon('next_grey');
                $icons .= $this->objIcon->show();

                // last
                $this->objIcon->title = $lblLast;
                $this->objIcon->extra = '';
                $this->objIcon->setIcon('last_grey');
                $icons .= '&#160;'.$this->objIcon->show();
            }else{
                // first
                $this->objIcon->title = $lblFirst;
                $this->objIcon->extra = '';
                $icoFirst = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => 1,
                )), 'first');
                $icons = '&#160;'.$icoFirst;

                // previous
                $this->objIcon->title = $lblPrevious;
                $this->objIcon->extra = '';
                $icoPrevious = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => ($order - 1),
                )), 'prev');
                $icons .= '&#160;'.$icoPrevious;

                // next
                $this->objIcon->title = $lblNext;
                $this->objIcon->extra = '';
                $icoNext = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => ($order + 1),
                )), 'next');
                $icons .= '&#160;'.$icoNext;

                // last
                $this->objIcon->title = $lblLast;
                $this->objIcon->extra = '';
                $icoLast = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'answerlist',
                    'id' => $id,
                    'order' => count($questions),
                )), 'last');
                $icons .= '&#160;'.$icoLast;
            }

            // set up display table
            $this->objTable = new htmltable();
            $this->objTable->cellspacing = '2';
            $this->objTable->cellpadding = '5';

            $this->objTable->startRow();
            $this->objTable->addCell($icons, '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblQuestion.':</b><br />'.$question['question'], '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblModel.':</b><br />'.$question['model_answer'], '', '', '', '', '');
            $this->objTable->endRow();
            $this->objTable->startRow();
            $this->objTable->addCell('<b>'.$lblMarkAllocated.':</b><br />'.$question['question_value'], '', '', '', '', '');
            $this->objTable->endRow();
            $tblDisplay = $this->objTable->show();

            // tabbed box
            $label = '<b>'.$lblQuestion.'&#160;'.$order.'&#160;'.strtolower($lblOf).'&#160;'.count($questions).'</b>';
            $this->objTabbedbox=new tabbedbox();
            $this->objTabbedbox->addTabLabel($label);
            $this->objTabbedbox->addBoxContent($tblDisplay);
            $content .= $this->objTabbedbox->show();

            if($answers == FALSE){
                // set up display table
                $this->objTable = new htmltable();
                $this->objTable->cellspacing = '2';
                $this->objTable->cellpadding = '5';
                $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

                $this->objTable->startRow();
                $this->objTable->addCell('<b>'.$lblNoRecords.'</b>', '', '', '', 'noRecordsMessage', '');
                $this->objTable->endRow();
                $tblDisplay = $this->objTable->show();

                // tabbed box
                $label = '<b>'.$lblAnswers.'</b>';
                $this->objTabbedbox=new tabbedbox();
                $this->objTabbedbox->addTabLabel($label);
                $this->objTabbedbox->addBoxContent($tblDisplay);
                $content .= $this->objTabbedbox->show();
            }else{
                // set up navigation icons
                $group = 10;
                $last = ((intval($answers[0]['count']/ $group) * $group) + 1);
                $next = ($num + $group);
                $prev = ($num - $group);
                if($answers[0]['count'] < $group){
                    // first
                    $this->objIcon->title = $lblFirst;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('first_grey');
                    $icons = $this->objIcon->show();

                    // previous
                    $this->objIcon->title = $lblPrevious;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('prev_grey');
                    $icons .= '&#160;'.$this->objIcon->show();

                    // next
                    $this->objIcon->title = $lblNext;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('next_grey');
                    $icons .= $this->objIcon->show();

                    // last
                    $this->objIcon->title = $lblLast;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('last_grey');
                    $icons .= '&#160;'.$this->objIcon->show();
                }elseif($num == 1){
                    // first
                    $this->objIcon->title = $lblFirst;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('first_grey');
                    $icons = $this->objIcon->show();

                    // previous
                    $this->objIcon->title = $lblPrevious;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('prev_grey');
                    $icons .= '&#160;'.$this->objIcon->show();

                    // next
                    $this->objIcon->title = $lblNext;
                    $this->objIcon->extra = '';
                    $icoNext = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' => $order,
                        'num' => $next,
                    )), 'next');
                    $icons .= '&#160;'.$icoNext;

                    // last
                    $this->objIcon->title = $lblLast;
                    $this->objIcon->extra = '';
                    $icoLast = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' => $order,
                        'num' => $last,
                    )), 'last');
                    $icons .= '&#160;'.$icoLast;
                }elseif($num == $last){
                    // first
                    $this->objIcon->title = $lblFirst;
                    $this->objIcon->extra = '';
                    $icoFirst = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' => $order,
                        'num' => 1,
                    )), 'first');
                    $icons = '&#160;'.$icoFirst;

                    // previous
                    $this->objIcon->title = $lblPrevious;
                    $this->objIcon->extra = '';
                    $icoPrevious = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' => $order,
                        'num' => $prev,
                    )), 'prev');
                    $icons .= '&#160;'.$icoPrevious;

                    // next
                    $this->objIcon->title = $lblNext;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('next_grey');
                    $icons .= $this->objIcon->show();

                    // last
                    $this->objIcon->title = $lblLast;
                    $this->objIcon->extra = '';
                    $this->objIcon->setIcon('last_grey');
                    $icons .= '&#160;'.$this->objIcon->show();
                }else{
                    // first
                    $this->objIcon->title = $lblFirst;
                    $this->objIcon->extra = '';
                    $icoFirst = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' => $order,
                        'num' => 1,
                    )), 'first');
                    $icons = '&#160;'.$icoFirst;

                    // previous
                    $this->objIcon->title = $lblPrevious;
                    $this->objIcon->extra = '';
                    $icoPrevious = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' => $order,
                        'num' => $prev,
                    )), 'prev');
                    $icons .= '&#160;'.$icoPrevious;

                    // next
                    $this->objIcon->title = $lblNext;
                    $this->objIcon->extra = '';
                    $icoNext = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' => $order,
                        'num' => $next,
                    )), 'next');
                    $icons .= '&#160;'.$icoNext;

                    // last
                    $this->objIcon->title = $lblLast;
                    $this->objIcon->extra = '';
                    $icoLast = $this->objIcon->getLinkedIcon($this->uri(array(
                        'action' => 'answerlist',
                        'id' => $id,
                        'order' =>$order,
                        'num' => $last,
                    )), 'last');
                    $icons .= '&#160;'.$icoLast;
                }

                // set up display table
                $string = $icons;
                foreach($answers as $key => $answer){
                    $this->objTable = new htmltable();
                    $this->objTable->cellspacing = '2';
                    $this->objTable->cellpadding = '5';
                    $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

                    $this->objTable->startRow();
                    $this->objTable->addCell($answer['answer'], '', '', '', '', '');
                    $this->objTable->endRow();
                    $tblDisplay = $this->objTable->show();

                    // tabbed box
                    $label = '<b>'.$lblAnswer.' '.($num + $key).'</b>';
                    $this->objTabbedbox=new tabbedbox();
                    $this->objTabbedbox->addTabLabel($label);
                    $this->objTabbedbox->addBoxContent($tblDisplay);
                    $tabDisplay = $this->objTabbedbox->show();

                    $this->objLayer = new layer();
                    $this->objLayer->padding = '10px';
                    $this->objLayer->addToStr($tabDisplay);
                    $string .= $this->objLayer->show();
                }

                // tabbed box
                $label = '<b>'.$lblAnswers.'</b>';
                $this->objTabbedbox=new tabbedbox();
                $this->objTabbedbox->addTabLabel($label);
                $this->objTabbedbox->addBoxContent($string);
                $content .= $this->objTabbedbox->show();
            }
        }

        $this->objLink = new link($this->uri(array(
            'action' => 'liststudents',
            'id' => $id,
        ),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br /><br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to show the export results page
    *
    * @access public
    * @param string $id: The id of the tutorial
    * @return string $content: The template output string
    */
    public function showExport($id)
    {
        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);

        // set up language elements
        $lblTutorial = $this->objLanguage->languageText('word_tutorial');
        $lblType = $this->objLanguage->languageText('mod_tutorials_exporttype', 'tutorials');
        $lblExport = $this->objLanguage->languageText('mod_tutorials_exportresults', 'tutorials');
        $lblResults = $this->objLanguage->languageText('mod_tutorials_results', 'tutorials');
        $lblMarks = $this->objLanguage->languageText('mod_tutorials_marks', 'tutorials');
        $lblSubmit = $this->objLanguage->languageText('word_submit');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblReturn = $this->objLanguage->languageText('mod_tutorials_returnlist', 'tutorials');

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblExport;
        $this->objHeading->type = 1;
        $header = $this->objHeading->show();
        $content = $header;

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblTutorial.': '.$tutorial['name'];
        $this->objHeading->type = 3;
        $header = $this->objHeading->show();
        $content .= $header;

        // set up htmlelements
        $this->objRadio = new radio('type');
        $this->objRadio->addOption('1', $lblResults);
        $this->objRadio->addOption('2', $lblMarks);
        $this->objRadio->setSelected('1');
        $this->objRadio->setBreakSpace('<br />');
        $radType = $this->objRadio->show();

        $this->objButton=new button('submit',$lblSubmit);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="document.frmCancel.submit();"';
        $btnCancel = $this->objButton->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpadding = '5';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblType.'</b>', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = 'onmouseover="this.className=\'tuts-ruler\';" onmouseout="this.className=\'\'; "';

        $this->objTable->startRow();
        $this->objTable->addCell($radType, '', '', '', '', '');
        $this->objTable->endRow();

        $this->objTable->row_attributes = '';
        $this->objTable->startRow();
        $this->objTable->addCell('&#160;', '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($btnSubmit.'&#160;'.$btnCancel, '', '', '', '', 'colspan="2"');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();

        // set up forms
        $this->objForm=new form('frmExport',$this->uri(array(
            'action'=>'doexport',
            'id' => $id,
        ), 'tutorials'));
        $this->objForm->addToForm($tblDisplay);
        $content .= $this->objForm->show();

        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'view',
            'id' => $id,
        ), 'tutorials'));
        $content .= $this->objForm->show();

        $this->objLink = new link($this->uri(array(
           'action' => 'liststudents',
            'id' => $id,
        ),'tutorials'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();
        $content .= '<br />'.$lnkReturn;

        return $content;
    }

    /**
    * Method to do the actual results export
    *
    * @access public
    * @param string $id: The id of the tutorial
    * @param array $type: The type of results to export
    * @param bool $status: TRUE on success | FALSE on failure
    */
    public function doExport($id, $type)
    {
        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $results = $this->objDbTutorials->getResultsForExport($id);
        $groupId = $this->objGroupadmin->getLeafId(array(
            $this->contextCode,
            'Students',
        ));
        $students = $this->objGroupadmin->getGroupUsers($groupId, array(
            'userid',
            'firstname',
            'surname',
        ));

        // set up language elements
        $lblStudentNo = $this->objLanguage->languageText('mod_tutorials_studentno', 'tutorials');
        $lblName = $this->objLanguage->languageText('phrase_firstname');
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblQuestion = $this->objLanguage->languageText('word_question');
        $lblResult = $this->objLanguage->languageText('word_result');
        $lblMark = $this->objLanguage->languageText('word_mark');
        $lblSubmitted = $this->objLanguage->languageText('word_submitted');
        $lblYes = $this->objLanguage->languageText('word_yes');
        $lblNo = $this->objLanguage->languageText('word_no');
        $lblNotMarked = $this->objLanguage->languageText('phrase_notmarked');
        $lblMarkedBy = $this->objLanguage->languageText('phrase_markedby');
        $lblMarked = $this->objLanguage->languageText('word_marked');
        $lblLecturer = $this->objLanguage->languageText('word_lecturer');

        if($type == 1){
            if($results == FALSE){
                return FALSE;
            }else{
                $userFileLocation = $this->objConfig->getValue('KEWL_CONTENT_BASEPATH');
                $fileLocation = $userFileLocation.'attachments/'.$this->userId;
                $checkDirectory = file_exists($fileLocation);
                if($checkDirectory === FALSE){
                    mkdir($userFileLocation.'attachments/'.$this->userId.'/');
                }
                $file = $fileLocation.'/results.csv';
                $outputFile = fopen($file, 'wb');
                $str = '"'.$lblStudentNo.'"';
                $str .= ',"'.$lblName.'"';
                $str .= ',"'.$lblSurname.'"';
                $str .= ',"'.$lblSubmitted.'"';
                if($tutorial['tutorial_type'] == 1){
                    $str .= ',"'.$lblMarked.'"';
                    $str .= ',"'.$lblMarkedBy.'"';
                    $str .= ',"'.$lblMarkedBy.' '.strtolower($lblLecturer).'"';
                }
                $str .= ',"'.$lblMark.'"';
                $str .= ',"'.$lblResult.'"';
                $str .= "\n";
                fwrite($outputFile, $str);
                foreach($students as $key => $student){
                    $array = array_merge($student, array(
                        'has_submitted' => '0',
                    ));
                    foreach($results as $result){
                        if($student['userid'] == $result['student_id']){
                            $array = array_merge($student, $result);
                        }
                    }
                    $students[$key] = $array;
                }
                $status = TRUE;
                foreach($students as $student){
                    if($student['has_submitted'] == 0){
                        $submitted = $lblNo;
                        $mark = '0';
                        $percentage = '0%';
                    }else{
                        $submitted = $lblYes;
                        if($student['mark_obtained'] == NULL){
                            $mark = $lblNotMarked;
                            $percentage = '0%';
                        }else{
                            $mark = $student['mark_obtained'];
                            $percentage = round(($student['mark_obtained'] / $tutorial['total_mark']) * 100, 0).'%';
                        }
                    }
                    $line = '"'.$student['userid'].'"';
                    $line .= ',"'.$student['firstname'].'"';
                    $line .= ',"'.$student['surname'].'"';
                    $line .= ',"'.$submitted.'"';
                    if($tutorial['tutorial_type'] == 1){
                        $lecturer = $this->objDbTutorials->checkLecturerMarked($id, $student['userid']);
                        $markedBy = $this->objDbTutorials->countCompletedMarked($id, $student['userid']);
                        $markedFor = $this->objDbTutorials->getMarkedStudents($id, $student['userid']);
                        $line .= ',"'.$markedFor.'"';
                        $line .= ',"'.$markedBy.'"';
                        if($lecturer == TRUE){
                            $line .= ',"'.$lblYes.'"';
                        }else{
                            $line .= ',"'.$lblNo.'"';
                        }
                    }
                    $line .= ',"'.$mark.'"';
                    $line .= ',"'.$percentage.'"';
                    $result = fwrite($outputFile, $line."\n");
                    if($result == FALSE){
                        $status = FALSE;
                    }
                }
                return $status;
            }
        }else{

        }
        return FALSE;
    }

    /**
    * Method to send the results via email
    *
    * @access public
    * @param string $id: The id of the tutorial
    * @return void
    */
    public function emailResults($id)
    {
        // set up email object
        $this->objEmail = $this->getObject('dbemail', 'internalmail');
        $this->objAttachments = $this->getObject('dbattachments', 'internalmail');

        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);

        // set up language elements
        $lblSubject = $this->objLanguage->languageText('mod_tutorials_resultssubject', 'tutorials');
        $array = array();
        $array['item'] = $tutorial['name'];
        $lblBody = $this->objLanguage->code2Txt('mod_tutorials_resultsbody', 'tutorials', $array);

        $emailId = $this->objEmail->sendMail($this->userId, $lblSubject, $lblBody, '1');
        $this->objAttachments->addAttachments($emailId, 'text/csv');

        $this->fileLocation = $this->objConfig->getValue('KEWL_CONTENT_BASEPATH').'attachments/';
        $this->attachLocation = $this->fileLocation.$this->userId."/";

        $files = $this->attachLocation.'*';
        if(glob($files)!=FALSE){
            foreach(glob($files) as $filename){
                unlink($filename);
            }
        }
        $checkDirectory = file_exists($this->attachLocation);
        if($checkDirectory){
            rmdir($this->attachLocation);
        }
    }

    /**
    * Method to send the moderation reason via email
    *
    * @access public
    * @param string $id: The id of the tutorial
    * @param string $order: The question orrder
    * @param string $emailList: The pipe separated list of email recipients
    * @param string $marks: The pipe separated list of marks
    * @param string $mark: The moderators mark
    * @param string $comment: The moderators comment
    * @return void
    */
    public function emailModeration($id, $order, $emailList, $marks, $mark, $comment)
    {
        // set up email object
        $this->objEmail = $this->getObject('dbemail', 'internalmail');

        // get data
        $tutorial = $this->objDbTutorials->getTutorial($id);
        $emails = explode('|', $emailList);
        $studentMarks = explode('|', $marks);

        // set up language elements
        $lblSubject = $this->objLanguage->languageText('mod_tutorials_moderatesubject', 'tutorials');

        foreach($emails as $key => $email){
            if($mark != $studentMarks[$key]){
                $array = array();
                $array['num'] = $order;
                $array['name'] = $tutorial['name'];
                $array['student'] = $studentMarks[$key];
                $array['moderator'] = $mark;
                $array['reason'] = $comment;
                $lblBody = $this->objLanguage->code2Txt('mod_tutorials_moderatebody', 'tutorials', $array);

                $emailId = $this->objEmail->sendMail($email, $lblSubject, $lblBody, '0');
            }
        }
    }
}
?>