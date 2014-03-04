<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$form = new form('searchbydatesform', $this->uri(array('action' => $action)));

$objDateTime = $this->getObject('dateandtime', 'utilities');
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'startdate';
$content = "Date From: &nbsp;" . $objDatePicker->show();

$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'enddate';
$content .= "Date To: &nbsp;" . $objDatePicker->show();

$form->addToForm($content);
$button = new button('view', 'View');
$button->setToSubmit();

$form->addToForm(' ' . $button->show());

$fs = new fieldset();
$fs->setLegend("View by date");
$fs->addContent($form->show());
echo $fs->show();
?>
