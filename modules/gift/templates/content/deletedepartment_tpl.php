<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');


$form = new form('deletedepartment', $this->uri(array('action'=>'deletedepartment')));

$form->addToForm('<p><strong class="deletecontent">Confirm delete</strong></p>');

$radio = new radio ('confirmation');
$radio->addOption('N',"No, dont delete");
$radio->addOption('Y',"Yes, delete");
$radio->setSelected('N');
$radio->setBreakSpace('</p><p>');

$form->addToForm('<p >'.$radio->show().'</p>');

$button = new button ('confirm', "Continue");
$button->setToSubmit();

$hiddeninput = new hiddeninput('id', $departmentid);

$form->addToForm('<p>'.$button->show().$hiddeninput->show().'</p>');


$objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
echo $objHighlightLabels->show();

$featurebox = $this->newObject('featurebox', 'navigation');
echo $featurebox->show($title, $form->show());

?>