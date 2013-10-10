<?php

//I need 2 drop down boxes, a form and a submit button
$button = $this->newObject('button', 'htmlelements');
$objCounsellorBox = $this->getObject('radio', 'htmlelements');
$form = $this->newObject('form' , 'htmlelements');


//setup the patient box
//$objPatientsBox->addFromDB($this->objDbImPres->getUsers($this->objUser->userId()),'Users', 'person', 'person' );
//print $objPatientsBox->show();
//setup the counsellor box
//$objCounsellorBox->addFromDB($this->objIMUsers->getAll(), 'Counsellor' ,'userid', 'userid');
$objCounsellorBox->name = "counsellorbox";
$objCounsellorBox->setBreakSpace('table');
$objCounsellorBox->cssClass = "radio1";

$r = "";
foreach ($this->objIMUsers->getAll() as $counsellor)
{
	$r .= '<div>';
	if($this->objUser->userId() != $counsellor['userid'])
	{
		$objCounsellorBox->addOption($counsellor['userid'],
									 "  ".$this->objUser->fullname($counsellor['userid']));
	}
}
$button->setToSubmit();
$button->value= " Reassign ";
$button->name = "reassignbutton";

$form->action=$this->uri(array('action'=>'reassign', 'patient' =>  $this->getParam('patient') ));
$form->setDisplayType(2);
$form->addToForm($objCounsellorBox);
$form->addToForm($button);

$str = '<div style="width:600px; margin-left:50px;margin-top:10px;">';
$str .= $form->show();
$str .= "</div>";

print $str;
//setup the form


//display everything



?>
