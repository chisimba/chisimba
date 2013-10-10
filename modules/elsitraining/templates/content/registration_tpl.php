<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = 'Register for Training';

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$textinput = new textinput('staffnum');
$textinput->size = 12;

$table->startRow();
$table->addCell("<b>Staff number:</b>");
$table->addCell($textinput->show());
$table->endRow();

$titletype = new dropdown('title');
$titletype->addOption("Miss");
$titletype->addOption("Mr");
$titletype->addOption("Mrs");
$titletype->addOption("Prof");
$titletype->addOption("Dr");

$table->startRow();
$table->addCell("<b>Title:</b>");
$table->addCell($titletype->show());
$table->endRow();

$textinput = new textinput('firstname');
$textinput->size = 20;

$table->startRow();
$table->addCell("<b>First Name:</b>");
$table->addCell($textinput->show());
$table->endRow();

$textinput = new textinput('surname');
$textinput->size = 20;

$table->startRow();
$table->addCell("<b>Surname:</b>");
$table->addCell($textinput->show());
$table->endRow();

$textinput = new textinput('email');
$textinput->size = 30;

$table->startRow();
$table->addCell("<b>E-mail:</b>");
$table->addCell($textinput->show());
$table->endRow();

$textinput = new textinput('tel');
$textinput->size = 8;

$table->startRow();
$table->addCell("<b>Tel. extension:</b>");
$table->addCell($textinput->show());
$table->endRow();
$oneonone=true;

if ($oneonone) {
    $table->startRow();
    $table->addCell('<b>Preferred Start Time:</b>');
    $objDateTime = $this->getObject('dateandtime', 'utilities');
    $objDatePicker = $this->newObject('datepicker', 'htmlelements');
    $objDatePicker->name = 'prefstarttime';

    $table->addCell($objDatePicker->show());
    $table->endRow();

    $table->startRow();
    $table->addCell('<b>Preferred End Time</b>');
    $objDateTime = $this->getObject('dateandtime', 'utilities');
    $objDatePicker = $this->newObject('datepicker', 'htmlelements');
    $objDatePicker->name = 'prefendtime';

    $table->addCell($objDatePicker->show());
    $table->endRow();
    
    $textinput = new textinput('venue');
    $textinput->size = 30;

    $table->startRow();
    $table->addCell("<b>Venue:</b>");
    $table->addCell($textinput->show());
    $table->endRow();


}

if ($oneonone){
    $action = 'oneonone';    
}

if (!$oneonone){
    $action = 'scheduled';
}


$form = new form('registration', $this->uri(array('action' => $action)));
$form->addToForm($table->show());


$button = new button('register', 'Register');
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show());


echo $form->show();
?>
<!--




-->