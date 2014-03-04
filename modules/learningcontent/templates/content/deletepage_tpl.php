<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$this->setVar('pageTitle', $this->objContext->getTitle().$this->objLanguage->languageText('mod_learningcontent_delcontextpages','learningcontent').htmlentities($page['menutitle']));


$title = $this->objLanguage->languageText('mod_learningcontent_delcontextpages','learningcontent')." ".htmlentities($page['menutitle']);


$form = new form('deletepage', $this->uri(array('action'=>'deletepageconfirm')));

$form->addToForm('<p><strong>'.$this->objLanguage->languageText('mod_learningcontent_delconf','learningcontent').'</strong></p>');

$radio = new radio ('confirmation');
$radio->addOption('N',$this->objLanguage->languageText('mod_learningcontent_delconfno','learningcontent'));
$radio->addOption('Y',$this->objLanguage->languageText('mod_learningcontent_delconfyes','learningcontent'));
$radio->setSelected('N');
$radio->setBreakSpace('</p><p>');

$form->addToForm('<p>'.$radio->show().'</p>');

$button = new button ('confirm', $this->objLanguage->languageText('mod_learningcontent_confirmdelcontextpages','learningcontent'));
$button->setToSubmit();

$hiddeninput = new hiddeninput('id', $page['id']);

$form->addToForm('<p>'.$button->show().$hiddeninput->show().'</p>');

$hiddeninput = new hiddeninput('context', $this->contextCode);
$form->addToForm($hiddeninput->show());

$objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
echo $objHighlightLabels->show();

$featurebox = $this->newObject('featurebox', 'navigation');
echo $featurebox->show($title, $form->show());

?>
