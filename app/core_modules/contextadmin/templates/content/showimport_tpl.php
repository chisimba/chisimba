<?php

$form =  $this->newObject('form', 'htmlelements');
$radio =  $this->newObject('radio', 'htmlelements');
$table =  $this->newObject('htmltable', 'htmlelements');
$button =  $this->newObject('button', 'htmlelements');
   $fileInput=$this->newObject('textinput','htmlelements');
   
   //setup form
    $form->name = 'import_do';
	$form->id = 'import_do';
    $form->extra=' enctype="multipart/form-data" ';
   ///  $paramArray = array('action' => 'import','contextid' => $this->getParam('contextid'),'contextcode' => $this->getParam('contextcode'),'title' => $this->getParam('title'));
    $form->setAction($this->uri(array('action' => 'import', 'nodeid' => $this->getParam('inpnodeId'))));
    $form->setDisplayType(2);
    
    //give a heading
      $this->objH = $this->getObject('htmlheading', 'htmlelements');
      $this->objH->type=1;
      $this->objH->str=$this->objLanguage->languageText('mod_contextadmin_importcontent');
      $heading = $this->objH->show();
      $str = '';// '<span class="warning">'. $this->objLanguage->languageText('mod_contextadmin_importwarning').'</span><br>';
   
   //set the first row
   $table->width = '100%';
   $table->startRow();
   $table->addCell($str,'','','center');
   $table->startRow();
       
    //setup the file input
    $fileInput->fldType='file';    
    $fileInput->name='userfile';
    $fileInput->size=60;
    
    $this->objH->type=3;
    $this->objH->str=   $this->objLanguage->languageText("mod_contextadmin_importto"). ' : ' .$this->objDBContentNodes->getField('title', $this->getParam('inpnodeId'));
   
   $table->startRow();
   $table->addCell($this->objH->show());
   $table->endRow();
   
   $table->startRow();
   $table->addCell($fileInput->show());
   $table->endRow();
   
   $table->startRow();
   $table->addCell(' <span class="warning">('.$this->objLanguage->languageText("mod_contextadmin_staticwarning").')</span><br>');
   $table->endRow();
      
    $button->setToSubmit();	
    $button->setValue($this->objLanguage->languageText("mod_contextadmin_save"));
    
    $table->startRow();
   $table->addCell('<br>'.$button->show());
   $table->endRow();
   
   $form->addToForm($heading);
   $form->addToForm($table);
    
    echo $form->show();
?>