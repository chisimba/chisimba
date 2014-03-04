<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

if ($mode == 'edit') {
    $formaction = 'updatechapter';
    echo '<h1>'.$this->objLanguage->languageText('mod_learningcontent_editchapter','learningcontent').': '.$chapter['chaptertitle'].'</h1>';
} else {
    echo '<h1>'.$this->objLanguage->languageText('mod_learningcontent_addnewchapterin','learningcontent').' '.$this->objContext->getTitle().'</h1>';
    $formaction = 'savechapter';
}
    //echo '<p>Todo: Allow User to place order of chapter</p>';
    
$form = new form ('addchapter', $this->uri(array('action'=>$formaction)));
$table = $this->newObject('htmltable', 'htmlelements');

$title = new textinput('chapter');
$title->size = 60;

if ($mode == 'edit') {
    $title->value = $chapter['chaptertitle'];
}

$label = new label ($this->objLanguage->languageText('mod_learningcontent_chaptertitle','learningcontent'), 'input_chapter');
$table->startRow();
$table->addCell($label->show(), 150);
$table->addCell($title->show());
$table->endRow();

$label = new label ($this->objLanguage->languageText('mod_learningcontent_aboutchapter_introduction','learningcontent'), 'input_aboutchapter');
$htmlArea = $this->newObject('htmlarea', 'htmlelements');
$htmlArea->name = 'intro';
$htmlArea->context = TRUE;

if ($mode == 'edit') {
    $htmlArea->value = $chapter['introduction'];
}

$table->startRow();
$table->addCell($label->show());
$table->addCell($htmlArea->show());
$table->endRow();


$radio = new radio ('visibility');
$radio->addOption('Y', ' '.$this->objLanguage->languageText('word_yes','system', 'Yes'));
$radio->addOption('N', ' '.$this->objLanguage->languageText('word_no','system', 'No'));
$radio->addOption('I', ' '.$this->objLanguage->languageText('mod_learningcontent_onlyshowintroduction','learningcontent'));

if ($mode == 'edit') {
    $radio->setSelected($chapter['visibility']);
} else {
    $radio->setSelected('Y');
}
$radio->setBreakSpace(' &nbsp; ');

$table->startRow();
$table->addCell($this->objLanguage->code2Txt('mod_learningcontent_visibletostudents','learningcontent'));
$table->addCell($radio->show());
$table->endRow();


$form->addToForm($table->show());


$button = new button('submitbutton', $this->objLanguage->languageText('mod_learningcontent_chapter','learningcontent'));
$button->setToSubmit();
$form->addToForm($button->show());

if ($mode == 'edit') {
    $hiddeninput = new hiddeninput('id', $id);
    $form->addToForm($hiddeninput->show());
    
    $hiddeninput = new hiddeninput('chaptercontentid', $chapter['id']);
    $form->addToForm($hiddeninput->show());
    
    $hiddeninput = new hiddeninput('contextchapterid', $chapter['contextchapterid']);
    $form->addToForm($hiddeninput->show());
    
}

echo $form->show();
$chapterlisturl = $this->uri(array('action'=>'chapterlistastree','contextcode'=>$this->contextCode));
$viewchapterurl = $this->uri(array('action'=>'viewchapter'));
?>
