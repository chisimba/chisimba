<?php
// First page for import
$form =  $this->newObject('form', 'htmlelements');
$radio =  $this->newObject('radio', 'htmlelements');
$table =  $this->newObject('htmltable', 'htmlelements');
$button =  $this->newObject('button', 'htmlelements');

    $form->name = 'import_one';
	$form->id = 'import_one';
    $form->setAction($this->uri(array('action' => 'import_type')));
    $form->setDisplayType(2);
    
      $this->objH = $this->getObject('htmlheading', 'htmlelements');
      $this->objH->type=1;
      $this->objH->str=$this->objLanguage->languageText('mod_contextadmin_importcontent','contextadmin');
         
       $radio->name = 'itype';
       $radio->setBreakSpace('<br>');
       $radio->addOption('1',$this->objLanguage->languageText("mod_contextadmin_importdeletecontent",'contextadmin'));
       $radio->addOption('0',$this->objLanguage->languageText("mod_contextadmin_importintonode",'contextadmin'));   
       $radio->setSelected('1')  ;
       
       $table->width = "100%";
    $table->startRow();
    $table->addCell($radio->show());
    $table->startRow();
    
    $table->startRow();    
    $button->setToSubmit();    
    $button->setValue($this->objLanguage->languageText("mod_context_next",'contextadmin'));
    $table->addCell('<br>'.$button->show(),'','','center');
    
    $table->endRow();
    
    $form->addToForm($this->objH);
    $form->addToForm($table);
    
    echo $form->show();
?>