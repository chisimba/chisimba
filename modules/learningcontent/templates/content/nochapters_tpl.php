<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

//Width of the label column
$tWidth=150;


    echo '<h1>'.$this->objLanguage->languageText('mod_learningcontent_contextpagesfor','learningcontent')." ".$this->objContext->getTitle().' - '.$this->objLanguage->languageText('mod_learningcontent_createachapter','learningcontent').'</h1>';
    
    echo '<p>'.$this->objLanguage->languageText('mod_learningcontent_createchapterexplanation','learningcontent').'</p>';
    
$form = new form ('addchapter', $this->uri(array('action'=>'savechapter')));
$table = $this->newObject('htmltable', 'htmlelements');

$title = new textinput('chapter');
$title->size = 90;
$label = new label ($this->objLanguage->languageText('mod_learningcontent_chaptertitle','learningcontent'), 'input_chapter');
$table->startRow();
$table->addCell($label->show(), $tWidth, 'top', 'left');
$table->addCell($title->show());
$table->endRow();

$label = new label ($this->objLanguage->languageText('mod_learningcontent_aboutchapter_introduction','learningcontent'), 'input_aboutchapter');
$htmlArea = $this->newObject('htmlarea', 'htmlelements');
$htmlArea->name = 'intro';
$htmlArea->context = TRUE;
$table->startRow();
$table->addCell($label->show(), $tWidth, 'top', 'left');
$table->addCell($htmlArea->show());
$table->endRow();


$radio = new radio ('visibility');
$radio->addOption('Y', ' '.$this->objLanguage->languageText('word_yes','system', 'Yes'));
$radio->addOption('N', ' '.$this->objLanguage->languageText('word_no','system', 'No'));
$radio->addOption('I', ' '.$this->objLanguage->languageText('mod_learningcontent_onlyshowintroduction','learningcontent'));
$radio->setSelected('Y');
$radio->setBreakSpace(' &nbsp; ');

$table->startRow();
$table->addCell($this->objLanguage->code2Txt('mod_learningcontent_visibletostudents','learningcontent'), $tWidth, 'top', 'left');
$table->addCell($radio->show());
$table->endRow();


$form->addToForm($table->show());


$button = new button('submitbutton', $this->objLanguage->languageText('mod_learningcontent_chapter','learningcontent'));
$button->setToSubmit();
$form->addToForm($button->show());

//$form->addRule('chapter');

echo $form->show();

?>
