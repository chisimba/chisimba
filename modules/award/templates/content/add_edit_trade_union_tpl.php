<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS org
*/

/**
* Add Edit Trade Union list template for the LRS org
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the textarea class
$this->loadClass('textarea', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the tabbed box class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the dropdown class
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');

$scriptRule = '<script type="text/javascript" src="modules/lrsadmin/resources/addRule.js"></script>';
//add to header
$this->appendArrayVar('headerParams', $scriptRule);

if(isset($unionId))
{
	$objAddEditTradeUnionForm = new form('lrsorg', $this->uri(array('action'=>'edittradeunion', 'unionId'=>$unionId, 'selected'=>'init_10')));
}
else
{
	$objAddEditTradeUnionForm = new form('lrsorg', $this->uri(array('action'=>'addtradeunion', 'selected'=>'init_10')));
}

$Abbr = $this->objLanguage->languageText('word_abbreviation');
$name = $this->objLanguage->languageText('word_name');
$msgAbb = $this->objLanguage->languageText('mod_lrsorg_partyabbreviationrequired', 'award');
$msgName = $this->objLanguage->languageText('mod_lrsorg_typenamerequired', 'award');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
if (isset($unionId)){
	$header->str = $this->objLanguage->languageText('mod_lrssic_edit_tradeunion', 'award');
	$objAddEditTradeUnionForm->addToForm($header->show());
}
else 
{
	$header->str = $this->objLanguage->languageText('mod_lrssic_add_tradeunion', 'award');
	$objAddEditTradeUnionForm->addToForm($header->show());
}

$objaddeditTable = new htmlTable('lrsorg');
$objaddeditTable->cellspacing = 2;
$objaddeditTable->cellpadding = '2';
$objaddeditTable->width = '90%';

$objaddeditHeadTable = new htmlTable('lrsorg');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrsorg_lbl_head_tradeunion', 'award')."</i>");
$objaddeditHeadTable->addCell(" ");
$objaddeditHeadTable->endRow();

if(isset($unionId))
{
	$valueRow = $this->objDbParty->getRow('id', $unionId);
	$setAbbreviation = $valueRow['abbreviation'];
	$setName = $valueRow['name'];
}
else
{
	$setAbbreviation = '';
	$setName = '';
}

$txtAbbr = new textinput('abbreviation', $setAbbreviation);
$txtName = new textarea('name', $setName);

$objaddeditTable->startRow();
$objaddeditTable->addCell($Abbr. ':', '30%', NULL, NULL, 'odd');
$objaddeditTable->addCell($txtAbbr->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($name. ':', NULL, 'top', NULL, 'odd');
$objaddeditTable->addCell($txtName->show());
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
//$btnSubmit->setOnClick("javascript:if((validate_form('input_abbreviation', '$msgAbb')) && (validate_form('input_name', '$msgName'))) {document.getElementById('form_lrsorg').submit()}");
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewtradeunion', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$objAddEditTradeUnionForm->addToForm($objaddeditHeadTable->show());
$objAddEditTradeUnionForm->addToForm($objaddeditTable->show());

echo $objAddEditTradeUnionForm->show();
?>