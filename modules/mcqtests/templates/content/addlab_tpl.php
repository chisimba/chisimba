<?php
/**
 * Template for adding a new test or editing an existing one.
 * @package mcqtests
 * @param array $data The details of the test to be edited.
 * @param string $mode Add or edit
 */
// set up layout template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');

// set up html elements
$objTable = &$this->loadClass('htmltable', 'htmlelements');
$objLink = &$this->loadClass('link', 'htmlelements');
$objText = &$this->loadClass('textinput', 'htmlelements');
$objButton = &$this->loadClass('button', 'htmlelements');
$objForm = &$this->loadClass('form', 'htmlelements');

// set up language items
$addHeading = $this->objLanguage->languageText('mod_mcqtests_addlab', 'mcqtests');
$submitLabel = $this->objLanguage->languageText('word_submit', 'system', 'Submit');
$requiredLabel = $this->objLanguage->languageText('mod_mcqtests_labrequired', 'mcqtests');
$helpLabel = $this->objLanguage->languageText('mod_mcqtests_labfile', 'mcqtests');

//separated blocks of text
$helpLabel1 = '<p>'.$this->objLanguage->languageText('mod_mcqtests_labfilewords1', 'mcqtests').'</p>';
$helpLabel2 = '<p>'.$this->objLanguage->languageText('mod_mcqtests_labfilewords2', 'mcqtests').'</p>';
$helpLabel3 =  $this->objLanguage->languageText('mod_mcqtests_labfilewords3', 'mcqtests');
$helpLabel4 = '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->objLanguage->languageText('mod_mcqtests_labfilewords4', 'mcqtests');
$helpLabel5 = '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->objLanguage->languageText('mod_mcqtests_labfilewords5', 'mcqtests');
$helpLabel6 = '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->objLanguage->languageText('mod_mcqtests_labfilewords6', 'mcqtests');
$helpLabel7 = '&nbsp;&nbsp;&nbsp;&nbsp;'.$this->objLanguage->languageText('mod_mcqtests_labfilewords7', 'mcqtests');

$helpLabel .= $helpLabel1.$helpLabel2.$helpLabel3.':'.'<p>'.$helpLabel4.'<br />'.$helpLabel5.'<br />'.$helpLabel6.'<br />'.$helpLabel7.'<br />'.'</p>';

$errorLabel = $this->objLanguage->languageText('mod_mcqtests_laberror', 'mcqtests');
$backLabel = $this->objLanguage->languageText('word_back');

// set up heading
$this->setVarByRef('heading', $addHeading);
$objText = new textinput('comLab', '', 'file', 50);
$labText = $objText->show();
$objTable = new htmltable();
$objTable->cellspacing = 2;
$objTable->cellpadding = 2;
$objTable->startRow();
$objTable->addCell($helpLabel, '', '', '', '', '');

$objTable->endRow();
if ($error) {
    $objTable->startRow();
    $objTable->addCell("<b>".$errorLabel."</b>", '', '', '', 'error', '');
    $objTable->endRow();
}
$objTable->startRow();
$objTable->addCell($labText, '', '', '', '', '');
$objTable->endRow();
$labTable = $objTable->show();
$objButton = new button('submitbutton', $submitLabel);
$objButton->setToSubmit();
$submitButton = $objButton->show();
$objForm = new form('addlab', $this->uri(array(
    'action' => 'applyaddlab',
    'id' => $id,
    'mode' => $mode
)));
$objForm->addToForm($labTable."<br />".$submitButton);
$objForm->addRule('comLab', $requiredLabel, 'required');
$objForm->extra = "enctype='multipart/form-data'";
$labForm = $objForm->show();
echo $labForm;
// set up rerurn link
$objLink = new link("javascript:history.back()");
$objLink->link = $backLabel;
$backLink = $objLink->show();
echo "<br />".$backLink;
?>
