<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');



$editTextForm = new form ('addtext', $this->uri(array('action'=>'updatemenu_text')));
$label = new label ('Text: ', 'input_text');
$text = new textinput('text');
$text->value = htmlentities($item['itemvalue']);

$hiddeninput = new hiddeninput('id', $id);

echo '<h1>'.$this->objLanguage->languageText('mod_news_editmenuitem', 'news', 'Edit Menu Item').': '.$item['itemvalue'].'</h1>';

$button = new button ('adddividerbutton', $this->objLanguage->languageText('mod_news_updatetext', 'news', 'Update Text'));
$button->setToSubmit();

$editTextForm->addToForm($label->show().$text->show().'<br />');
$editTextForm->addToForm($button->show());
$editTextForm->addToForm($hiddeninput->show());

echo $editTextForm->show();

?>