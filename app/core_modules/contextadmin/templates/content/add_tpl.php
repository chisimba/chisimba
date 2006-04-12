<?php

    $this->objH =& $this->getObject('htmlheading', 'htmlelements');
    $this->objH->type=1;
    $this->objH->str=ucwords($this->objLanguage->code2Txt('mod_contextadmin_addnewcontext',array('context'=>'course')));
    

    //setting up the instances of the htmlelements to be used in creating tables, buttons, textfield, buttons etc.
    $objLang=& $this->getObject('language', 'language');
    
    
    $this->loadClass('radio','htmlelements');
    $this->loadClass('form','htmlelements');
    $this->loadClass('textinput','htmlelements');
    $this->loadClass('checkbox','htmlelements');                    
    $this->loadClass('button','htmlelements');                    
    $editor=&$this->newObject('htmlarea','htmlelements');
    $table=& $this->newObject('htmltable','htmlelements');
    
    //setup form
    $objForm = $this->newObject('form','htmlelements');
    $objForm->name='testform';
    $objForm->setAction($this->uri(array('action'=>'create'),'contextadmin'));
    $objForm->setDisplayType(3);    
    
    //add context code
    $table->startRow();
    $objElement = new textinput('contextCode');   
    $objElement->size = '20';
    $objElement->searchField='contextCode';
    $table->addCell(ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextcode',array('context'=>'course'))),'100px');
    $table->addCell($objElement->show());   
    $table->endRow();
    $objForm->addRule('contextCode',$this->objLanguage->languageText("mod_contextadmin_err_required",'contextadmin'), 'required');
    $objForm->addRule(array('name'=>'contextCode','length'=>20),ucwords($this->objLanguage->code2Txt('mod_contextadmin_error_length',array('length'=>'20'))),'maxlength');
    
    //add title
    $table->startRow();
    $objElement = new textinput('title');     
    $objElement->size = '55';
    $table->addCell($this->objLanguage->languageText("mod_contextadmin_title,'contextadmin'));
    $table->addCell($objElement->show());
    $table->endRow();
    $objForm->addRule('title',$this->objLanguage->languageText("mod_contextadmin_err_required",'contextadmin','contextadmin'), 'required');
    $objForm->addRule(array('name'=>'title','length'=>250),ucwords($this->objLanguage->code2Txt('mod_contextadmin_error_length',array('length'=>'50'))),'maxlength');
    
    //add menu text
    $table->startRow();
    $objElement = new textinput('menutext');
    $objElement->size = '55';
    $table->addCell($this->objLanguage->languageText("mod_contextadmin_menutext",'contextadmin'));
    $table->addCell($objElement->show()."<br>");
    $table->endRow();
    $objForm->addRule('menutext',$this->objLanguage->languageText("mod_contextadmin_err_required",'contextadmin'), 'required');
    $objForm->addRule(array('name'=>'menutext','length'=>250),ucwords($this->objLanguage->code2Txt('mod_contextadmin_error_length',array('length'=>'50'))),'maxlength');

    //add isclosed
    $table->startRow();
    $objElement = new radio('isclosed');
    $objElement->addOption('0',$this->objLanguage->languageText("mod_contextadmin_isopen",'contextadmin'));
    $objElement->addOption('1',$this->objLanguage->languageText("mod_contextadmin_isclosed",'contextadmin'));
    
    $tmp = $objElement->show();
    //add isactive
    $objElement = new radio('isactive');
    $objElement->addOption('1',$this->objLanguage->languageText("mod_contextadmin_active",'contextadmin'));
    $objElement->addOption('0',$this->objLanguage->languageText("mod_contextadmin_inactive",'contextadmin'));
    $objElement->setSelected('1');
    
    $table->addCell($this->objLanguage->languageText("mod_contextadmin_status",'contextadmin'));
    $table->addCell($objElement->show().'<br>'. $tmp);
    $table->endRow();
    
    //about
    $table->startRow();
    $editor->setName('about');
    $editor->setBasicToolBar();
    $editor->context = TRUE;
    $editor->width = '300';
    $editor->height = '200';
    $table->addCell($this->objLanguage->languageText("mod_contextadmin_about",'contextadmin'));
    $table->addCell($editor->show()); 
    $table->endRow();    
    
    //submit button
    $table->startRow();
    $objElement = new button('mybutton');    
    $objElement->setToSubmit();    
    $objElement->setValue($this->objLanguage->languageText("mod_contextadmin_save",'contextadmin'));
    $table->addCell("");
    $table->addCell($objElement->show());
    $table->endRow();
    
    //add  link  
    $objLink = & $this->newObject('link','htmlelements');
    $objLink->cssClass = 'pseudbutton';
    $objLink->href = $this->uri(array(), 'contextadmin');
    $objLink->link = $this->objLanguage->languageText("word_back",'contextadmin');
   
    $objForm->addToForm($table);
    $center = $this->objH->show();
    $center .=  $objForm->show();
    $center .=  $objLink->show();
    
    $this->contentNav = & $this->newObject('layer','htmlelements');
    $this->contentNav->id = "content";
    $this->contentNav->addToStr($center);
    echo $this->contentNav->show();
    
    

?>