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
* Select addedit index overview template for the LRS Wages
* 
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkBox', 'htmlelements');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_award_editindexvalues', 'award');

//$lblindexName = $this->objLanguage->languageText('mod_lrs_index_name');
$lblindexAbbr = $this->objLanguage->languageText('mod_lrs_index_abbr', 'award');
$lblindexDate = $this->objLanguage->languageText('mod_lrs_index_date', 'award');
$lblindexValue = $this->objLanguage->languageText('mod_lrs_index_value', 'award');
$lblindexYear = $this->objLanguage->languageText('mod_lrs_index_year', 'award');

$monthRule = $this->objLanguage->languageText('mod_lrs_index_month_Rule', 'award');
$valueRule = $this->objLanguage->languageText('mod_lrs_index_value_Rule', 'award');

$indexYr = $this->objLanguage->languageText('word_year');

//create table
$objaddeditTable = new htmlTable('indexValueTable');
$objaddeditTable->cellspacing = 2;

$indexType = $this->objIndexes->getRow('id', $typeId);

$headName = $this->newObject('htmlheading','htmlelements');
$headName->type = 4;
$headName->str = $indexType['name'].' - '.$indexType['shortname'];

$objYearForm = new form('yearForm', $this->uri(array('action'=>'addoreditindex', 'typeId'=>$typeId, 'selected'=>'init_10')));
$objyearTable = new htmlTable('lrsadmin');

if (!isset($currentYr) || $currentYr > date('Y')) {
	$currentYr = date('Y');
}

$objyear = new textinput('year', $currentYr, NULL, '4');

$btnUpdateYr = new button('submitYear');
$btnUpdateYr->setToSubmit();
$btnUpdateYr->setValue(' '.$this->objLanguage->languageText("word_update").' ');

$objyearTable->startRow();
$objyearTable->addCell("<i>".$lblindexYear."</i>", '30%');
$objyearTable->addCell($objyear->show().' '.$btnUpdateYr->show());
$objyearTable->endRow();

$objYearForm->addToForm($objyearTable->show());

$objmonth = new dropdown('month');
$objmonth->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
$objmonth->setSelected('-1');
$objaddeditTable->startHeaderRow();
$objaddeditTable->addHeaderCell($indexYr);
$currentYr -= 4;

for($i=1;$i<=12;$i++){
	$ts = mktime(0,0,0,$i);
	$objaddeditTable->addHeaderCell(date('M', $ts));
	$objmonth->addOption($i,date('F', $ts));
}
$objaddeditTable->endHeaderRow();

for($j=0;$j<=4;$j++)
{
	$thisYear = $currentYr+$j;
	$link = new link();
	$objaddeditTable->startRow('odd');
	$objaddeditTable->addCell($thisYear);
	
	//Create the index list with links to there overviews on each name
	for ($i=1;$i<=12;$i++) 
	{
		$item = $this->objIndexes->getArray("SELECT id, value FROM tbl_award_index_values
						WHERE typeid = '$typeId' AND MONTH(indexdate) = '$i' AND YEAR(indexdate) = '$thisYear'");
		$item = current($item);
		if($item == false) {
			$txtinputValue = new textinput("inputValue_{$thisYear}_{$i}", NULL, NULL, '4');
			$objaddeditTable->addCell($txtinputValue->show());
		} else {
			$wordUpdate = ' '.$this->objLanguage->languageText("word_update").' ';
			$wordBack = ' '.$this->objLanguage->languageText("word_back").' ';
			$errorMsg = $this->objLanguage->languageText("phrase_required");
			$link->link = $item['value'];
			$link->link('#');
			$link->extra = " onclick = 'javascript:updateIndexValue(\"{$item['id']}\", \"{$item['value']}\", $i, $thisYear, \"$wordUpdate\", \"$wordBack\", \"$errorMsg\")'";
			$objaddeditTable->addCell("<div id='indexCell_{$thisYear}_{$i}'>".$link->show()."</div>");
		}
	}
	$objaddeditTable->endRow();	
}

$objNewValues = new form('newValues', $this->uri(array('action'=>'insertindexvalue', 'currentYr'=>$currentYr, 'typeId'=>$typeId)));

$btnSubmit = new button('submit');
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');
$btnSubmit->setToSubmit();

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'startindex', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

/*if($indexType['display'] == 1){
	$setDisplay = true;
}
else{
	$setDisplay = false;
}

$display = new checkBox("display", null, $setDisplay);

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText("mod_lrs_index_enable_disable", 'award'));
$objaddeditTable->addCell($display->show());
$objaddeditTable->endRow();*/

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().'  '.$btnCancel->show(),null,'top',null,null,"colspan=3");
$objaddeditTable->endRow();

$objNewValues->addToForm($objaddeditTable->show());

$script = $this->getResourceUri('indexadmin.js');;
//add to header
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$script'></script>");

echo $header->show().$headName->show();
echo $objYearForm->show();
echo $objNewValues->show();
?>