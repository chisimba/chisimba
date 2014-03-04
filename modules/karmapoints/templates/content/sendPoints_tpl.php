<?php
//Load the form class
$this->loadClass('form','htmlelements');
//Load the button object
$this->loadClass('button', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//load the table class
$this->loadClass('htmlTable', 'htmlelements');
//load the Heading class
$this->loadClass('htmlHeading', 'htmlelements');
//load the dropdown class
$this->loadClass('dropdown', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput','htmlelements');
//Load the link class
$this->loadClass('link','htmlelements');


//Create form
$objsendForm = new form('karmapoints', $this->uri(array('action'=>'donate')));

//Create a table for the header
$objheadTable = new htmlTable('karmapoints');

//create an element for the heading label
$objkarmaSendLabel = new htmlHeading();
$objkarmaSendLabel->str=$this->objLanguage->languageText('mod_karmapoints_karmaSendLabel', 'karmapoints');
$objkarmaSendLabel->type=2;

//Add a heading row in the table
$objheadTable->startHeaderRow();
$objheadTable->addHeaderCell("<br />", '30%');
$objheadTable->addHeaderCell($objkarmaSendLabel->show(), '70%');
$objheadTable->endHeaderRow();

if(isset($notEnough))
{
	$objheadTable->startRow();
	$objheadTable->addCell("<span class='error'>$notEnough</span>");
	$objheadTable->addCell("<br />");
	$objheadTable->endRow();
}
else
{
	$objheadTable->startRow();
	$objheadTable->addCell("<br />");
	$objheadTable->addCell("<br />");
	$objheadTable->endRow();
}

$objsendForm->addToForm($objheadTable->show(). "<br />");

$objpointsTable = new htmlTable('karmapoints');
$objpointsTable->width='50%';
//$objpointsTable->border=2;

$objsendPointsHeadLabel = new htmlHeading();
$objsendPointsHeadLabel->str=$this->objLanguage->languageText('mod_karmapoints_typePointsHeadLabel', 'karmapoints');
$objsendPointsHeadLabel->type=3;

//Add a heading row in the table
$objpointsTable->startHeaderRow();
$objpointsTable->addHeaderCell($objsendPointsHeadLabel->show());
$objpointsTable->addHeaderCell($totalPoints, NULL, 'botton');
$objpointsTable->endHeaderRow();

//Create an element for the name of link label
$objselectUserLabel = new label($this->objLanguage->languageText('mod_karmapoints_selectUserLabel', 'karmapoints'), NULL);


//Create dropdown list of users
$objdonateeDrop = new dropdown('donatee');

if(!empty($allUsers)){
	foreach ($allUsers as $userInfo) {
		$objdonateeDrop->addOption($userInfo['userid'], $this->objUser->userName($userInfo['userid']));
	}
}

$objpointsTable->startRow();
$objpointsTable->addCell($objselectUserLabel->show());
$objpointsTable->addCell($objdonateeDrop->show());
$objpointsTable->endRow();

//Add an empty row to the table
$objpointsTable->startRow();
$objpointsTable->addCell("<br />");
$objpointsTable->addCell("<br />");
$objpointsTable->endRow();


//Create an element for the name of link label
$objpointsLabel = new label($this->objLanguage->languageText('mod_karmapoints_pointsLabel', 'karmapoints'), NULL);

$objpoints = new textinput('points', NULL);

$objpointsTable->startRow();
$objpointsTable->addCell($objpointsLabel->show());
$objpointsTable->addCell($objpoints->show());
$objpointsTable->endRow();

//Add an empty row to the table
$objpointsTable->startRow();
$objpointsTable->addCell("<br />");
$objpointsTable->addCell("<br />");
$objpointsTable->endRow();

//Create an element for the name of link label
$objdonateLabel = new label($this->objLanguage->languageText('mod_karmapoints_donateLabel', 'karmapoints'), NULL);

$objdonateButton = new button('donate');
$objdonateButton->setToSubmit();
$objdonateButton->setValue(' '.$this->objLanguage->languageText("word_donate").' ');

$objpointsTable->startRow();
$objpointsTable->addCell($objdonateLabel->show());
$objpointsTable->addCell($objdonateButton->show());
$objpointsTable->endRow();

//Rule so that the points text input must be numeric
$objsendForm->addRule('points',$objLanguage->languageText("mod_karma_val_points_numeric"),'numeric');

$objsendForm->addToForm($objpointsTable->show());

echo $objsendForm->show();
?>