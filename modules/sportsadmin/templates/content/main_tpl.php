<?php 
/* the main page for sports
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary, Kaddu Ismael
*
*/

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);
	
// Create instances of the used classes
	$objHeader =& $this->newObject('htmlheading', 'htmlelements');
	//$heading = & $this->getObject('htmlheading','htmlelements');
	$objTable =& $this->newObject("htmltable","htmlelements");
	$objLanguage = & $this->getObject('language','language');
	$objButton =& $this->newObject('button', 'htmlelements');
	$this->objUser=& $this->getObject('user','security');

	$objSports=0;
	$objSports =& $this->getObject('dbsports');
	
// Show the add link
    $addIcon = $this->getObject('geticon','htmlelements');
    $addurl = $this->uri(array('module'=>'sports','action'=>'add'));
    $addIconLink= $addIcon->getAddIcon($addurl);
	
	$objHeader->str = $objLanguage->languageText('mod_sports','sports').'&nbsp;&nbsp;&nbsp;'.$addIconLink;

// set up language items
   	$sportName = $objLanguage->languageText('mod_sports_sportname','sports');
	$description = $objLanguage->languageText('mod_sports_description','sports');
	$creatorName = $objLanguage->languageText('mod_sports_userid','sports');
	$dateCreated = $objLanguage->languageText('mod_sports_datecreated','sports');
	$action = $objLanguage->languageText('word_action');
	
// set up heading
    $objHeader->str = $objHeader->show();
    $objHeader->type = 2;
	$objHeader->align = "left";
    $str = $objHeader->show();
    echo $str . "<hr />";
	
 // Create a table object
    $objTable->border = 0;
    $objTable->cellspacing='3';
    $objTable->width = "90%";

	$objTable->startRow();
	$objTable->addHeaderCell("<b>".$sportName."</b>",'','','left');
	$objTable->addHeaderCell("<b>".$description."</b>",'','','left');
	$objTable->addHeaderCell("<b>".$creatorName."</b>",'','','left');
	$objTable->addHeaderCell("<b>".$dateCreated."</b>",'','','left');
	$objTable->addHeaderCell("<b>".$action."</b>",'','','left');
	$objTable->endRow();
	
//get all sports added
	$sportsarray=array();
	$sportsarray=$objSports->listAll();
	if(!empty($sportsarray)) {
// Step through identifying sports added.
	$class = 'even';
 	foreach ($sportsarray as $item) {
    $class = ($class == 'odd') ? 'even':'odd';
	
// Display each field for Lecturers
    $objTable->startRow();
    $objTable->addCell($item['name'], '', '', '', $class);
    $objTable->addCell($item['description'], '', '', '', $class);
    $objTable->addCell($this->objUser->fullname($item['userId']), '', '', '', $class);
    $objTable->addCell($item['datecreated'], '', '', '', $class);

 // Show the edit link
    $iconEdit = $this->getObject('geticon','htmlelements');
    $iconEdit->setIcon('edit');
    $iconEdit->alt = $objLanguage->languageText('mod_sports_edit','sports');
    $iconEdit->align=false;
    $objLink =& $this->getObject("link","htmlelements");
	
    $objLink->link($this->uri(array('module'=>'sports','action'=>'add','useEdit'=>'1','id'=>$item["id"])));
    $objLink->link = $iconEdit->show();
    $linkEdit = $objLink->show();

 // Show the delete link
    $iconDelete = $this->getObject('geticon','htmlelements');
    $iconDelete->setIcon('delete');
    $iconDelete->alt = $objLanguage->languageText('mod_sports_delete','sports');
    $iconDelete->align=false;

    $objConfirm =& $this->getObject("link","htmlelements");
    $objConfirm=&$this->newObject('confirm','utilities');
    $objConfirm->setConfirm(
    $iconDelete->show(),
    $this->uri(array('module'=>'sports','action'=>'delete','confirm'=>'yes','id'=>$item["id"])),
    $objLanguage->languageText('mod_sports_suredelete','sports'));

    $objTable->addCell($linkEdit . $objConfirm->show(), '', '', '', $class);
    $objTable->endRow();
 }//end foreach
}//end if

echo $objTable->show();
?>