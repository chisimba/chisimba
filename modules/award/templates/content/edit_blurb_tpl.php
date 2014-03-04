<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS Blurb
*/

/**
* Add Blurb group list template for the LRS Blurb
* Author Brent van Rensburg
*/

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

$objeditBlurbForm = new form('lrsblurb', $this->uri(array('action'=>'updateblurb', 'blurbId'=>$blurbId)));

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_blurb_edit_header', 'award');
$objeditBlurbForm->addToForm($header->show());

$valueRow = $this->objBlurb->getRow('id', $blurbId);

$setModule = ucwords(strtolower($valueRow['module']));
$setSection = $valueRow['section'];
$setText = $valueRow['text'];

$rep = array('MODULE'=>$setModule, 'SECTION'=>$setSection);

$objaddeditTable = new htmlTable('lrsblurb');
$objaddeditTable->cellspacing = 2;
$objaddeditTable->cellpadding = '2';
$objaddeditTable->width = '90%';

$objaddeditHeadTable = new htmlTable('lrseditblurb');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->code2Txt("mod_lrs_blurb_edit_sub_header", 'award', $rep)."</i>");
$objaddeditHeadTable->addCell();
$objaddeditHeadTable->endRow();

$txtText = new textinput('text', $setText, null, 70);

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_blurb_edit_text', 'award'), NULL, 'top', NULL, 'odd');
$objaddeditTable->addCell($txtText->show());
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewblurb'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddeditTable->startRow();
$objaddeditTable->addCell("<br />");
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$objeditBlurbForm->addToForm($objaddeditHeadTable->show());
$objeditBlurbForm->addToForm($objaddeditTable->show());

echo $objeditBlurbForm->show();
?>