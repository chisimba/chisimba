<?php

//var_dump($practical);

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_practicals_deleteassgn', 'practicals', 'Delete Practical').' - '.$practical['name'];



$header->type = 1;

$objDateTime = $this->getObject('dateandtime', 'utilities');

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('word_description', 'system', 'Description').'</strong>', 130);
$table->addCell($practical['description'], NULL, NULL, NULL, NULL, ' colspan="3"');
$table->endRow();

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->code2Txt('mod_practicals_lecturer', 'practicals', NULL, '[-author-]').':</strong>', 130);
$table->addCell($this->objUser->fullName($practical['userid']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_totalmark', 'practicals').'</strong>', 130);
$table->addCell($practical['mark']);
$table->endRow();

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_openingdate', 'practicals', 'Opening Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($practical['opening_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_percentyrmark', 'practicals', 'Percentage of year mark').':</strong>', 200, NULL, NULL, 'nowrap');
$table->addCell($practical['percentage'].'%');
$table->endRow();

$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_closingdate', 'practicals', 'Closing Date').'</strong>', 130);
$table->addCell($objDateTime->formatDate($practical['closing_date']));
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_practicals_practicaltype', 'practicals', 'Practical Type').'</strong>', 130);
if ($practical['format'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_practicals_online', 'practicals', 'Online'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_practicals_upload', 'practicals', 'Upload'));
}
$table->endRow();

echo $table->show();

$htmlHeader = new htmlHeading();
$htmlHeader->type = 1;
$htmlHeader->str = $this->objLanguage->languageText('mod_practicals_confirmdelete', 'practicals');
echo '<hr />'.$htmlHeader->show();

$form = new form ('deletepractical', $this->uri(array('action'=>'deleteconfirm')));

$hiddenInput = new hiddeninput('id', $practical['id']);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('randNumber', $randNumber);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('return', $this->getParam('return'));
$form->addToForm($hiddenInput->show());

$form->addToForm('<p>'.$this->objLanguage->languageText('mod_practicals_confirmdeleteassgn', 'practicals', 'Are you sure you want to delete this practical').'?</p>');

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
$backLink->link = 'Back to List of Practicals';

echo '<p>'.$backLink->show().'</p>';

?>