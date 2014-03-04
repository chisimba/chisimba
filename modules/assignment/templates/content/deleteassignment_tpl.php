<?php

//var_dump($assignment);

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_assignment_deleteassgn', 'assignment', 'Delete Assignment').' - '.$assignment['name'];



$header->type = 1;

$objDateTime = $this->getObject('dateandtime', 'utilities');

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('word_description', 'system', 'Description').'</strong>', 130);
$table->addCell($assignment['description'], NULL, NULL, NULL, NULL, ' colspan="3"');
$table->endRow();

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->code2Txt('mod_assignment_lecturer', 'assignment', NULL, '[-author-]').':</strong>', 130);
$table->addCell($this->objUser->fullName($assignment['userid']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_totalmark', 'assignment').'</strong>', 130);
$table->addCell($assignment['mark']);
$table->endRow();

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_openingdate', 'assignment', 'Opening Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($assignment['opening_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_percentyrmark', 'assignment', 'Percentage of year mark').':</strong>', 200, NULL, NULL, 'nowrap');
$table->addCell($assignment['percentage'].'%');
$table->endRow();

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($assignment['closing_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type').'</strong>', 130);
if ($assignment['format'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_assignment_online', 'assignment', 'Online'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_assignment_upload', 'assignment', 'Upload'));
}
$table->endRow();

echo $table->show();

$htmlHeader = new htmlHeading();
$htmlHeader->type = 1;
$htmlHeader->str = $this->objLanguage->languageText('mod_assignment_confirmdelete', 'assignment');
echo '<hr />'.$htmlHeader->show();

$form = new form ('deleteassignment', $this->uri(array('action'=>'deleteconfirm')));

$hiddenInput = new hiddeninput('id', $assignment['id']);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('randNumber', $randNumber);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('return', $this->getParam('return'));
$form->addToForm($hiddenInput->show());

$form->addToForm('<p>'.$this->objLanguage->languageText('mod_assignment_confirmdeleteassgn', 'assignment', 'Are you sure you want to delete this assignment').'?</p>');

$radio = new radio ('confirm');
$radio->addOption('N', $this->objLanguage->languageText('word_no', 'system', 'No'));
$radio->addOption('Y', $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
$radio->setBreakSpace(' &nbsp; ');
$radio->setSelected('N');

$form->addToForm('<p>'.$radio->show().'</p>');

$button = new button('confirmbutton', $this->objLanguage->languageText('word_confirm', 'system', 'Confirm'));
$button->setToSubmit();

$form->addToForm('<p>'.$button->show().'</p>');

echo $form->show();

$backLink = new link ($this->uri(NULL));
$backLink->link = 'Back to List of Assignments';

echo '<p>'.$backLink->show().'</p>';

?>