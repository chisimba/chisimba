<?php
/* -------------------- export template for testadmin ----------------*/
// security check-must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * @package mcqtests
 * Export template for testadmin
 * Author Kevin Cyster
 *
 */
// set up layout template
$this->setLayoutTemplate('mcqtests_layout_tpl.php');

// set up html elements
$objHeader = &$this->loadClass('htmlheading', 'htmlelements');
$objTable = &$this->loadClass('htmltable', 'htmlelements');
$objLink = &$this->loadClass('link', 'htmlelements');
$objButton = &$this->loadClass('button', 'htmlelements');
$objRadio = &$this->loadClass('radio', 'htmlelements');
$objForm = &$this->loadClass('form', 'htmlelements');

// set up language items
$backLabel = $this->objLanguage->languageText('word_back');
$exportLabel = $this->objLanguage->languageText('mod_mcqtests_export', 'mcqtests');
$selectLabel = $this->objLanguage->languageText('mod_mcqtests_selectexport', 'mcqtests');
$answerLabel = $this->objLanguage->languageText('mod_mcqtests_exportanswers', 'mcqtests');
$resultsLabel = $this->objLanguage->languageText('mod_mcqtests_exportresults', 'mcqtests');
$submitLabel = $this->objLanguage->languageText('word_submit', 'system', 'Submit');
$errorLabel = $this->objLanguage->languageText('mod_mcqtests_errorselect', 'mcqtests');
$objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
echo $objHighlightLabels->show();

// set up heading
$this->setVarByRef('heading', $exportLabel);

// set up htmlelements
$objRadio = new radio('exporttype');
$objRadio->addOption('answers', $answerLabel);
$objRadio->addOption('results', $resultsLabel);
$objRadio->setBreakSpace('<br />');
$optionRadio = $objRadio->show();

$objButton = new button('submitbutton', $submitLabel);
$objButton->setToSubmit();
$submitButton = $objButton->show();

// set up table
$objTable = new htmltable();
$objTable->cellspacing = '2';
$objTable->cellpadding = '2';
$objTable->startRow();
$objTable->addCell('<b>'.$selectLabel.'</b>', '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($optionRadio, '', '', '', '', '');
$objTable->endRow();
$optionTable = $objTable->show();

$objForm = new form('optionform', $this->uri(array(
    'action' => 'doexport',
    'testId' => $testId
)));
$objForm->addToForm($optionTable.'<br />'.$submitButton);
$objForm->addRule('exporttype', $errorLabel, 'required');
$optionForm = $objForm->show();
echo $optionForm;

// set up rerurn link
$objLink = new link("javascript:history.back()");
$objLink->link = $backLabel;
$backLink = $objLink->show();
echo "<br />".$backLink;
?>