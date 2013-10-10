<?php 
//template for ading answers to pastpapers
$content = "";
$this->loadClass('textinput','htmlelements');
$this->loadClass('hiddeninput','htmlelements');
$this->loadClass('link','htmlelements');

$this->objLanguage = & $this->getObject('language','language');
$this->loadClass('textinput','htmlelements');
//get the id of the paper to be added
$paperid = $this->getParam('paperid',NULL);
$this->loadClass('htmltable','htmlelements');

$form =& $this->getObject('form','htmlelements');
$form->action =$this->uri(array('action'=>'saveanswers','paperid'=>$paperid));
$form->extra ="enctype='multipart/form-data'";

//get the paper details
$this->pastapapers =& $this->getObject('pastpaper');
$paperdetails = $this->pastapapers->getPaperDetails($paperid);

$heading = $this->getObject('htmlheading','htmlelements');
$heading->align ="center";
$heading->str = $this->objLanguage->languageText('mod_pastpapers_addanswersto','pastpapers')."&nbsp;".$paperdetails;

$uploadlabel = $this->objLanguage->languageText('mod_pastpapers_uploadfile','pastpapers');
$uploadfileinput = new textinput('filename','','file');

//add a button 
$submit = & $this->getObject('button','htmlelements');
$submit->name = "name";
$submit->value = $this->objLanguage->languageText('word_submit');
$submit->setToSubmit();

$table = new htmltable();

$table->startRow();
$table->addCell($uploadlabel);
$table->addCell($uploadfileinput->show());
$table->endRow();

$paperid = $this->getParam('paperid',NULL);

$hiddenfield = new hiddeninput('paperid',$paperid);
//add a hidden input
$table->startRow();
$table->addCell("");
$table->addCell($hiddenfield->show());
$table->endRow();

$table->startRow();
$table->addCell("");
$table->addCell($submit->show());
$table->endRow();


$form->addToForm($table->show());
$content .= $heading->show($optionlabel);

$content .= $form->show();
echo $content;
?>