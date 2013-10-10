<?php
/* -------------------- survey extends controller ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * @package survey
 */

/**
 * Group template for the survey manager
 * Author Kevin Cyster
 * */

$headerParams=$this->getJavascriptFile('selectall.js','htmlelements');
$this->appendArrayVar('headerParams',$headerParams);
$this->setLayoutTemplate('layout_tpl.php');

// set up html elements
$objHeader=&$this->loadClass('htmlheading','htmlelements');
$objTable=&$this->loadClass('htmltable','htmlelements');
$objButton=&$this->loadClass('button','htmlelements');
$objForm=&$this->loadClass('form','htmlelements');
$objInput=&$this->loadClass('textinput','htmlelements');
$objDrop=&$this->loadClass('dropdown','htmlelements');
$objCheck=&$this->loadClass('checkbox','htmlelements');
$objLink=&$this->loadClass('link','htmlelements');
$objIcon=&$this->newObject('geticon','htmlelements');
$objTabbedbox=&$this->loadClass('tabbedbox','htmlelements');
$objPopup=&$this->loadClass('windowpop','htmlelements');

// set up language items
$headerLabel=$this->objLanguage->languageText('mod_survey_groupheader', 'survey');
$observersLabel=$this->objLanguage->languageText('mod_survey_wordobservers', 'survey');
$collaboratorsLabel=$this->objLanguage->languageText('mod_survey_wordcollaborators', 'survey');
$respondentsLabel=$this->objLanguage->languageText('mod_survey_wordrespondents', 'survey');
$userIdLabel=$this->objLanguage->languageText('mod_survey_userid', 'survey');
$usernameLabel=$this->objLanguage->languageText('mod_survey_username', 'survey');
$titleLabel=$this->objLanguage->languageText('mod_survey_title', 'survey');
$firstnameLabel=$this->objLanguage->languageText('mod_survey_firstname', 'survey');
$surnameLabel=$this->objLanguage->languageText('mod_survey_surname', 'survey');
$emailLabel=$this->objLanguage->languageText('mod_survey_emailaddress', 'survey');
$searchHeading=$this->objLanguage->languageText('mod_survey_searchheading', 'survey');
$fieldLabel=$this->objLanguage->languageText('mod_survey_searchby', 'survey');
$searchByLabel=$this->objLanguage->languageText('mod_survey_startswith', 'survey');
$searchLabel=$this->objLanguage->languageText('mod_survey_search', 'survey');
$orderLabel=$this->objLanguage->languageText('mod_survey_orderby', 'survey');
$resultsLabel=$this->objLanguage->languageText('mod_survey_pageresults', 'survey');
$emptyLabel=$this->objLanguage->languageText('mod_survey_empty', 'survey');
$lecturersLabel=$this->objLanguage->languageText('mod_survey_lecturers', 'survey');
$studentsLabel=$this->objLanguage->languageText('mod_survey_student', 'survey');
$respondentsDescLabel=$this->objLanguage->languageText('mod_survey_respondents', 'survey');
$listLabel=$this->objLanguage->languageText('mod_survey_list', 'survey');
$questionLabel=$this->objLanguage->languageText('mod_survey_question', 'survey');
$surveyLabel=$this->objLanguage->languageText('mod_survey_survey', 'survey');
$groupsLabel=$this->objLanguage->languageText('mod_survey_groups', 'survey');
$selectLabel=$this->objLanguage->languageText('mod_survey_select', 'survey');
$selectallLabel=$this->objLanguage->languageText('mod_survey_selectall', 'survey');
$selectnoneLabel=$this->objLanguage->languageText('mod_survey_selectnone', 'survey');
$deleteLabel=$this->objLanguage->languageText('mod_survey_deleteselected', 'survey');
$addUsersLabel=$this->objLanguage->languageText('mod_survey_addusers', 'survey');
$addGroupsLabel=$this->objLanguage->languageText('mod_survey_addgroups', 'survey');

// set up code to text
$array=array('authors'=>$lecturersLabel);
$observersDescLabel=$this->objLanguage->code2Txt('mod_survey_observers', 'survey',$array);
$array=array('readonlys'=>$studentsLabel);
$collaboratorsDescLabel=$this->objLanguage->code2Txt('mod_survey_collaborators', 'survey',$array);
$array=array('item'=>$questionLabel);
$addLabel=$this->objLanguage->code2Txt('mod_survey_add', 'survey',$array);
$array=array('item'=>strtolower($surveyLabel));
$returnLabel=$this->objLanguage->code2Txt('mod_survey_return', 'survey',$array);
$array=array('item'=>strtolower($observersLabel));
$observerConfirmLabel=$this->objLanguage->code2txt('mod_survey_delete', 'survey',$array);
$array=array('item'=>strtolower($collaboratorsLabel));
$collaboratorConfirmLabel=$this->objLanguage->code2txt('mod_survey_delete', 'survey',$array);
$array=array('item'=>strtolower($respondentsLabel));
$respondentConfirmLabel=$this->objLanguage->code2txt('mod_survey_delete', 'survey',$array);
$array=array('item'=>strtolower($observersLabel));
$observersEmailLabel=$this->objLanguage->code2Txt('mod_survey_emailheading', 'survey',$array);
$array=array('item'=>strtolower($collaboratorsLabel));
$collaboratorsEmailLabel=$this->objLanguage->code2Txt('mod_survey_emailheading', 'survey',$array);

// set up data
$arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
$surveyName=$arrSurveyData[0]['survey_name'];
$arrUserGroupList = $this->groups->getAllGroupUsers($surveyId);
// set up add user icon
$objIcon->title=$addUsersLabel;
$addUsersIcon=$objIcon->getAddIcon($this->uri(array('action'=>'search','survey_id'=>$surveyId)));

// set up add group icon
$objIcon->title=$addGroupsLabel;
$addGroupsIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'search','survey_id'=>$surveyId,'field'=>'groups')),'add_multiple');

// set up header
$objHeader = new htmlheading;
$objHeader->str=$headerLabel.'&nbsp;'.$addUsersIcon.'&nbsp;'.$addGroupsIcon;
$objHeader->type=1;
$str=$objHeader->show();
echo $str;

$objHeader = new htmlheading;
$objHeader->str=$surveyName;
$objHeader->type=3;
$str=$objHeader->show();
echo $str;

$objInput=new textinput('survey_id',$surveyId);
$objInput->fldType='hidden';
$surveyIdText=$objInput->show();

// set up email popup
$objIcon->title=$observersEmailLabel;
$objIcon->setIcon('notes');
$emailIcon=$objIcon->show();

$objPopup = new windowpop();
$objPopup->set('location',$this->uri(array('action'=>'mailpopup','survey_id'=>$surveyId,'mode'=>'Observers')));
$objPopup->set('linktext',$emailIcon);
$objPopup->set('width','600');
$objPopup->set('height','350');
$objPopup->set('left','200');
$objPopup->set('top','200');
$objPopup->putJs(); // you only need to do this once per page
$observersEmailPopup=$objPopup->show();

// set up observers table
$objTable=new htmltable();
$objTable->cellspacing='2';
$objTable->cellpadding='2';

$objTable->startRow();
$objTable->addCell($observersDescLabel,'','','','confirm','colspan="7"');
$objTable->endRow();

$objTable->startRow();
if(!empty($arrUserGroupList['Observers'])) {
    $objTable->addCell($observersEmailPopup,'','','center','heading','');
}else {
    $objTable->addCell('','','','','heading','');
}
$objTable->addCell($userIdLabel,'','','','heading','');
$objTable->addCell($usernameLabel,'','','','heading','');
$objTable->addCell($titleLabel,'','','','heading','');
$objTable->addCell($firstnameLabel,'','','','heading','');
$objTable->addCell($surnameLabel,'','','','heading','');
$objTable->addCell($emailLabel,'','','','heading','');
$objTable->endRow();

if(isset($arrUserGroupList['Observers']) && !empty($arrUserGroupList['Observers'])) {
    $objTable->row_attributes='onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';

    // set up check all button
    $objButton=new button('checkallbutton',$selectallLabel);
    $objButton->setOnClick("javascript:SetAllCheckBoxes('Observers','userId[]',true);");
    $selectAllButton=$objButton->show();

    // set up uncheck all button
    $objButton=new button('uncheckallbutton',$selectnoneLabel);
    $objButton->setOnClick("javascript:SetAllCheckBoxes('Observers','userId[]',false);");
    $selectNoneButton=$objButton->show();

    // set up delete button
    $objButton=new button('delete',$deleteLabel);
    $objButton->setToSubmit();
    $deleteButton=$objButton->show();

    foreach($arrUserGroupList['Observers'] as $user) {
        // set up checkboxes
        $objCheck=new checkbox('userId[]');
        $objCheck->value=$user['userid'];
        $deleteCheck=$objCheck->show();

        $objTable->startRow();
        $objTable->addCell($deleteCheck,'','','center','','');
        $objTable->addCell($user['userid'],'','','','','');
        $objTable->addCell($user['username'],'','','','','');
        $objTable->addCell($user['title'],'','','','','');
        $objTable->addCell($user['firstname'],'','','','','');
        $objTable->addCell($user['surname'],'','','','','');
        $objTable->addCell($user['emailaddress'],'','','','','');
        $objTable->endRow();

    }
    $objTable->row_attributes='';
    $objTable->startRow();
    $objTable->addCell($selectAllButton,'','','','','');
    $objTable->addCell($selectNoneButton,'','','','','');
    $objTable->addCell($deleteButton,'','','','','');
    $objTable->endRow();
}else {
    $objTable->startRow();
    $objTable->addCell($emptyLabel,'','','center','noRecordsMessage','colspan="7"');
    $objTable->endRow();
}

$observersTable=$objTable->show();

$objTabbedbox=new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel($observersLabel);
$objTabbedbox->addBoxContent($observersTable);

$observersTab=$objTabbedbox->show();

$objForm=new form('Observers',$this->uri(array('action'=>'deletegroupusers','group'=>'Observers','survey_id'=>$surveyId)));
$objForm->addToForm($surveyIdText);
$objForm->addToForm($observersTab);
$observersForm=$objForm->show();

echo $observersForm;

// set up collaborators table
$objIcon->title=$collaboratorsEmailLabel;
$objIcon->setIcon('notes');
$emailIcon=$objIcon->show();

$objPopup->set('location',$this->uri(array('action'=>'mailpopup','survey_id'=>$surveyId,'mode'=>'Collaborators')));
$objPopup->set('linktext',$emailIcon);
$objPopup->set('width','600');
$objPopup->set('height','350');
$objPopup->set('left','200');
$objPopup->set('top','200');
$collaboratorsEmailPopup=$objPopup->show();

$objTable=new htmltable();
$objTable->cellspacing='2';
$objTable->cellpadding='2';

$objTable->startRow();
$objTable->addCell($collaboratorsDescLabel,'','','','confirm','colspan="7"');
$objTable->endRow();

$objTable->startRow();
if(!empty($arrUserGroupList['Collaborators'])) {
    $objTable->addCell($collaboratorsEmailPopup,'','','center','heading','');
}else {
    $objTable->addCell('','','','','heading','');
}
$objTable->addCell($userIdLabel,'','','','heading','');
$objTable->addCell($usernameLabel,'','','','heading','');
$objTable->addCell($titleLabel,'','','','heading','');
$objTable->addCell($firstnameLabel,'','','','heading','');
$objTable->addCell($surnameLabel,'','','','heading','');
$objTable->addCell($emailLabel,'','','','heading','');
$objTable->endRow();

if(isset($arrUserGroupList['Collaborators']) && !empty($arrUserGroupList['Collaborators'])) {
    $objTable->row_attributes='onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';

    // set up check all button
    $objButton=new button('checkallbutton',$selectallLabel);
    $objButton->setOnClick("javascript:SetAllCheckBoxes('Collaborators','userId[]',true);");
    $selectAllButton=$objButton->show();

    // set up uncheck all button
    $objButton=new button('uncheckallbutton',$selectnoneLabel);
    $objButton->setOnClick("javascript:SetAllCheckBoxes('Collaborators','userId[]',false);");
    $selectNoneButton=$objButton->show();

    // set up delete button
    $objButton=new button('delete',$deleteLabel);
    $objButton->setToSubmit();
    $deleteButton=$objButton->show();

    foreach($arrUserGroupList['Collaborators'] as $user) {
        // set up checkboxes
        $objCheck=new checkbox('userId[]');
        $objCheck->value=$user['userid'];
        $deleteCheck=$objCheck->show();

        $objTable->startRow();
        $objTable->addCell($deleteCheck,'','','center','','');
        $objTable->addCell($user['userid'],'','','','','');
        $objTable->addCell($user['username'],'','','','','');
        $objTable->addCell($user['title'],'','','','','');
        $objTable->addCell($user['firstname'],'','','','','');
        $objTable->addCell($user['surname'],'','','','','');
        $objTable->addCell($user['emailaddress'],'','','','','');
        $objTable->endRow();

    }
    $objTable->row_attributes='';
    $objTable->startRow();
    $objTable->addCell($selectAllButton,'','','','','');
    $objTable->addCell($selectNoneButton,'','','','','');
    $objTable->addCell($deleteButton,'','','','','');
    $objTable->endRow();
}else {
    $objTable->startRow();
    $objTable->addCell($emptyLabel,'','','center','noRecordsMessage','colspan="7"');
    $objTable->endRow();
}

$collaboratorsTable=$objTable->show();

$objTabbedbox=new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel($collaboratorsLabel);
$objTabbedbox->addBoxContent($collaboratorsTable);

$collaboratorsTab=$objTabbedbox->show();

$objForm=new form('Collaborators',$this->uri(array('action'=>'deletegroupusers','group'=>'Collaborators')));
$objForm->addToForm($surveyIdText);
$objForm->addToForm($collaboratorsTab);
$collaboratorsForm=$objForm->show();

echo $collaboratorsForm;

// set up respondents table
$objTable=new htmltable();
$objTable->cellspacing='2';
$objTable->cellpadding='2';

$objTable->startRow();
$objTable->addCell($respondentsDescLabel,'','','','confirm','colspan="7"');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('','','','','heading','');
$objTable->addCell($userIdLabel,'','','','heading','');
$objTable->addCell($usernameLabel,'','','','heading','');
$objTable->addCell($titleLabel,'','','','heading','');
$objTable->addCell($firstnameLabel,'','','','heading','');
$objTable->addCell($surnameLabel,'','','','heading','');
$objTable->addCell($emailLabel,'','','','heading','');
$objTable->endRow();

if(isset($arrUserGroupList['Respondents']) && !empty($arrUserGroupList['Respondents'])) {
    $objTable->row_attributes='onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';

    // set up check all button
    $objButton=new button('checkallbutton',$selectallLabel);
    $objButton->setOnClick("javascript:SetAllCheckBoxes('Respondents','userId[]',true);");
    $selectAllButton=$objButton->show();

    // set up uncheck all button
    $objButton=new button('uncheckallbutton',$selectnoneLabel);
    $objButton->setOnClick("javascript:SetAllCheckBoxes('Respondents','userId[]',false);");
    $selectNoneButton=$objButton->show();

    // set up delete button
    $objButton=new button('delete',$deleteLabel);
    $objButton->setToSubmit();
    $deleteButton=$objButton->show();

    foreach($arrUserGroupList['Respondents'] as $user) {
        // set up checkboxes
        $objCheck=new checkbox('userId[]');
        $objCheck->value=$user['userid'];
        $deleteCheck=$objCheck->show();

        $objTable->startRow();
        $objTable->addCell($deleteCheck,'','','center','','');
        $objTable->addCell($user['userid'],'','','','','');
        $objTable->addCell($user['username'],'','','','','');
        $objTable->addCell($user['title'],'','','','','');
        $objTable->addCell($user['firstname'],'','','','','');
        $objTable->addCell($user['surname'],'','','','','');
        $objTable->addCell($user['emailaddress'],'','','','','');
        $objTable->endRow();

    }
    $objTable->row_attributes='';
    $objTable->startRow();
    $objTable->addCell($selectAllButton,'','','','','');
    $objTable->addCell($selectNoneButton,'','','','','');
    $objTable->addCell($deleteButton,'','','','','');
    $objTable->endRow();
}else {
    $objTable->startRow();
    $objTable->addCell($emptyLabel,'','','center','noRecordsMessage','colspan="7"');
    $objTable->endRow();
}

$respondentsTable=$objTable->show();

$objTabbedbox=new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel($respondentsLabel);
$objTabbedbox->addBoxContent($respondentsTable);

$respondentsTab=$objTabbedbox->show();

$objForm=new form('Respondents',$this->uri(array('action'=>'deletegroupusers','group'=>'Respondents')));
$objForm->addToForm($surveyIdText);
$objForm->addToForm($respondentsTab);
$respondentsForm=$objForm->show();

echo $respondentsForm;

// set up search table
$objDrop=new dropdown('field');
$objDrop->addOption('userId',$userIdLabel);
$objDrop->addOption('username',$usernameLabel);
$objDrop->addOption('firstName',$firstnameLabel);
$objDrop->addOption('surname',$surnameLabel);
$objDrop->addOption('groups',$groupsLabel);
$objDrop->setSelected('surname');
$objDrop->extra=' onchange="javascript:
        if(this.value==\'groups\')
        {
            var x=document.getElementById(\'input_order\');
            x.disabled=true;
            x.options[x.selectedIndex].text=\'- '.$selectLabel.' -\';
        }else{
            var x=document.getElementById(\'input_order\');
            x.disabled=false;
            if(x.options[x.selectedIndex].value==\'userId\'){
                x.options[x.selectedIndex].text=\''.$userIdLabel.'\';
            }else if(x.options[x.selectedIndex].value==\'username\'){
                x.options[x.selectedIndex].text=\''.$usernameLabel.'\';
            }else if(x.options[x.selectedIndex].value==\'firstName\'){
                x.options[x.selectedIndex].text=\''.$firstnameLabel.'\';
            }else{
                x.options[x.selectedIndex].text=\''.$surnameLabel.'\';
            }
        }
    "';
$fieldDrop=$objDrop->show();

$objInput=new textinput('search','','','50');
$searchByText=$objInput->show();

$objDrop=new dropdown('order');
$objDrop->addOption('userId',$userIdLabel);
$objDrop->addOption('username',$usernameLabel);
$objDrop->addOption('firstName',$firstnameLabel);
$objDrop->addOption('surname',$surnameLabel);
$objDrop->setSelected('surname');
//    $objRadio->setBreakSpace('<br>');
$orderDrop=$objDrop->show();

$objDrop=new dropdown('number');
$objDrop->addOption(25,'25');
$objDrop->addOption(50,'50');
$objDrop->addOption(75,'75');
$objDrop->addOption(100,'100');
$objDrop->setSelected(25);
//    $objRadio->setBreakSpace('<br>');
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
$objButton->setToSubmit();
$searchButton=$objButton->show();

$objForm=new form('searchform',$this->uri(array('action'=>'search')));
$objForm->addToForm($surveyIdText);
$objForm->addToForm($searchTable);
$objForm->addToForm($searchButton);
$searchForm=$objForm->show();

$objTabbedbox=new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel($searchHeading);
$objTabbedbox->addBoxContent($searchForm);

$str=$objTabbedbox->show();

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

$arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
if(!empty($arrQuestionList)) {
    echo $listLink.' / '.$returnLink;
}else {
    echo $addLink.' / '.$returnLink;
}
?>
