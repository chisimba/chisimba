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
* Award admin template for inflation indexes
* 
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_index_overview_header', 'award');

$lblindexName = $this->objLanguage->languageText('mod_lrs_index_name', 'award');
$lblindexAbbr = $this->objLanguage->languageText('mod_lrs_index_abbr', 'award');
$lblindexDate = $this->objLanguage->languageText('mod_lrs_index_last_date', 'award');
$lblindexValue = $this->objLanguage->languageText('mod_lrs_index_value', 'award');

//create table
$objindexTable = new htmlTable('lrsadmin');
$objindexTable->cellspacing = 2;

$objindexTable->startHeaderRow();
$objindexTable->addHeaderCell($lblindexAbbr);
$objindexTable->addHeaderCell($lblindexName);
$objindexTable->addHeaderCell($lblindexDate);
$objindexTable->addHeaderCell($lblindexValue);
$objindexTable->addHeaderCell($this->objLanguage->languageText('word_edit'));
$objindexTable->endHeaderRow();

$indexNameList = $this->objIndexes->getAll();

$link = new link();
$class = '';

//Create the index list with links to there overviews on each name
foreach ($indexNameList as $list) 
{
	$typeId = $list['id'];
	//Display the date of the last input
	$sqlLastDate = "SELECT Max(indexdate) AS date, value FROM tbl_award_index_values WHERE typeid = '$typeId' GROUP BY typeid";
	$arrayLastDate = $this->objIndexValues->getArray($sqlLastDate);
	$elementLastDate = current($arrayLastDate);
	if($elementLastDate['date'] == ''){
		$elementDate = NULL;
	}
	else{
		$elementDate = strtotime($elementLastDate['date']);
	}
	$lastDate = date('M-Y', $elementDate);
	$lastElementDate = $elementLastDate['date'];
	$indexValList = $this->objIndexValues->getAll("WHERE indexdate = '$lastElementDate' AND typeid = '$typeId'");
	$indexVal = current($indexValList);
	if(($lastDate == "Jan-1970")&&($indexVal['value']=='')){
		$lastDate = '-';
		$indexVal['value'] = '-';
	}
	
	$class = ($class=='odd')? 'even' : 'odd';
	$link->link = $list['shortname'];
	$link->link($this->uri(array('action'=>'addoreditindex', 'typeId'=>$list['id'], 'selected'=>'init_10'),'award'));
	$objindexTable->startRow($class);
	$objindexTable->addCell($link->show(), '15%');
	$objindexTable->addCell($list['name']);
	$objindexTable->addCell($lastDate);
	$objindexTable->addCell($indexVal['value']);
    $objindexTable->addCell($objIcon->getEditIcon($this->uri(array('action'=>'addindex', 'indexId'=>$list['id'], 'selected'=>'init_10'))));
	$objindexTable->endRow();	
}

//Add a new index link
$linkaddIndex = new link($this->uri(array('action'=>'addindex', 'selected'=>'init_10'),'award'));
$linkaddIndex->link = $this->objLanguage->languageText('mod_lrs_add_index_link', 'award');

$linkBack = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkBack->link = $this->objLanguage->languageText("word_back");

$links = "<br />".$linkaddIndex->show()." / ".$linkBack->show();

echo $header->show().$objindexTable->show().$links;
?>