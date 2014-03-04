<?php
//Load the link class
$this->loadClass('link','htmlelements');
//Load the label class
$this->loadClass('label','htmlelements');
//load the Heading class
$this->loadClass('htmlHeading', 'htmlelements');
//load the table class
$this->loadClass('htmlTable', 'htmlelements');

//create a table
$objTable = new htmltable('festival');

//create an element for the heading label
$objStatHeadLabel = new htmlHeading();
$objStatHeadLabel->str=$this->objLanguage->languageText('mod_festival_statHeading', 'festival');
$objStatHeadLabel->type=2;

//Instanstiate the player
$player = $this->newObject('buildsoundplayer','files');
//Set the file
$player->setSoundFile($oggFile);

//Create an element for the name of link label
$objLinkLabel = new label($this->objLanguage->languageText('mod_festival_download', 'festival'), NULL);

//Create a link to download the file
$objLink = new link($this->uri(array('action'=>'download','file'=>$oggFile)));
//Create the label for the link "here"
$objLink->link = $this->objLanguage->languageText('mod_festival_here', 'festival');

//Add a heading row in the table
$objTable->startHeaderRow();
$objTable->addHeaderCell("<br />", '30%');
$objTable->addHeaderCell($objStatHeadLabel->show(), '70%');
$objTable->endHeaderRow();

//Display the table
echo $objTable->show();
//Display the player
echo $player->show();
//Display the link label
echo $objLinkLabel->show();
//Display the link
echo $objLink->show();
?>