<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
 * Agreement level template for lrspostlogin
 *
 * @package LRS
 * @author Nic Appleby
 * @license GNU/GPL
 * @copyright UWC
 * @version $Id: agreement_tpl.php 130 2008-08-20 11:21:22Z nonqaba $
 *
 */

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('form','htmlelements');
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_buheader','award');

$objRadio = $this->newObject('radio','htmlelements');
$objRadio->name='searchtype';
$objRadio->addOption(0," ".$this->objLanguage->languageText('mod_lrs_busearch','award')." ");
$objRadio->addOption(1," ".$this->objLanguage->languageText('mod_lrs_sicsearch','award')." ");
$objRadio->setSelected(0);
$objRadio->extra = "onclick = 'javascript:changeSearch(this.value)'";

$tInput = new textinput('filter');
$objSearchButton = new button('button_search', $this->objLanguage->languageText('word_search', 'award', 'Search'), "javascript:unitSearchByStr(document.getElementById('input_filter').value)");
$objselectUnits = new dropdown('unit');

$unitId = $this->getParam('unitId');
if (isset($unitId) && $unitId) {
    $unit = $this->objDbUnit->getRow('id',$unitId);
    $objselectUnits->addOption($unitId,$unit['name']);
} else {
    $objselectUnits->addOption('-1',$this->objLanguage->languageText('mod_lrs_select', 'award'));
}

$selected = $this->getParam('selected');

$sicList = $this->objDbSicMajorDivs->getAll('ORDER BY description');
$sicInput = new dropdown('sic');
foreach($sicList as $sic) {
    if (strlen($sic['description']) > 50) {
        $sic['description'] = substr($sic['description'],0,47)."...";
    }
    $sicInput->addOption($sic['id'],"{$sic['code']}0 - {$sic['description']}");
}
$sicInput->extra = "style='display: none' onchange='javascript: updateSic(this.value)'";
$objSicDiv = $this->getObject('dbsicdiv','awardapi');
$sicDivList = $objSicDiv->getAll("WHERE major_divid = '{$sicList[0]['id']}' ORDER BY description");
$sicDivInput = new dropdown('sicDiv');
$sicDivInput->extra = "style='display: none' onchange='javascript: unitSearchBySic();'";

foreach($sicDivList as $sic) {
	if (strlen($sic['description']) > 50) {
       $sic['description'] = substr($sic['description'],0,47)."...";
    }
    $sicDivInput->addOption($sic['id'],"{$sicList[0]['code']}{$sic['code']} - {$sic['description']}");
}

$typesList = $this->objDbAgreeType->getAll('ORDER BY name');
$typeInput = new dropdown('type');
$typeInput->addFromDB($typesList,'name','id');
$typeInput->extra = "style='display: none' onchange='javascript: unitSearchBySic();'";

$message = $this->objLanguage->languageText('mod_lrspostlogin_selectagree', 'award');
$buttonOnlClick = "javascript: if (validateAgreeDrop(document.getElementById('input_unit'),'$message')) {
        document.buform.submit()}";
$objButton = new button('enter',$this->objLanguage->languageText('word_go'),$buttonOnlClick);

$selectedHidden = new textinput('selected',$this->getParam('selected'),'hidden');

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->startRow();
$objTable->addCell($objRadio->show()."<br /><br />",null,'top',null,null,'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("<div id='label_filter'>".$this->objLanguage->languageText('mod_lrs_textfilter','award').": </div>","45%");
$objTable->addCell($tInput->show() . $objSearchButton->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("<div id='label_sic' style='display: none'>".$this->objLanguage->languageText('mod_lrs_sicfilter', 'award').": </div>","45%");
$objTable->addCell($sicInput->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("<div id='label_sicDiv' style='display: none'>".$this->objLanguage->languageText('mod_lrs_sicdivfilter', 'award').": </div>","45%");
$objTable->addCell("<div id='dropdown_sicDiv'>".$sicDivInput->show()."</div>");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("<div id='label_type' style='display: none'>".$this->objLanguage->languageText('mod_lrs_typefilter', 'award').": </div>","45%");
$objTable->addCell($typeInput->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_lrs_dropdown','award').": ");
$objTable->addCell("<div id='unitSelect'>".$objselectUnits->show()."</div>");
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($selectedHidden->show());
$objTable->addCell($objButton->show());
$objTable->endRow();

$objForm = new form('buform',$this->uri(array('action'=>'selectbu'),'award'));
$objForm->addToForm($objTable->show());

$content = $header->show().$objForm->show();

$jsLib = $this->getResourceUri("postlogin.js");
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$jsLib'></script>");
echo $content;


?>
