<?php

$link = $this->getObject('link','htmlelements');
$objThumbnail = & $this->getObject('thumbnails','filemanager');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$h = $this->getObject('htmlheading','htmlelements');
$form = $this->getObject('form', 'htmlelements');
$form->action = $this->uri(array('action' => 'saveedit', 'commentid' => $comment['id']));

$name = new textinput('name', $comment['name']);
$form->addRule('name','Please suppy a name!', 'required');

$table = new htmltable();
$table->width = '200';
$table->startRow();
$table->addCell('<label for="name">Name:</label>');
$table->addCell($name->show());
$table->endRow();

$email = new textinput('email', $comment['email']);
$table->startRow();
$table->addCell('<label for="email">E-Mail:</label>');
$table->addCell($email->show());
$table->endRow();

$website = new textinput('website', $comment['website']);
$table->startRow();
$table->addCell('<label for="website">Site:</label>');
$table->addCell($website->show());
$table->endRow();

$commentdate = new textinput('commentdate', $comment['commentdate']);
$table->startRow();
$table->addCell('<label for="website">Date/Time:</label>');
$table->addCell($commentdate->show());
$table->endRow();

$commentField = new textarea('comment',$comment['comment']);

$table->startRow();
$table->addCell('<label for="comment">Comment:</label>');
$table->addCell($commentField->show());
$table->endRow();

$button = new button();
$button->value = 'Save Comment';
$button->setToSubmit();

$form->addToForm('<h3>Edit comment</h3>'.$table->show());
$form->addToForm('<br/>'.$button->show());

echo $form->show();

?>