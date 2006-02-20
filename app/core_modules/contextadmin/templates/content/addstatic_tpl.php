<?php
//creating the form
$form=&$this->newObject('form','htmlelements');
$form->extra=' enctype="multipart/form-data" ';
$form->name='importstatic';
 $paramArray = array('action' => 'import','contextid' => $this->getParam('contextid'),'contextcode' => $this->getParam('contextcode'),'title' => $this->getParam('title'));
$form->setAction($this->uri($paramArray,'contextadmin'));

//the file input
$fileInput=&$this->newObject('textinput','htmlelements');
$fileInput->fldType='file';
$fileInput->label=$this->objLanguage->languageText("mod_contextadmin_folderpath");
$fileInput->name='userfile';
$fileInput->size=60;

//the submit button
$objElement = new button('mybutton');	
$objElement->setToSubmit();	
$objElement->setValue($this->objLanguage->languageText("mod_contextadmin_save"));

//add the objects to the form
$form->addToForm($fileInput);
$form->addToForm('<BR><span class="warning">('.$this->objLanguage->languageText("mod_contextadmin_staticwarning").')</span><br>');
$form->addToForm($objElement);

//echo $form->show();


$this->leftNav = &$this->newObject('layer','htmlelements');
$this->leftNav->id = "leftnav";
//$this->leftNav->str=$str;
echo $this->leftNav->addToLayer();

//RIGHT


$this->rightNav = &$this->newObject('layer','htmlelements');
$this->rightNav->id = "rightnav";
//$this->rightNav->str = $str;
echo $this->rightNav->addToLayer();

//Center
$heading=&$this->newObject('htmlheading','htmlelements');
$heading->str=$this->objLanguage->languageText("mod_contextadmin_importcontent");
$heading->type=3;
$strCenter=$heading->show();
$strCenter.=$form->show();

$this->contentNav = &$this->newObject('layer','htmlelements');
$this->contentNav->id = "content";
$this->contentNav->height='1000'; 
$this->contentNav->str = $strCenter;
echo $this->contentNav->addToLayer();

?>