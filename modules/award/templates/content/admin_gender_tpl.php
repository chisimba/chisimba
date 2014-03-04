<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS admin
*/

/**
* Gender list template for the LRS admin
* Author Brent van Rensburg
*/

//Set layout template
$this -> setLayoutTemplate('layout_tpl.php');

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textarea class
$this->loadClass('textarea', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the tabbed box class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the dropdown class
$this->loadClass('dropdown', 'htmlelements');

//Create form
$objGenderForm = new form('lrsadmin');

// Create add icon
$param = $this->uri(array('action' => 'addnewgender','selected'=>'init_10'));
$objAddIcon = &$this->newObject('geticon', 'htmlelements');
$objAddIcon->alt = $this->objLanguage->languageText('mod_lrssoc_majorgroup', 'award');
$addIcon = $objAddIcon->getAddIcon($param);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type =1;
$header->str = $this->objLanguage->languageText('mod_lrs_gender_gender', 'award').'  '.$addIcon;
$objGenderForm->addToForm($header->show());

$objBCEA = &$this->getObject('dbbcea','award');
$headArray = array($this->objLanguage->languageText('word_category'),$this->objLanguage->languageText('mod_lrspostlogin_typebenefit', 'award'),
$this->objLanguage->languageText('word_benefit'),
$this->objLanguage->languageText('word_bcea'),$this->objLanguage->languageText('word_comment'),$this->objLanguage->languageText('word_edit'));
$objTable = $this->newObject('htmltable','htmlelements');
$objTable->cellspacing=2;
$objTable->addHeader($headArray);

$bcea = $objBCEA->getArray('SELECT DISTINCT category FROM tbl_award_gender_bcea');
$class = 'odd';
foreach ($bcea as $cat) {
	$cells = $objBCEA->getArray("SELECT * FROM tbl_award_gender_bcea WHERE category LIKE '{$cat['category']}'");
	$count = 0;
	$total = count($cells);
	$objTable->startRow($class);
	// Create edit icon
	$param = $this->uri(array('action' => 'editgendercat', 'genderCat'=>$cat['category'], 'selected'=>'init_10'));
	$objEditIcon = &$this->newObject('geticon', 'htmlelements');
	$objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editminorgroup', 'award');
	$editIcon = $objEditIcon->getEditIcon($param);
	
	$objTable->addCell($cat['category'].'  '.$editIcon,'20%',null,'center',null,"rowspan=$total border=1");
	foreach ($cells as $cell) {
		$count++;
		
		$param = $this->uri(array('action' => 'editgenderrow','selected'=>'init_10', 'genderId'=>$cell['id']));
		$objEditIcon = &$this->newObject('geticon', 'htmlelements');
		$objEditIcon->alt = $this->objLanguage->languageText('mod_lrssoc_editminorgroup', 'award');
		$editIcon = $objEditIcon->getEditIcon($param);
		
		$genderName = $this->objBenefitNames->getRow('id', $cell['nameid']);
		$typeCase = ucwords(strtolower($cell['type']));
		
		$objTable->addCell($typeCase,null,null,'center');
		$objTable->addCell($genderName['name'],null,null,'center');
		$objTable->addCell($cell['bcea'],null,null,'center');
		$objTable->addCell($cell['comment'],null,null,'center');
		$objTable->addCell($editIcon,null,null,'center');
		$objTable->endRow();
		if ($count < $total) $objTable->startRow($class);
	}
	
	$class = ($class == 'odd')? 'even' : 'odd';
}

// set up exit link
$exitLink = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$exitLink->link = $this->objLanguage->languageText("word_back");

$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->endRow();

$objTable->startRow();
$objTable->addCell($exitLink->show());
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->addCell("<br />");
$objTable->endRow();
		
$objGenderForm->addToForm($objTable->show());

echo $objGenderForm->show();

?>
