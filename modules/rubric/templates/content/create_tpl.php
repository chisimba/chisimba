<?php 
    // Load classes
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
   $this->loadClass('radio', 'htmlelements');
	$this->loadClass("dropdown","htmlelements");
	$this->loadClass("button","htmlelements");
   $this->loadClass('label','htmlelements'); 
	    
    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=$objLanguage->languageText('rubric_createrubric','rubric');	
    
    $createForm = $this->newObject('form','htmlelements');
    $createForm->name="main";
    $createForm->action=$this->uri(array(
	    	'module'=>'rubric',
			'action'=>'createtableconfirm',
			'type'=>$_type));

	$createForm->setDisplayType(3);
	$createForm->addToForm($pageTitle->show());
    
    $objTable=$this->newObject('htmltable','htmlelements');    
    $objTable->border='0';
    $objTable->width='40%';    
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';
    
    $row = array("<b>".$objLanguage->languageText("rubric_name","rubric")."</b>", $objUser->fullName());
    $objTable->addRow($row, 'even');
    $row = array("<b>".ucfirst($objLanguage->code2Txt('rubric_course',"rubric",array('context'=>'')))."</b>", $contextTitle);
    $objTable->addRow($row, 'even');
    $textinput = new textinput("title","");
    $textinput->size = 50;
    $labelTitle = new label($this->objLanguage->languageText("rubric_title","rubric"),"input_title");    
    $row = array("<b>". $labelTitle->show()."</b>", $textinput->show());
    $objTable->addRow($row, 'even');
    $textinput = new textinput("description","");
    $textinput->size = 70;    
    $labelDescription = new label($this->objLanguage->languageText("rubric_description","rubric"),"input_description");
    $row = array("<b>" . $labelDescription->show() . "</b>", $textinput->show());
    $objTable->addRow($row, 'even');
    $dropdown = new dropdown("rows");
    $dropdown->addOption("1", "1");
    $dropdown->addOption("2", "2");
    $dropdown->addOption("3", "3");
    $dropdown->addOption("4", "4");
    $dropdown->addOption("5", "5");
    $dropdown->addOption("6", "6");
    $dropdown->addOption("7", "7");
    $dropdown->addOption("8", "8");
    $dropdown->addOption("9", "9");    
    $labelObjective = new label($this->objLanguage->languageText("word_objectives","rubric"),"input_rows");
    $row = array("<b>" . $labelObjective->show() . "</b>", $dropdown->show());
    $objTable->addRow($row, 'even');
    $dropdown = new dropdown("cols");
    $dropdown->addOption("1", "1");
    $dropdown->addOption("2", "2");
    $dropdown->addOption("3", "3");
    $dropdown->addOption("4", "4");
    $dropdown->addOption("5", "5");
    $dropdown->addOption("6", "6");
    $dropdown->addOption("7", "7");
    $dropdown->addOption("8", "8");
    $dropdown->addOption("9", "9");
    $labelPerformance = new label($this->objLanguage->languageText("word_performance","rubric"),"input_cols");
    $row = array("<b>".$labelPerformance->show()."</b>", $dropdown->show());
    $objTable->addRow($row, 'even');
    $button = new button("submit", $objLanguage->languageText("word_submit")); //word_create
    $button->setToSubmit();
    
    $cancelButton = new button("cancel", $objLanguage->languageText("word_cancel")); //word_create
    $cancelButton->setOnClick("window.location='".$this->uri(NULL)."'");
    
    //$row = array($button->show());
    
    //$objTable->addRow($row, 'even');	
    $createForm->addToForm($objTable->show());
    $createForm->addToForm("<br />");
    $createForm->addToForm('<p>'.$button->show().' / '.$cancelButton->show().'</p>');
    
    $createForm->addRule('title', $objLanguage->languageText('mod_rubric_pleaseentertitle',"rubric", 'Please enter a title for the rubric.'),'required');
    $createForm->addRule('description', $objLanguage->languageText('mod_rubric_pleaseenterdescription',"rubric",'Please enter a description for the rubric.'),'required');
    
	echo $createForm->show();    
?>
