<?php

$this->loadclass('link', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$gtype = new dropdown('type');
$gtype->addOption("Sponsorship");
$gtype->addOption("Individual");
$gtype->addOption("Group");


$form = new form('filterform', $this->uri(array('action' => 'filterbygifttype')));

$button = new button('addgift', "View");
$button->setToSubmit();
$form->addToForm($gtype->show());
$form->addToForm('<br/>'.$button->show());

echo $form->show();
?>
