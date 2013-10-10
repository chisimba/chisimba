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
 * Email popup template for the survey manager
 * Author Kevin Cyster
 * */

$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('pageSuppressIM', TRUE);
$this->setVar('suppressFooter', TRUE);

// set up html elements
$objHeader=&$this->loadClass('htmlheading','htmlelements');
$objTable=&$this->loadClass('htmltable','htmlelements');
$objIcon=&$this->newObject('geticon','htmlelements');
$objText=&$this->loadClass('textarea','htmlelements');
$objInput=&$this->loadClass('textinput','htmlelements');
$objButton=&$this->loadClass('button','htmlelements');
$objForm=&$this->loadClass('form','htmlelements');
$objLayer=&$this->loadClass('layer','htmlelements');

// set up language items
$subjectLabel=$this->objLanguage->languageText('mod_survey_subject', 'survey');
$sendLabel=$this->objLanguage->languageText('mod_survey_sendemail', 'survey');
$linkLabel=$this->objLanguage->languageText('mod_survey_linktext', 'survey');
$respondentSubjLabel=$this->objLanguage->languageText('mod_survey_respondentsubject', 'survey');
$anonymousLabel=$this->objLanguage->languageText('mod_survey_anon', 'survey');
$respondentLinkLabel=$this->objLanguage->languageText('mod_survey_take', 'survey');
$respondentsLabel=$this->objLanguage->languageText('mod_survey_wordrespondents', 'survey');
$observersLabel=$this->objLanguage->languageText('mod_survey_wordobservers', 'survey');
$collaboratorsLabel=$this->objLanguage->languageText('mod_survey_wordcollaborators', 'survey');
$uriLinkLabel=$this->objLanguage->languageText('mod_survey_linklabel', 'survey');
$observerSubjLabel=$this->objLanguage->languageText('mod_survey_observersubject', 'survey');
$collaboratorSubjLabel=$this->objLanguage->languageText('mod_survey_collaboratorsubject', 'survey');

// set up data
$arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
$surveyName=$arrSurveyData[0]['survey_name'];
$recordedResponses=$arrSurveyData[0]['recorded_responses'];

// set up code to text elements
$array=array('item'=>strtolower($respondentsLabel));
$respondentHeading=$this->objLanguage->code2Txt('mod_survey_emailheading', 'survey',$array);
$array=array('item'=>strtolower($observersLabel));
$observersHeading=$this->objLanguage->code2Txt('mod_survey_emailheading', 'survey',$array);
$array=array('item'=>strtolower($collaboratorsLabel));
$collaboratorsHeading=$this->objLanguage->code2Txt('mod_survey_emailheading', 'survey',$array);
$array=array('item'=>strtoupper($surveyName));
$respondentBodyLabel=$this->objLanguage->code2Txt('mod_survey_respondenttext', 'survey',$array);
$observerBodyLabel=$this->objLanguage->code2Txt('mod_survey_observertext', 'survey',$array);
$collaboratorBodyLabel=$this->objLanguage->code2Txt('mod_survey_collaboratortext', 'survey',$array);

if($mode=='Respondents') {
    $objHeader = new htmlheading();
    $objHeader->str=$respondentHeading;
    $objHeader->type=1;
    $heading=$objHeader->show().'<hr />';

    $objInput=new textinput('subject',$respondentSubjLabel,'','75');
    $subjectText=$objInput->show();

    $objInput=new textinput('link',$respondentLinkLabel,'','75');
    $linkText=$objInput->show();

    if($recordedResponses!=1) {
        $respondentBodyLabel=$respondentBodyLabel.$anonymousLabel;
    }

    $objText=new textarea('body',$respondentBodyLabel,'6','85');
    $bodyArea=$objText->show();

    $objTable=new htmltable();
    $objTable->cellspacing=2;
    $objTable->cellpadding=2;

    $objTable->startRow();
    $objTable->addCell('<nobr><b>'.$subjectLabel.' :</b></nobr>','10%','','','','');
    $objTable->addCell($subjectText,'','','','','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell($bodyArea,'','','','','colspan="2"');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<nobr><b>'.$linkLabel.' :</b></nobr>','','','','','');
    $objTable->addCell($linkText,'','','','','');
    $objTable->endRow();

    $emailTable=$objTable->show();

    // set up submit button
    $objButton=new button('sendbutton',$sendLabel);
    $objButton->extra=' onclick="javascript:
            this.disabled=\'disabled\';
            document.getElementById(\'form_emailform\').submit();
            //opener.location.reload();
            //self.close();
        "';
    $sendButton=$objButton->show();

    // set up form
    $objForm=new form('emailform',$this->uri(array('action'=>'sendemail','survey_id'=>$surveyId,'mode'=>$mode)));
    $objForm->addToForm($emailTable);
    $objForm->addToForm('<br />'.$sendButton);
    $respondentForm=$objForm->show();

    $objLayer = new layer();
    $objLayer->padding='10px';
    $objLayer->addToStr($heading);
    $objLayer->addToStr($respondentForm);
    $respondentLayer=$objLayer->show();

    echo $respondentLayer;
}elseif($mode=='Observers') {
    $objHeader = new htmlheading();
    $objHeader->str=$observersHeading;
    $objHeader->type=1;
    $heading=$objHeader->show().'<hr />';

    $objInput=new textinput('subject',$observerSubjLabel,'','75');
    $subjectText=$objInput->show();

    $objInput=new textinput('link',$uriLinkLabel,'','75');
    $linkText=$objInput->show();

    $objText=new textarea('body',$observerBodyLabel,'6','85');
    $bodyArea=$objText->show();

    $objTable=new htmltable();
    $objTable->cellspacing=2;
    $objTable->cellpadding=2;

    $objTable->startRow();
    $objTable->addCell('<nobr><b>'.$subjectLabel.' :</b></nobr>','10%','','','','');
    $objTable->addCell($subjectText,'','','','','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell($bodyArea,'','','','','colspan="2"');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<nobr><b>'.$linkLabel.' :</b></nobr>','','','','','');
    $objTable->addCell($linkText,'','','','','');
    $objTable->endRow();

    $emailTable=$objTable->show();

    // set up submit button
    $objButton=new button('sendbutton',$sendLabel);
    $objButton->extra=' onclick="javascript:
            this.disabled=\'disabled\';
            document.getElementById(\'form_emailform\').submit();
            opener.location.reload();
            self.close();
        "';
    $sendButton=$objButton->show();

    // set up form
    $objForm=new form('emailform',$this->uri(array('action'=>'sendemail','survey_id'=>$surveyId,'mode'=>$mode)));
    $objForm->addToForm($emailTable);
    $objForm->addToForm('<br />'.$sendButton);
    $observerForm=$objForm->show();

    $objLayer = new layer();
    $objLayer->padding='10px';
    $objLayer->addToStr($heading);
    $objLayer->addToStr($observerForm);
    $observerLayer=$objLayer->show();

    echo $observerLayer;
}else {
    $objHeader = new htmlheading();
    $objHeader->str=$collaboratorsHeading;
    $objHeader->type=1;
    $heading=$objHeader->show().'<hr />';

    $objInput=new textinput('subject',$collaboratorSubjLabel,'','75');
    $subjectText=$objInput->show();

    $objInput=new textinput('link',$uriLinkLabel,'','75');
    $linkText=$objInput->show();

    $objText=new textarea('body',$collaboratorBodyLabel,'6','85');
    $bodyArea=$objText->show();

    $objTable=new htmltable();
    $objTable->cellspacing=2;
    $objTable->cellpadding=2;

    $objTable->startRow();
    $objTable->addCell('<nobr><b>'.$subjectLabel.' :</b></nobr>','10%','','','','');
    $objTable->addCell($subjectText,'','','','','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell($bodyArea,'','','','','colspan="2"');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<nobr><b>'.$linkLabel.' :</b></nobr>','','','','','');
    $objTable->addCell($linkText,'','','','','');
    $objTable->endRow();

    $emailTable=$objTable->show();

    // set up submit button
    $objButton=new button('sendbutton',$sendLabel);
    $objButton->extra=' onclick="javascript;
            this.disabled=\'disabled\';
            document.getElementById(\'form_emailform\').submit();
            opener.location.reload();
            self.close();
        "';
    $sendButton=$objButton->show();

    // set up form
    $objForm=new form('emailform',$this->uri(array('action'=>'sendemail','survey_id'=>$surveyId,'mode'=>$mode)));
    $objForm->addToForm($emailTable);
    $objForm->addToForm('<br />'.$sendButton);
    $collaboratorForm=$objForm->show();

    $objLayer = new layer();
    $objLayer->padding='10px';
    $objLayer->addToStr($heading);
    $objLayer->addToStr($collaboratorForm);
    $collaboratorLayer=$objLayer->show();

    echo $collaboratorLayer;
}
?>