<?php 
    // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("button","htmlelements");
    $this->loadClass('label','htmlelements'); 
	// Display form.
	$form = new form("main", 
		$this->uri(array(
	    	'module'=>'rubric',
			'action'=>'renametableconfirm',
			'tableId'=>$tableId
		))	
	);
	$form->setDisplayType(3);

    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=$objLanguage->languageText('rubric_renamerubric','rubric');	
	$form->addToForm($pageTitle->show());
    
    $objTable=$this->newObject('htmltable','htmlelements');
    $objTable->border='0';    
    $objTable->width='40%';
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';
    
    $row = array("<b>".$objLanguage->languageText("rubric_name","rubric")."</b>", $objUser->fullName());
    $objTable->addRow($row, 'even');
    $row = array("<b>".ucfirst($objLanguage->code2Txt('rubric_course','rubric',array('context'=>'')))."</b>", $contextTitle);
	 //made changes to fix the laguage item error	    
    //$row = array("<b>".ucfirst($objLanguage->code2Txt('rubric_course'))."</b>", $contextTitle);
    
    $objTable->addRow($row, 'even');
    $textinput = new textinput("title",$title);
    $textinput->size = 50;
    
    $labelTitle = new label($this->objLanguage->languageText("rubric_title",'rubric'),"input_title");
    $row = array("<b>".$labelTitle->show()."</b>", $textinput->show());
    $objTable->addRow($row, 'even');
    
    $textinput = new textinput("description",$description);
    $textinput->size = 70;
    
    $labelDescription = new label($this->objLanguage->languageText("rubric_description","rubric"),"input_description");
    $row = array("<b>".$labelDescription->show()."</b>", $textinput->show());    
    
    $objTable->addRow($row, 'even');
    $button = new button("submit", $objLanguage->languageText("word_save"));
    $button->setToSubmit();    
    $form->addToForm($objTable->show());    
    $form->addToForm("<br />");    
    $form->addToForm($button->show());
    
	echo $form->show();
?>
