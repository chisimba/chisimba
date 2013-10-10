<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS SIC
*/

/**
* blurbs list template for the LRS blurbs
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

$objBlurbsForm = new form('lrsblurbs');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrs_blurb_header', 'award');
$objBlurbsForm->addToForm($header->show());

//Create htmltable for selectmajorgroup form elements
$objSelectTable =& $this->newObject('htmltable', 'htmlelements');
$objSelectTable->cellspacing = '2';
$objSelectTable->cellpadding = '2';
$objSelectTable->width = '90%';

//Set table header row
$objSelectTable->startHeaderRow();
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_blurb_module', 'award'), '15%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_blurb_section', 'award'), '30%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_blurb_text', 'award'), '40%', '', '', '');
$objSelectTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_decent_work_edit', 'award'), '10%', '', '', '');
$objSelectTable->endHeaderRow();

$class = '';
if(count($blurb) > '0'){
  foreach($blurbs as $blurb){ 
     $blurbId = $blurb['id'];
     $blurbModule = ucwords(strtolower($blurb['module']));
     $blurbSection = $blurb['section'];
     $blurbText = ucfirst($blurb['text']);

     // Create edit icon
     $param = $this->uri(array('action' => 'editblurb', 'blurbId'=>$blurbId));
     $objEditIcon = &$this->newObject('geticon', 'htmlelements');
     $objEditIcon->alt = $this->objLanguage->languageText('mod_lrssic_editmajordiv', 'award');
     $editIcon = $objEditIcon->getEditIcon($param); 

     $class = ($class=='odd')? 'even' : 'odd';
     
     $objSelectTable->startRow();
     $objSelectTable->addCell($blurbModule, '', '', '', $class, '');
     $objSelectTable->addCell($blurbSection, '', '', '', $class, '');
     $objSelectTable->addCell($blurbText, '', '', '', $class, '');
     $objSelectTable->addCell($editIcon, '', '', '', $class, '');
     $objSelectTable->endRow();
  }
} else {
     //Add no records message to table
     $objSelectTable->startRow();
     $objSelectTable->addCell($this->objLanguage->languageText("mod_lrssoc_norecordsavailable", 'award'), '', '', '', 'noRecordsMessage', 'colspan=4');
     $objSelectTable->endRow();
}

$linkExit = new link($this->uri(array('action'=>'admin'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

//Create htmltable for selectmajorgroup form elements
$objAddTable =& $this->newObject('htmltable', 'htmlelements');
$objAddTable->cellspacing = '2';
$objAddTable->cellpadding = '2';
$objAddTable->width = '90%';

$objAddTable->startRow();
$objAddTable->addCell("<br />");
$objAddTable->addCell("<br />");
$objAddTable->endRow();

$objAddTable->startRow();
$objAddTable->addCell($linkExit->show());
$objAddTable->addCell("<br />");
$objAddTable->endRow();

$objBlurbsForm->addToForm($objSelectTable->show());
$objBlurbsForm->addToForm($objAddTable->show());

echo $objBlurbsForm->show();

?>