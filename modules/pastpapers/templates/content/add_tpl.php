<?php
/*template for adding past papers

*/
$content = "";
$this->loadClass('htmltable','htmlelements');
$heading = $this->getObject('htmlheading','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('radio','htmlelements');
$this->objPopupcal = &$this->getObject('datepickajax', 'popupcalendar');
$this->loadClass('button','htmlelements');
$objIcon = & $this->getObject('geticon','htmlelements');

$form = $this->getObject('form','htmlelements');
$form->name = "addform";
$form->action = $this->uri(array('action'=>'savepaper'));
$form->extra ="enctype='multipart/form-data'";

$heading->str = $this->objLanguage->languageText('mod_pastpapers_addpastpaper','pastpapers')."&nbsp;".$this->_objDBContext->getTitle($this->_objDBContext->getContextCode());
$heading->align= "center";

$content .= $heading->show();

//fields to add to the form
$fileuploadlabel = $this->objLanguage->languageText('mod_pastpapers_uploadfile','pastpapers');
$uploadfield = new textinput('filename','','file');

//input field for the date
$defaultDate = date('Y-m-d');
//$dateinput = new textinput('date',$defaultDate );
$dateinput  = $this->objPopupcal->show('date', 'no', 'yes',$defaultDate);

$objIcon->seticon('select_date');
$examtimelabel = $this->objLanguage->languageText('mod_pastpapers_examtime','pastpapers');
$dateurl = $this->uri(array('field'=>'document.addform.date','fieldvalue'=>date('Y-m-d')), 'popupcalendar');
$onclick = "javascript:window.open('" .$dateurl."', 'popupcal', 'width=320, height=410, scrollbars=1, resize=yes')";
$startDateLink = new link('#');
$startDateLink->extra = "onclick=\"$onclick\"";
$startDateLink->link = $objIcon->show().' '.$this->objLanguage->languageText('word_date');

//$Date = $this->objPopupcal->show('date', 'no', 'yes',$defaultDate);

//filed for the topic field
$topicfield = new textinput('topic');

//add options for lecturer to choose whether to students or other users can add answers
$optionlabel =$this->objLanguage->languageText('mod_pastpapers_allowstudents','pastpapers'); 
//add radio buttons
$radiooptions = new radio('option');
$radiooptions->addOption("1",$this->objLanguage->languageText('word_yes'));
$radiooptions->addOption("0",$this->objLanguage->languageText('word_no'));
$radiooptions->setSelected('0');

//button
$objButton = new button("save",$objLanguage->languageText("word_save"));   
$objButton->setToSubmit();

//create the table and fill it with the form items
$table = new htmltable();

$table->startRow();
$table->addCell($fileuploadlabel);
$table->addCell($uploadfield->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_pastpapers_topic','pastpapers'));
$table->addCell($topicfield->show());
$table->endRow();

$table->startRow();
$table->addCell($examtimelabel);
//$table->addCell($dateinput->show()."".$startDateLink->show());
$table->addCell($dateinput);
$table->endRow();

$table->startRow();
$table->addCell($optionlabel);
$table->addCell($radiooptions->show());
$table->endRow();

$table->startRow();
$table->addCell("");
$table->addCell($objButton->show());
$table->endRow();


$form->addToform($table->show());

$content .= $form->show();
echo $content;

?>