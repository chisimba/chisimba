<?php
$this->loadClass('htmlheading','htmlelements');

$objHead = new htmlheading();
$objHead->str=$this->objLanguage->languageText('mod_assignment_submittedassignments', 'assignment', 'Submitted Assignments');
$objHead->type=1;

echo $objHead->show();

$assgnFunctions = $this->objAssignmentFunctions->displayAssignment();
$this->objLink->link($this->uri(array('action'=>'home')));
$this->objLink->link=$this->objLanguage->languageText('mod_assignment_backtolist', 'assignment', 'Back to List of Assignments');

echo $assgnFunctions.$this->objLink->show();

?>
