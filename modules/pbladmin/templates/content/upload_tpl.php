<?php
/**
* @package pbladmin
*/

/**
* Template to upload a pbl (xml) file.
*/
$this->setLayoutTemplate('admin_layout_tpl.php');

// html elements
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$objIcon =& $this->newObject('geticon','htmlelements');
$objLayer =& $this->newObject('layer','htmlelements');

$heading=$this->objLanguage->languageText('phrase_uploadfile');
$this->setVarByRef('heading',$heading);

$text = $this->objLanguage->languageText('mod_pbladmin_fileformat', 'pbladmin');
$downloadLabel = $this->objLanguage->languageText('mod_pbladmin_downloadtemplate', 'pbladmin');

// Set up file input
$objInput = new textinput('upload', '', 'file');

$objTable = new htmltable();

$objTable->startRow();
$objTable->addCell('','10%');
$objTable->addCell('','30%');
$objTable->addCell('','50%');
$objTable->addCell('','10%');
$objTable->endRow();

$objTable->row_attributes=' height="30" ';
$objTable->startRow();
$objTable->addCell('','10%');
$objTable->addCell('<b>'.$this->objLanguage->languageText('phrase_filename').'</b>','30%','center');
$objTable->addCell($objInput->show(),'','center');
$objTable->endRow();

// Display text about file format.
$objTable->row_attributes=' height="30" ';
$objTable->startRow();
$objTable->addCell('');
$objTable->addCell('<br />'.$text,'','center','','','colspan="2"');
$objTable->endRow();

// create, upload & exit buttons
$objButton = new button('save',$this->objLanguage->languageText('word_upload'));
$objButton->setToSubmit();
$objButton->setIconClass("upload");
$loadBtn = $objButton->show();

$objButton = new button('back',$this->objLanguage->languageText('word_back'));
$objButton->setToSubmit();
$objButton->setIconClass("cancel");
$loadBtn1 = $objButton->show();

$objTable->row_attributes='';
$objTable->startRow();
$objTable->addCell('');
$objTable->addCell($loadBtn,'','center','center');
$objTable->addCell($loadBtn1,'','center','');
$objTable->endRow();

// set up form
$objForm = new form('InstallCase', $this->uri(array('action'=>'upload')));
$objForm->extra=" enctype='multipart/form-data'";
$objForm->addToForm($objTable->show());

$objLayer->cssClass='even';
$objLayer->str = '<br />'.$objForm->show().'<br />';
$div = $objLayer->show();

// display table
echo $div;
?>