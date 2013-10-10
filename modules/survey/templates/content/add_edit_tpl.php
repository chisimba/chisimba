<?php

/* -------------------- survey extends controller ---------------- */

// security check-must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
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
$objHeader = &$this->loadClass('htmlheading', 'htmlelements');
$objTable = &$this->loadClass('htmltable', 'htmlelements');
$objIcon = &$this->newObject('geticon', 'htmlelements');
$objLink = &$this->loadClass('link', 'htmlelements');
$objRadio = &$this->loadClass('radio', 'htmlelements');
$objInput = &$this->loadClass('textinput', 'htmlelements');
$objButton = &$this->loadClass('button', 'htmlelements');
$objForm = &$this->loadClass('form', 'htmlelements');
$objEditor = $this->newObject('htmlarea', 'htmlelements');
$objPopupcal = $this->newObject('datepickajax', 'popupcalendar');

// set up language items
$nameLabel = $this->objLanguage->languageText('mod_survey_surveyname', 'survey');
$startLabel = $this->objLanguage->languageText('mod_survey_startdate', 'survey');
$endLabel = $this->objLanguage->languageText('mod_survey_enddate', 'survey');
$dateLabel = $this->objLanguage->languageText('mod_survey_date', 'survey');
$surveyLabel = $this->objLanguage->languageText('mod_survey_survey', 'survey');
$maximumLabel = $this->objLanguage->languageText('mod_survey_maximum', 'survey');
$recordingLabel = $this->objLanguage->languageText('mod_survey_recording', 'survey');
$anonymousLabel = $this->objLanguage->languageText('mod_survey_anonymous', 'survey');
$recordedLabel = $this->objLanguage->languageText('mod_survey_recorded', 'survey');
$entriesLabel = $this->objLanguage->languageText('mod_survey_entries', 'survey');
$singleLabel = $this->objLanguage->languageText('mod_survey_single', 'survey');
$multipleLabel = $this->objLanguage->languageText('mod_survey_multiple', 'survey');
$resultsLabel = $this->objLanguage->languageText('mod_survey_results', 'survey');
$loginLabel = $this->objLanguage->languageText('mod_survey_login', 'survey', 'Login');
$viewLabel = $this->objLanguage->languageText('mod_survey_viewresults', 'survey');
$noviewLabel = $this->objLanguage->languageText('mod_survey_noresults', 'survey');
$nologinLabel = $this->objLanguage->languageText('mod_survey_nologin','survey', 'Users dont need to login');
$yesloginLabel = $this->objLanguage->languageText('mod_survey_yeslogin','survey', 'Users must login');
$submitLabel = $this->objLanguage->languageText('word_submit');
$errorLabel = $this->objLanguage->languageText('mod_survey_creationerrors', 'survey');
$introLabel = $this->objLanguage->languageText('mod_survey_introheading', 'survey');
$intronoteLabel = $this->objLanguage->languageText('mod_survey_introduction', 'survey');
$thanksLabel = $this->objLanguage->languageText('mod_survey_thanksheading', 'survey');
$thanksnoteLabel = $this->objLanguage->languageText('mod_survey_thankyou', 'survey');
$introduction = $this->objLanguage->languageText('mod_survey_wordintroduction', 'survey');
$introductionNote = $this->objLanguage->languageText('mod_survey_introductionnote', 'survey');
$thankyou = $this->objLanguage->languageText('mod_survey_phrasethankyou', 'survey');
$thankyouNote = $this->objLanguage->languageText('mod_survey_thankyounote', 'survey');

// set up code to text elements
$array = array('item' => strtolower($surveyLabel));
if ($mode == 'add') {
    $heading = $this->objLanguage->code2Txt('mod_survey_add', 'survey', $array);
} else {
    $heading = $this->objLanguage->code2Txt('mod_survey_edit', 'survey', $array);
}
$returnLabel = $this->objLanguage->code2Txt('mod_survey_return', 'survey', $array);

// set up data
if (!$error) {
    if ($mode == 'add') {
        $arrSurveyData = array();
        $arrSurveyData['survey_id'] = '';
        $arrSurveyData['survey_name'] = '';
        $arrSurveyData['start_date'] = date('Y-m-d');
        $arrSurveyData['end_date'] = date('Y-m-d', time() + (7 * 24 * 60 * 60));
        $arrSurveyData['max_responses'] = '';
        $arrSurveyData['recorded_responses'] = '0';
        $arrSurveyData['single_responses'] = '0';
        $arrSurveyData['view_results'] = '0';
        $arrSurveyData['login'] = '0';
        $arrSurveyData['intro_label'] = $introduction;
        $arrSurveyData['intro_text'] = $introductionNote;
        $arrSurveyData['thanks_label'] = $thankyou;
        $arrSurveyData['thanks_text'] = $thankyouNote;
    } elseif ($mode == 'edit') {
        $arrSurveyData = $this->dbSurvey->getSurvey($surveyId);
        $arrSurveyData = $arrSurveyData['0'];
        $arrSurveyData['survey_id'] = $arrSurveyData['id'];
    }
} else {
    $arrErrorMsg = $this->getSession('error');
    $arrSurveyData = $this->getSession('survey');
}
//error_log(var_export($arrSurveyData, true))
// set up data variables
$surveyId = $arrSurveyData['survey_id'];
$surveyName = stripslashes($arrSurveyData['survey_name']);
$startDate = $arrSurveyData['start_date'];
$endDate = $arrSurveyData['end_date'];
$responseMaximum = $arrSurveyData['max_responses'];
$recordedResponses = $arrSurveyData['recorded_responses'];
$singleResponses = $arrSurveyData['single_responses'];
$viewResults = $arrSurveyData['view_results'];
$login = $arrSurveyData['login'];
$introductionLabel = stripslashes($arrSurveyData['intro_label']);
$introductionText = stripslashes($arrSurveyData['intro_text']);
$thankyouLabel = stripslashes($arrSurveyData['thanks_label']);
$thankyouText = stripslashes($arrSurveyData['thanks_text']);

// set up heading
$objHeader = new htmlheading();
$objHeader->str = $heading;
$objHeader->type = 1;
echo $objHeader->show() . '<hr />';

if ($error) {
    $objHeader->str = '<font class="error">' . $errorLabel . '</font><hr />';
    $objHeader->type = 3;
    echo $objHeader->show();
}

// set up html elements
$objInput = new textinput('survey_id', $surveyId, '', '85');
$objInput->fldType = 'hidden';
$surveyIdText = $objInput->show();

$objInput = new textinput('survey_name', $surveyName, '', '85');
$nameText = $objInput->show();

$startField = $objPopupcal->show('start_date', 'no', 'no', $startDate);

$endField = $objPopupcal->show('end_date', 'no', 'no', $endDate);

$objInput = new textinput('max_responses', $responseMaximum, '', '8');
$objInput->extra = 'maxlength = "7"';
$maximumText = $objInput->show();

$objRadio = new radio('recorded_responses');
$objRadio->addOption(0, $anonymousLabel);
$objRadio->addOption(1, $recordedLabel);
$objRadio->setSelected($recordedResponses);
$objRadio->setBreakSpace('<br />');
$recordedRadio = $objRadio->show();

$objRadio = new radio('single_responses');
$objRadio->addOption(0, $multipleLabel);
$objRadio->addOption(1, $singleLabel);
$objRadio->setSelected($singleResponses);
$objRadio->setBreakSpace('<br />');
$singleRadio = $objRadio->show();

$objRadio = new radio('view_results');
$objRadio->addOption(0, $noviewLabel);
$objRadio->addOption(1, $viewLabel);
$objRadio->setSelected($viewResults);
$objRadio->setBreakSpace('<br />');
$resultsRadio = $objRadio->show();

$objRadio = new radio('login');
$objRadio->addOption(0, $nologinLabel);
$objRadio->addOption(1, $yesloginLabel);
$objRadio->setSelected($login);
$objRadio->setBreakSpace('<br />');
$loginRadio = $objRadio->show();

$objInput = new textinput('intro_label', $introductionLabel, '', '85');
$introText = $objInput->show();

$objEditor->init('intro_text', $introductionText, '', '', NULL);
$objEditor->height = '150px';
$objEditor->setBasicToolBar();
$introArea = $objEditor->show();

$objInput = new textinput('thanks_label', $thankyouLabel, '', '85');
$thanksText = $objInput->show();

$objEditor->init('thanks_text', $thankyouText, '', '', NULL);
$objEditor->height = '150px';
$objEditor->setBasicToolBar();
$thanksArea = $objEditor->show();

// set up table
$objTable = new htmltable();
$objTable->cellspacing = '2';
$objTable->cellpadding = '2';

$objTable->startRow();
if ($error && isset($arrErrorMsg['survey_name'])) {
    $nameLabel.='<br /><font class="error"><b>' . stripslashes($arrErrorMsg['survey_name']) . '</b></font>';
    $objTable->addCell($nameLabel, '', '', '', 'odd', '');
} else {
    $objTable->addCell($nameLabel, '', '', '', 'odd', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($surveyIdText . $nameText, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
if ($error && isset($arrErrorMsg['start_date'])) {
    $startLabel.='<br /><font class="error"><b>' . stripslashes($arrErrorMsg['start_date']) . '</b></font>';
    $objTable->addCell($startLabel, '', '', '', 'even', '');
} else {
    $objTable->addCell($startLabel, '', '', '', 'even', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($startField, '', '', '', 'even', '');
$objTable->endRow();
$objTable->startRow();
if ($error && isset($arrErrorMsg['end_date'])) {
    $endLabel.='<br /><font class="error"><b>' . $arrErrorMsg['end_date'] . '</b></font>';
    $objTable->addCell($endLabel, '', '', '', 'odd', '');
} else {
    $objTable->addCell($endLabel, '', '', '', 'odd', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($endField, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
if ($error && isset($arrErrorMsg['max_responses'])) {
    $maximumLabel.='<br /><font class="error"><b>' . $arrErrorMsg['max_responses'] . '</b></font>';
    $objTable->addCell($maximumLabel, '', '', '', 'even', '');
} else {
    $objTable->addCell($maximumLabel, '', '', '', 'even', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($maximumText, '', '', '', 'even', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($recordingLabel, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($recordedRadio, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($entriesLabel, '', '', '', 'even', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($singleRadio, '', '', '', 'even', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($resultsLabel, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($resultsRadio, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($loginLabel, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($loginRadio, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
if ($error && isset($arrErrorMsg['intro_label'])) {
    $introLabel.='<br /><font class="error"><b>' . stripslashes($arrErrorMsg['intro_label']) . '</b></font>';
    $objTable->addCell($introLabel, '', '', '', 'even', '');
} else {
    $objTable->addCell($introLabel, '', '', '', 'even', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($introText, '', '', '', 'even', '');
$objTable->endRow();
$objTable->startRow();
if ($error && isset($arrErrorMsg['intro_text'])) {
    $intronoteLabel.='<br /><font class="error"><b>' . stripslashes($arrErrorMsg['intro_text']) . '</b></font>';
    $objTable->addCell($intronoteLabel, '', '', '', 'odd', '');
} else {
    $objTable->addCell($intronoteLabel, '', '', '', 'odd', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($introArea, '', '', '', 'odd', '');
$objTable->endRow();
$objTable->startRow();
if ($error && isset($arrErrorMsg['thanks_label'])) {
    $thanksLabel.='<br /><font class="error"><b>' . stripslashes($arrErrorMsg['thanks_label']) . '</b></font>';
    $objTable->addCell($thanksLabel, '', '', '', 'even', '');
} else {
    $objTable->addCell($thanksLabel, '', '', '', 'even', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($thanksText, '', '', '', 'even', '');
$objTable->endRow();
$objTable->startRow();
if ($error && isset($arrErrorMsg['thanks_text'])) {
    $thanksnoteLabel.='<br /><font class="error"><b>' . stripslashes($arrErrorMsg['thanks_text']) . '</b></font>';
    $objTable->addCell($thanksnoteLabel, '', '', '', 'odd', '');
} else {
    $objTable->addCell($thanksnoteLabel, '', '', '', 'odd', '');
}
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($thanksArea, '', '', '', 'odd', '');
$objTable->endRow();

// set up submit button
$objButton = new button('submitButton', $submitLabel);
$objButton->extra = ' onclick="javascript:
        this.disabled=\'disabled\';
        document.getElementById(\'form_addForm\').submit();
    "';
$submitButton = $objButton->show();

// set up form
$objForm = new form('addForm', $this->uri(array('action' => 'validatesurvey', 'mode' => $mode)));
$objForm->addToForm($objTable->show());
$objForm->addToForm('<br />' . $submitButton);
$str = $objForm->show();
echo $str;

// set up exit link
$objLink = new link($this->uri(array(), 'survey'));
$objLink->link = $returnLabel;
echo '<hr /><br />' . $objLink->show();
?>