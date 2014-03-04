<?php
/* -------------------- survey extends controller ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package survey
*/

/**
* Error template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');
    $objForm=&$this->loadClass('form','htmlelements');
    $objInput=&$this->loadClass('textinput','htmlelements');
    $objDrop=&$this->loadClass('dropdown','htmlelements');
    $objRadio=&$this->loadClass('radio','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');
    $objTabbedbox=&$this->loadClass('tabbedbox','htmlelements');

    $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
    echo $objHighlightLabels->show();

// set up language items
    $headerLabel=$this->objLanguage->languageText('mod_survey_searchusers', 'survey');
    $observerLabel=$this->objLanguage->languageText('mod_survey_wordobserver', 'survey');
    $collaboratorLabel=$this->objLanguage->languageText('mod_survey_wordcollaborator', 'survey');
    $respondentLabel=$this->objLanguage->languageText('mod_survey_wordrespondent', 'survey');
    $userIdLabel=$this->objLanguage->languageText('mod_survey_userid', 'survey');
    $usernameLabel=$this->objLanguage->languageText('mod_survey_username', 'survey');
    $titleLabel=$this->objLanguage->languageText('mod_survey_title', 'survey');
    $firstnameLabel=$this->objLanguage->languageText('mod_survey_firstname', 'survey');
    $surnameLabel=$this->objLanguage->languageText('mod_survey_surname', 'survey');
    $emailLabel=$this->objLanguage->languageText('mod_survey_emailaddress', 'survey');
    $fieldLabel=$this->objLanguage->languageText('mod_survey_searchby', 'survey');
    $searchByLabel=$this->objLanguage->languageText('mod_survey_startswith', 'survey');
    $searchLabel=$this->objLanguage->languageText('mod_survey_search', 'survey');
    $orderLabel=$this->objLanguage->languageText('mod_survey_orderby', 'survey');
    $resultsLabel=$this->objLanguage->languageText('mod_survey_pageresults', 'survey');
    $emptyLabel=$this->objLanguage->languageText('mod_survey_empty', 'survey');
    $usersLabel=$this->objLanguage->languageText('mod_survey_listofusers', 'survey');
    $assignLabel=$this->objLanguage->languageText('mod_survey_assign', 'survey');
    $listLabel=$this->objLanguage->languageText('mod_survey_list', 'survey');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question', 'survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey', 'survey');
    $groupsLabel=$this->objLanguage->languageText('mod_survey_groupheader', 'survey');
    $notUserLabel=$this->objLanguage->languageText('mod_survey_notuser', 'survey');
    $updateLabel=$this->objLanguage->languageText('mod_survey_update', 'survey');

// set up code to text
    $array=array('item'=>$questionLabel);
    $addLabel=$this->objLanguage->code2Txt('mod_survey_add', 'survey',$array);
    $array=array('item'=>strtolower($surveyLabel));
    $returnLabel=$this->objLanguage->code2Txt('mod_survey_return', 'survey',$array);
    $array=array('item'=>strtolower($groupsLabel));
    $returnGroupsLabel=$this->objLanguage->code2Txt('mod_survey_return', 'survey',$array);
    $array=array('item'=>strtolower($observerLabel));
    $makeObserverLabel=$this->objLanguage->code2Txt('mod_survey_make', 'survey',$array);
    $array=array('item'=>strtolower($collaboratorLabel));
    $makeCollaboratorLabel=$this->objLanguage->code2Txt('mod_survey_make', 'survey',$array);
    $array=array('item'=>strtolower($respondentLabel));
    $makeRespondentLabel=$this->objLanguage->code2Txt('mod_survey_make', 'survey',$array);

// set up data
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $surveyName=$arrSurveyData[0]['survey_name'];

// set up header
    $objHeader = new htmlheading();
    $objHeader->str=$headerLabel;
    $objHeader->type=1;
    $str=$objHeader->show();
    echo $str;

    $objHeader = new htmlheading();
    $objHeader->str=$surveyName;
    $objHeader->type=3;
    $str=$objHeader->show();
    echo $str;

// set up search table
    $objInput=new textinput('survey_id',$surveyId);
    $objInput->fldType='hidden';
    $surveyIdText=$objInput->show();

    $objDrop=new dropdown('field');
    $objDrop->addOption('userId',$userIdLabel);
    $objDrop->addOption('username',$usernameLabel);
    $objDrop->addOption('firstName',$firstnameLabel);
    $objDrop->addOption('surname',$surnameLabel);
    $objDrop->setSelected($field);
    $fieldDrop=$objDrop->show();

    $objInput=new textinput('search',$search,'','50');
    $searchByText=$objInput->show();

    $objDrop=new dropdown('order');
    $objDrop->addOption('userId',$userIdLabel);
    $objDrop->addOption('username',$usernameLabel);
    $objDrop->addOption('firstName',$firstnameLabel);
    $objDrop->addOption('surname',$surnameLabel);
    $objDrop->setSelected($order);
    $orderDrop=$objDrop->show();

    $objDrop=new dropdown('number');
    $objDrop->addOption(1,'1');
    $objDrop->addOption(25,'25');
    $objDrop->addOption(50,'50');
    $objDrop->addOption(75,'75');
    $objDrop->addOption(100,'100');
    $objDrop->setSelected($number);
    $numberDrop=$objDrop->show();

    $objTable=new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $objTable->startRow();
    $objTable->addCell($fieldLabel,'10%','','','','');
    $objTable->addCell($fieldDrop,'','','','','');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell($searchByLabel,'','','','','');
    $objTable->addCell($searchByText,'','','','','');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell($orderLabel,'','','','','');
    $objTable->addCell($orderDrop,'','','','','');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell('<nobr>'.$resultsLabel.'</nobr>','','','','','');
    $objTable->addCell($numberDrop,'','','','','');
    $objTable->endRow();

    $searchTable=$objTable->show();

    $objButton=new button('searchbutton',$searchLabel);
    $objButton->extra=' onclick="javascript:
        this.disabled=\'disabled\';
        document.getElementById(\'form_searchform\').submit();
    "';
    $searchButton=$objButton->show();

    $objForm=new form('searchform',$this->uri(array('action'=>'search')));
    $objForm->addToForm($surveyIdText);
    $objForm->addToForm($searchTable);
    $objForm->addToForm($searchButton);
    $searchForm=$objForm->show();

    $objTabbedbox=new tabbedbox();
    $objTabbedbox->extra = ' style="padding:10px;"';
    $objTabbedbox->addTabLabel($searchLabel);
    $objTabbedbox->addBoxContent($searchForm);

    $str=$objTabbedbox->show();

// set up users table
    $objButton=new button('updatebutton',$updateLabel);
    $objButton->extra=' onclick="javascript:
        this.disabled=\'disabled\';
        document.getElementById(\'form_updateform\').submit();
    "';
    $updateButton=$objButton->show();

    $objTable=new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $objTable->startRow();
    $objTable->addCell($userIdLabel,'','','','heading','');
    $objTable->addCell($usernameLabel,'','','','heading','');
    $objTable->addCell($firstnameLabel,'','','','heading','');
    $objTable->addCell($surnameLabel,'','','','heading','');
    $objTable->addCell($assignLabel,'','','','heading','');
    $objTable->endRow();

    if(empty($arrUserList)){
        $objTable->startRow();
        $objTable->addCell($emptyLabel,'','','center','noRecordsMessage','colspan="5"');
        $objTable->endRow();
    }else{
        foreach($arrUserList as $user){
            $group=$this->groups->getUserGroup($user['userid'],$surveyId);

            // set up not user icon
            $objIcon->title=$notUserLabel;
            $objIcon->setIcon('not_applicable','gif','icons/');
            $notUserIcon=$objIcon->show();

            // set up make observer icon
            $objIcon->title=$makeObserverLabel;
            $objIcon->setIcon('userview','gif','icons/modules/');
            $makeObserverIcon=$objIcon->show();

            // set up make collaborator icon
            $objIcon->title=$makeCollaboratorLabel;
            $objIcon->setIcon('contextmembers','gif','icons/modules');
            $makeCollaboratorIcon=$objIcon->show();

            // set up make respondent icon
            $objIcon->title=$makeRespondentLabel;
            $objIcon->setIcon('userfiles','gif','icons/modules');
            $makeRespondentIcon=$objIcon->show();

            //set up assing radio
            $objRadio=new radio('assign['.$user['userid'].']');
            $objRadio->addOption('None',$notUserIcon);
            $objRadio->addOption('Observers',$makeObserverIcon);
            $objRadio->addOption('Collaborators',$makeCollaboratorIcon);
            $objRadio->addOption('Respondents',$makeRespondentIcon);
            $objRadio->setSelected($group);
            $objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;');
            $assignRadio=$objRadio->show();

            $objTable->row_attributes='onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';
            $objTable->startRow();
            $objTable->addCell($user['userid'],'','','','','');
            $objTable->addCell($user['username'],'','','','','');
            $objTable->addCell($user['firstname'],'','','','','');
            $objTable->addCell($user['surname'],'','','','','');
            $objTable->addCell('<nobr>'.$assignRadio.'</nobr>','','','','','');
            $objTable->endRow();
        }
        $objTable->row_attributes='';
        $objTable->startRow();
        $objTable->addCell('','','','','','');
        $objTable->addCell('','','','','','');
        $objTable->addCell('','','','','','');
        $objTable->addCell('','','','','','');
        $objTable->addCell($updateButton,'','','center','','');
        $objTable->endRow();
    }
    $pages=$this->groups->generateUserPaging($search,$field,$order,$number,$page,$surveyId);

    $respondentsTable=$objTable->show();

    $objForm=new form('updateform',$this->uri(array('action'=>'updategroups','mode'=>'users','survey_id'=>$surveyId)));
    $objForm->addToForm($surveyIdText);
    $objForm->addToForm($respondentsTable);
    $updateusersForm=$objForm->show();

    $objTabbedbox=new tabbedbox();
    $objTabbedbox->extra = 'style="padding: 10px;"';
    $objTabbedbox->addTabLabel($usersLabel);
    $objTabbedbox->addBoxContent($updateusersForm.'<hr />'.$pages);

    $str.=$objTabbedbox->show();

    echo $str;

    $objLink=new link($this->uri(array('action'=>'listquestions','survey_id'=>$surveyId)));
    $objLink->link=$listLabel;
    $listLink=$objLink->show();

    $objLink=new link($this->uri(array('action'=>'addquestion','survey_id'=>$surveyId)));
    $objLink->link=$addLabel;
    $addLink=$objLink->show();

    $objLink=new link($this->uri(array('action'=>'')));
    $objLink->link=$returnLabel;
    $returnLink=$objLink->show();

    $objLink=new link($this->uri(array('action'=>'surveygroups','survey_id'=>$surveyId)));
    $objLink->link=$returnGroupsLabel;
    $groupLink=$objLink->show();

    $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
    if(!empty($arrQuestionList)){
        echo $groupLink.' / '.$listLink.' / '.$returnLink;
    }else{
        echo $groupLink.' / '.$addLink.' / '.$returnLink;
    }
?>