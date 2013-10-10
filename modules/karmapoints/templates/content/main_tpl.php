<?php
//Load the form class
$this->loadClass('form','htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//load the table class
$this->loadClass('htmlTable', 'htmlelements');
//load the Heading class
$this->loadClass('htmlHeading', 'htmlelements');
//load the dropdown class
$this->loadClass('dropdown', 'htmlelements');
//Load the link class
$this->loadClass('link','htmlelements');

//Create form
$objForm = new form('karmapoints', $this->uri(array()),'karmapoints');

//Create a table for the header
$objheadTable = new htmlTable('karmapoints');

//create an element for the heading label
$objkarmaHeadLabel = new htmlHeading();
$objkarmaHeadLabel->str=$this->objLanguage->languageText('mod_karmapoints_karmaHeadLabel', 'karmapoints');
$objkarmaHeadLabel->type=1;

//Add a heading row in the table
$objheadTable->startHeaderRow();
$objheadTable->addHeaderCell("<br />", '30%');
$objheadTable->addHeaderCell($objkarmaHeadLabel->show(), '70%');
$objheadTable->endHeaderRow();

$objForm->addToForm($objheadTable->show());

//Create dropdown list of users
$objuserDrop = new dropdown('theUser');

if(!empty($allUsers)){
	foreach ($allUsers as $userInfo) {
		$objuserDrop->addOption($userInfo['userid'], $this->objUser->userName($userInfo['userid']));
	}
}

$objuserDrop->setSelected($user);

//create button to display points
$objshowbutton =  new button('show');
// Set the button type to click
$objshowbutton->setToSubmit();
// with the word create
$objshowbutton->setValue(' '.$this->objLanguage->languageText("word_show").' ');

//Create a table
$objselectTable = new htmlTable('karmapoints');
$objselectTable->width='40%';

//Add a row container the dropdown list and show button to the table
$objselectTable->startRow();
$objselectTable->addCell($objuserDrop->show(), NULL, NULL, 'right');
$objselectTable->addCell($objshowbutton->show(), NULL, NULL, 'right');
$objselectTable->endRow();

//Add table to form
$objForm->addToForm($objselectTable->show());// . "<br /><br />");

//Create a table
$objpointsTable = new htmlTable('karmapoints');
$objpointsTable->width='40%';
$objpointsTable->border=2;

//create an element for the heading label of the points table
$objtypePointsHeadLabel = new htmlHeading();
$objtypePointsHeadLabel->str=$this->objLanguage->languageText('mod_karmapoints_typePointsHeadLabel', 'karmapoints');
$objtypePointsHeadLabel->type=3;

//create an element for the heading label of the points table
$objpointsHeadLabel = new htmlHeading();
$objpointsHeadLabel->str=$this->objLanguage->languageText('mod_karmapoints_pointsHeadLabel', 'karmapoints');
$objpointsHeadLabel->type=3;
//$this->objDbKarma->addUser('123brent', 'blog', '2', 'Brent');
//Add a heading row in the table
$objpointsTable->startHeaderRow();
$objpointsTable->addHeaderCell($objtypePointsHeadLabel->show(), '80%');
$objpointsTable->addHeaderCell($objpointsHeadLabel->show(), '20%');
$objpointsTable->endHeaderRow();

$blogLabel = new label($this->objLanguage->languageText('mod_karmapoints_blogLabel', 'karmapoints'), NULL);
$commentLabel = new label($this->objLanguage->languageText('mod_karmapoints_commentLabel', 'karmapoints'), NULL);
$contentLabel = new label($this->objLanguage->languageText('mod_karmapoints_contentLabel', 'karmapoints'), NULL);
$discussionLabel = new label($this->objLanguage->languageText('mod_karmapoints_discussionLabel', 'karmapoints'), NULL);
$receivedLabel = new label($this->objLanguage->languageText('mod_karmapoints_receivedLabel', 'karmapoints'), NULL);
$sentLabel = new label($this->objLanguage->languageText('mod_karmapoints_sentLabel', 'karmapoints'), NULL);
///////////////////////////////////
$objpointsTable->startRow();
$objpointsTable->addCell($blogLabel->show());
$objpointsTable->addCell($contBlog['points'], NULL, NULL, 'center');
$objpointsTable->endRow();

$objpointsTable->startRow();
$objpointsTable->addCell($commentLabel->show());
$objpointsTable->addCell($contComment['points'], NULL, NULL, 'center');
$objpointsTable->endRow();

$objpointsTable->startRow();
$objpointsTable->addCell($contentLabel->show());
$objpointsTable->addCell($contContent['points'], NULL, NULL, 'center');
$objpointsTable->endRow();

$objpointsTable->startRow();
$objpointsTable->addCell($discussionLabel->show());
$objpointsTable->addCell($contDiscussion['points'], NULL, NULL, 'center');
$objpointsTable->endRow();

$objpointsTable->startRow();
$objpointsTable->addCell($receivedLabel->show());
$objpointsTable->addCell($contReceived['points'], NULL, NULL, 'center');
$objpointsTable->endRow();

$objpointsTable->startRow();
$objpointsTable->addCell($sentLabel->show());
$objpointsTable->addCell($contSent['points'], NULL, NULL, 'center');
$objpointsTable->endRow();

//Add an empty row to the table
$objpointsTable->startRow();
$objpointsTable->addCell("<br />");
$objpointsTable->addCell("<br />");
$objpointsTable->endRow();
	
$total = $contBlog['points'] + $contComment['points'] + $contContent['points'] + $contDiscussion['points'] + $contReceived['points'] - $contSent['points'];

//create an element for the total points label
$objtotalLabel = new label($this->objLanguage->languageText('mod_karmapoints_totalLabel', 'karmapoints'), NULL);

//Add a Total row to the table
$objpointsTable->startRow();
$objpointsTable->addCell($objtotalLabel->show());
$objpointsTable->addCell($total, NULL, NULL, 'center');
$objpointsTable->endRow();

//Add an empty row to the table
$objpointsTable->startRow();
$objpointsTable->addCell("<br />");
$objpointsTable->addCell("<br />");
$objpointsTable->endRow();

$objsendLabel = new label($this->objLanguage->languageText('mod_karmapoints_send', 'karmapoints'), NULL);

$link = new link($this->uri(array('action'=>'send'),'karmapoints'));
$link->link = $this->objLanguage->languageText('mod_karmapoints_sendLink', 'karmapoints');

//Add a Total row to the table
$objpointsTable->startRow();
$objpointsTable->addCell($objsendLabel->show());
$objpointsTable->addCell($link->show(), NULL, NULL, 'center');
$objpointsTable->endRow();

$objForm->addToForm($objpointsTable->show());

echo $objForm->show();
?>