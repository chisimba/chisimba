<?php


$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');


$htmlheading = new htmlheading();
$htmlheading->type = 1;
$htmlheading->str = $this->objLanguage->languageText('mod_learningcontent_deletechapter','learningcontent', 'Delete Chapter').': '.htmlentities($chapter['chaptertitle']);

echo $htmlheading->show();

$form = new form('deletepage', $this->uri(array('action'=>'deletechapterconfirm')));


if ($numPages > 0) {
    $warning = $this->objLanguage->languageText('mod_learningcontent_chapterhaspagesalsotobedeleted', 'learningcontent', 'Warning - This chapter has [-NUM-]  pages which will also be deleted');
    
    $warning = str_replace('[-NUM-]', $numPages, $warning);
    $form->addToForm('<p class="warning">'.$warning.'.</p>');
}

$radio = new radio ('confirmation');
$radio->addOption('N',$this->objLanguage->languageText('mod_learningcontent_delchapterconfno','learningcontent', 'No - Do not delete this chapter'));
$radio->addOption('Y',$this->objLanguage->languageText('mod_learningcontent_delchapterconfyes','learningcontent', 'Yes - Delete this chapter'));
$radio->setSelected('N');
$radio->setBreakSpace('</p><p>');

$form->addToForm('<p>'.$radio->show().'</p>');

$button = new button ('confirm', $this->objLanguage->languageText('mod_learningcontent_confirmdelcontextpages','learningcontent', 'Confirm Delete'));
$button->setToSubmit();

$hiddeninput = new hiddeninput('id', $id);

$form->addToForm('<p>'.$button->show().$hiddeninput->show().'</p>');

$hiddeninput = new hiddeninput('context', $this->contextCode);
$form->addToForm($hiddeninput->show().'</p>');

$hiddeninput = new hiddeninput('context', $this->contextCode);
$form->addToForm($hiddeninput->show());

$objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
echo $objHighlightLabels->show();

$featurebox = $this->newObject('featurebox', 'navigation');
echo $featurebox->show($this->objLanguage->languageText('mod_learningcontent_deletechapterconf','learningcontent', 'Are you sure you want to delete this chapter').'?', $form->show());
?>
