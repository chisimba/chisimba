<?php

         $this->objH = $this->getObject('htmlheading', 'htmlelements');
    $this->objH->type=1;
    $this->objH->str=ucwords($this->objLanguage->code2Txt('mod_contextadmin_editcontext','contextadmin',array('context'=>'course')));
 
        
//load the classes
$this->objToolBar->addToBreadCrumbs(array($this->objLanguage->languageText('word_edit')));
if($this->objDBContext->isInContext())
{
      $this->setVar('footerStr',$this->getContextLinks().'&nbsp;'.$this->getContentLinks());
}
        $table= $this->newObject('htmltable','htmlelements');
        $form= $this->newObject('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $editor=$this->newObject('htmlarea','htmlelements');
         $objHelp= $this->getObject('helplink','help');
        if (!$this->getParam('contextCode')) {
            // if no context code is parsed then try to use the course you are logged in
            $contextCode = $this->objDBContext->getContextCode();            
        } else
            $contextCode=$this->getParam('contextCode');
        
        //check there is contextcode
        if (isset($contextCode)) {
            //get the result set
            $contextRS = $this->objDBContext->getRow('contextCode', $contextCode);
            //start the form
            // Header
            $this->objH = $this->getObject('htmlheading', 'htmlelements');
            $this->objH->type=1;
            $this->objH->str=$this->objLanguage->languageText('word_edit').' '.$contextRS['title'];
         
            $form = new form('edit_context');
			$form->id = 'edit_context';
            $form->setAction($this->uri(array('action' => 'save','fromwhere' => $this->getParam('fromwhere'))));
            $form->setDisplayType(2);
            
            //title
            $table->startRow();
            $title = new textinput('title');            
            $title->setValue($contextRS['title']);
            $title->size = 60;           
            $table->addCell($this->objLanguage->languageText("mod_contextadmin_title",'contextadmin'),'100');
            $table->addCell($title->show());
            $table->endRow();
            $form->addRule('title',$this->objLanguage->languageText("mod_contextadmin_err_required"), 'required');
            $form->addRule(array('name'=>'title','length'=>250),ucwords($this->objLanguage->code2Txt('mod_contextadmin_error_length','contextadmin',array('length'=>'50'))),'maxlength');
            
            //menutext
            $table->startRow();
            $menutext = new textinput('menutext');           
            $menutext->setValue($contextRS['menutext']);
            $menutext->size = 60;
            $table->addCell($this->objLanguage->languageText("mod_contextadmin_menutext",'contextadmin'));
            $table->addCell($menutext->show());
			$form->addRule('menutext',$this->objLanguage->languageText("mod_contextadmin_err_required",'contextadmin'), 'required');
            $form->addRule(array('name'=>'menutext','length'=>250),ucwords($this->objLanguage->code2Txt('mod_contextadmin_error_length',array('length'=>'50'))),'maxlength');
   
            $table->endRow();     
            
           
            //hidden contextcode
            $table->startRow();
            $contextCodeInput = new textinput('contextcode');           
            $contextCodeInput->setValue($contextRS['contextcode']);
            $contextCodeInput->fldType='hidden';
             $table->addCell($contextCodeInput->show());
            $table->endRow();     
           
           //isActive
            $table->startRow();
            $table->addCell($this->objLanguage->languageText("mod_contextadmin_status",'contextadmin'));
           
			
            $isactive = new radio('isactive');
            $isactive->addOption('1',$this->objLanguage->languageText("mod_contextadmin_active",'contextadmin'));
            $isactive->addOption('0',$this->objLanguage->languageText("mod_contextadmin_inactive",'contextadmin'));
            if($contextRS['isactive']) 
                $isactive->setSelected('1');
            else
                $isactive->setSelected('0');            
            $tmp = $isactive->show();
            
            
            //closed
            $isclosed = new radio('isclosed');
            $isclosed->addOption('1', $this->objLanguage->languageText("mod_contextadmin_isclosed",'contextadmin'));
            $isclosed->addOption('0', $this->objLanguage->languageText("mod_contextadmin_isopen",'contextadmin'));
            if($contextRS['isclosed']){
                $isclosed->setSelected('1');
            }else{
                $isclosed->setSelected('0');
            }
            $table->addCell($isclosed->show().'<br/>'.$tmp);                
            $table->endRow();     
            
            //about
            $table->startRow();
            $editor->setName('about');
            $editor->setBasicToolBar();
            $editor->context = TRUE;
            $editor->setContent(stripslashes($contextRS['about']));
            $editor->width = '325';
            $editor->height = '200';
            $helpstr = "&nbsp;".$objHelp->show('mod_html_help_editor');
            $table->addCell($this->objLanguage->languageText("mod_contextadmin_about",'contextadmin').$helpstr);
            $table->addCell($editor->show()); 
            $table->endRow();                
          
          
            
            //submit button
            $table->startRow();
            $button = new button();
            $button->setToSubmit();
            $button->setValue($this->objLanguage->languageText("mod_contextadmin_save",'contextadmin'));
            $table->addCell("");
            $table->addCell($button->show()); 
            $table->endRow();                            
            
            
            $form->addToForm($table);
            $showedit= $form->show();
            //add  link  
            
    $objLink =  $this->newObject('link','htmlelements');
    $objLink->cssClass = 'pseudbutton';
    $objLink->href = $this->uri(array(), 'contextadmin');
    $objLink->link = $this->objLanguage->languageText("word_back");
    $showedit .= '<br/>'.$objLink->show(); 

    		//$objConfig=&$this->newObject('config','config');
	    	//$siteRoot=$objConfig->siteRoot();

    //$_SESSION['formname'] = 'edit_context';
    //$_SESSION['textareaname'] = 'about';
           
            /*           
            $button = new button(
                "insertimage", 
                "Insert Image", 
                "window.open('".$siteRoot."index.php?module=contextview&action=contextlist','InsertImage','width=500,height=300,toolbar=0,status=0');"
            );
            $showedit.=$button->show();
            */

    //$showedit.='<input type="button" name="ins" value="  insert html  " onclick="return insertHTML();" />';
            
            
	}
    $center =     $this->objH->show();
        
    $center  .= $showedit;
    $this->contentNav =  $this->newObject('layer','htmlelements');
    $this->contentNav->id = "content";
    $this->contentNav->addToStr($center);
    echo $this->contentNav->show();
?>