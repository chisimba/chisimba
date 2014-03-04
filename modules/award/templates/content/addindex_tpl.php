<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS Admin
*/

/**
* Select add index template for the LRS Wages
* 
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');

//Create form
$objaddIndexForm = new form('lrsadmin', $this->uri(array('action'=>'insertindex', 'indexId'=>$indexId)));

$hStr = $this->objLanguage->languageText('mod_lrs_add_index_header', 'award');

$name = $shortname = '';
if (isset($indexId)) {
    $index = $this->objIndexes->getRow('id',$indexId);
    $name = $index['name'];
    $shortname = $index['shortname'];
    $hStr = $this->objLanguage->languageText('mod_award_editindex', 'award');
}
//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $hStr;
$objaddIndexForm->addToForm($header->show());

//Create a table
$objaddIndexTable = new htmlTable('lrsadmin');
$objaddIndexTable->cellspacing = '2';
$objaddIndexTable->cellpadding = '2';
$objaddIndexTable->width = '90%';

$lbladdNew = $this->objLanguage->languageText('mod_lrs_index_add_new', 'award');
$lblindexName = $this->objLanguage->languageText('mod_lrs_index_name', 'award');
$lblindexAbbr = $this->objLanguage->languageText('mod_lrs_index_abbr', 'award');
$lblindexDate = $this->objLanguage->languageText('mod_lrs_index_date', 'award');
$lblindexValue = $this->objLanguage->languageText('mod_lrs_index_value', 'award');
$nameRule = $this->objLanguage->languageText('mod_lrs_index_name_rule', 'award');
$abbrRule = $this->objLanguage->languageText('mod_lrs_index_abbr_rule', 'award');
$valueRule = $this->objLanguage->languageText('mod_lrs_index_value_rule', 'award');

$objheadTable = new htmlTable('lrsadmin');

$objheadTable->startRow();
$objheadTable->addCell("<i>".$lbladdNew."</i>");
$objheadTable->addCell("<br />");
$objheadTable->endRow();

$objaddIndexForm->addToForm($objheadTable->show());

$txtName = new textinput('name',$name,'text',60);

$objaddIndexTable->startRow();
$objaddIndexTable->addCell($lblindexName. ':', '30%','','','odd');
$objaddIndexTable->addCell($txtName->show(), '70%');
$objaddIndexTable->endRow();

$txtAbbr = new textinput('abbr',$shortname);

$objaddIndexTable->startRow();
$objaddIndexTable->addCell($lblindexAbbr. ':','','','','odd');
$objaddIndexTable->addCell($txtAbbr->show());
$objaddIndexTable->endRow();

// && (validate_form('input_value', '$valueRule'))
$btnSubmit = new button('submitindex');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'startindex', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddIndexTable->startRow();
$objaddIndexTable->addCell("<br />");
$objaddIndexTable->addCell("<br />");
$objaddIndexTable->endRow();

$objaddIndexTable->startRow();
$objaddIndexTable->addCell($btnSubmit->show().' '.$btnCancel->show());
$objaddIndexTable->addCell("<br />");
$objaddIndexTable->endRow();

$objaddIndexForm->addToForm($objaddIndexTable->show());
$objaddIndexForm->addRule('name',$nameRule,'required');
$objaddIndexForm->addRule('abbr',$abbrRule,'required');

echo $objaddIndexForm->show();
?>