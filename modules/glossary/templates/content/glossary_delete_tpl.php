<?php

/* A PHP template for the Home Page of the Glossary Module */

// Classes being used
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');

echo $header;

// Create Header Tag ' Add a word
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1;
$this->objH->str=$objLanguage->languageText('mod_glossary_delete', 'glossary').' '.$record['term'].'?';
echo $this->objH->show();

// Start of Form
$addForm = new form('deleteWord', $this->uri(array(
		'module'=>'glossary',
		'action'=>'deleteconfirm',
		'id'=>$id
	)));

$addForm->addToForm($objLanguage->code2Txt('mod_glossary_confirmDelete', 'glossary', array('TERM'=>$record['term']))); //$objLanguage->languageText('mod_glossary_confirmDelete', 'glossary').' "'.$record['term'].'"'

$yesButton = new button('delete', $objLanguage->languageText('word_yes'));
$yesButton->setToSubmit();
$noButton = new button('delete', $objLanguage->languageText('word_no'));
$noButton->setToSubmit();

$addForm->addToForm(' <br /><br />');

$addForm->addToForm($yesButton);

$addForm->addToForm(' ');

$addForm->addToForm($noButton);
$addForm->displayType =3;

echo $addForm->show();


?>