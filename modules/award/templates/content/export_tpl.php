<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package AWARD Data Export
*/

$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');

$objH = $this->getObject('htmlheading', 'htmlelements');
$objH->type = '2';
//Set the header string
$objH->str = $this->objLanguage->languageText('mod_award_dataexport','award');

$indexes = $this->indexFacet->getIndexes();
$indexSelect = new dropdown('indexSelect');
if (is_array($indexes)) {
    foreach ($indexes as $index) {
    	$indexSelect->addOption($index['id'],$index['shortname']);
    }
}

$year = new textinput('years');

$objRadio = $this->newObject('radio','htmlelements');
$objRadio->name ='type';
$objRadio->addOption('wages'," ".$this->objLanguage->languageText('word_wages')." ");
$objRadio->addOption('conditions'," ".$this->objLanguage->languageText('word_conditions')." ");
$objRadio->setSelected('wages');
$objRadio->extra = "onclick = 'javascript:changeExport(this.value)'";

$button = new button('enter',$this->objLanguage->languageText('word_export'));
$button->setToSubmit();

$backUri = $this->uri(array('action'=>'admin', 'selected'=>'init_10'));
$back = new button('back', $this->objLanguage->languageText('word_back'), "document.location='$backUri'");

$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_award_exporttype', 'award'));
$objTable->addCell($objRadio->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('mod_award_exportyear', 'award'));
$objTable->addCell($year->show());
$objTable->endRow();
$objTable->startRow("export_index");
$objTable->addCell($this->objLanguage->languageText('mod_lrspostlogin_selectinflationindex', 'award'));
$objTable->addCell($indexSelect->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('');
$objTable->addCell($button->show()."&nbsp;&nbsp;".$back->show());
$objTable->endRow();

$objForm = new form('exportform', $this->uri(array('action'=>'dataexport'), 'award'));
$objForm->addToForm($objTable->show());
$objForm->addRule('years', $this->objLanguage->languageText('mod_award_yearsreq', 'award'), 'required');

$jsLib = $this->getResourceUri("admin.js");
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$jsLib'></script>");

echo $objH->show().$objForm->show();
?>