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
* Add edit template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');
    $objRadio=&$this->loadClass('radio','htmlelements');
    $objButton=&$this->loadClass('button','htmlelements');
    $objForm=&$this->loadClass('form','htmlelements');

// set up language items
    $nameLabel=$this->objLanguage->languageText('mod_survey_surveyname','survey');
    $startLabel=$this->objLanguage->languageText('mod_survey_startdate','survey');
    $endLabel=$this->objLanguage->languageText('mod_survey_enddate','survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey','survey');
    $maximumLabel=$this->objLanguage->languageText('mod_survey_maximumresponse','survey');
    $anonymousLabel=$this->objLanguage->languageText('mod_survey_anonymous','survey');
    $recordedLabel=$this->objLanguage->languageText('mod_survey_recorded','survey');
    $singleLabel=$this->objLanguage->languageText('mod_survey_single','survey');
    $multipleLabel=$this->objLanguage->languageText('mod_survey_multiple','survey');
    $viewLabel=$this->objLanguage->languageText('mod_survey_viewresults','survey');
    $noviewLabel=$this->objLanguage->languageText('mod_survey_noresults','survey');
    $backLabel=$this->objLanguage->languageText('word_back');

// set up code to text elements
    $array=array('item'=>strtolower($surveyLabel));
    $heading=$this->objLanguage->code2Txt('mod_survey_view','survey',$array);

// set up data
    $arrSurveyData=$this->dbSurvey->getSurvey($surveyId);
    $arrSurveyData=$arrSurveyData['0'];
    $arrSurveyData['survey_id']=$arrSurveyData['id'];

// set up data variables
    $surveyName=$arrSurveyData['survey_name'];
    $startDate=$this->formatDate($arrSurveyData['start_date']);
    $endDate=$this->formatDate($arrSurveyData['end_date']);
    $responseMaximum=$arrSurveyData['max_responses'];
    $recordedResponses=$arrSurveyData['recorded_responses'];
    $singleResponses=$arrSurveyData['single_responses'];
    $viewResults=$arrSurveyData['view_results'];
    $introductionLabel=$arrSurveyData['intro_label'];
    $introductionText=stripslashes($arrSurveyData['intro_text']);
    $thankyouLabel=stripslashes($arrSurveyData['thanks_label']);
    $thankyouText=stripslashes($arrSurveyData['thanks_text']);

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$heading;
    $objHeader->type=1;
    echo $objHeader->show().'<hr />';

// set up html elements
    if($recordedResponses==1){
        $respondentLabel=$recordedLabel;
    }else{
        $respondentLabel=$anonymousLabel;
    }

    if($singleResponses==1){
        $responsesLabel=$singleLabel;
    }else{
        $responsesLabel=$multipleLabel;
    }

    if($viewResults==1){
        $resultsLabel=$viewLabel;
    }else{
        $resultsLabel=$noviewLabel;
    }

// set up table
    $objTable = new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $objTable->startRow();
    $objTable->addCell('<b>'.$nameLabel.'</b>','45%','','','odd','');
    $objTable->addCell($surveyName,'','','','odd','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$startLabel.'</b>','','','','even','');
    $objTable->addCell($startDate,'','','','even','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$endLabel.'</b>','','','','odd','');
    $objTable->addCell($endDate,'','','','odd','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$maximumLabel.'</b>','','','','even','');
    $objTable->addCell($responseMaximum,'','','','even','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$respondentLabel.'</b>','','','','odd','colspan="2"');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$responsesLabel.'</b>','','','','even','colspan="2"');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$resultsLabel.'</b>','','','','odd','colspan="2"');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$introductionLabel.'</b>','','','','even','');
    $objTable->addCell($introductionText,'','','','even','');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$thankyouLabel.'</b>','','','','odd','');
    $objTable->addCell($thankyouText,'','','','odd','');
    $objTable->endRow();

    $str=$objTable->show();
    echo $str;

// set up exit link
    $objLink=new link('javascript:history.back()');
    $objLink->link=$backLabel;
    echo '<hr /><br />'.$objLink->show();
?>