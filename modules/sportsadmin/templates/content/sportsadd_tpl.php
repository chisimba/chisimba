<?php  
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("textarea","htmlelements");
	$this->loadClass('hiddeninput','htmlelements');
	$this->loadClass("button","htmlelements");
	//$objLabel =& $this->newObject('label', 'htmlelements');
	$objHeading =& $this->getObject('htmlheading','htmlelements');
	$objHeading->type=1;
	$objHeading->str =$objLanguage->languageText('mod_sports_add','sports');

	//check whether we are editing or adding
	$useEdit=0;
	$useEdit=$this->getParam('useEdit', NULL);
	if($useEdit) {
		$id=0;
		$id=$this->getParam('id', NULL);
	}
	//create form class
	$objForm = new form('addForm');
		
//Add the form title to the form
	$objForm->addToForm('<div align="center" />');
	$this->objH =& $this->getObject('htmlheading', 'htmlelements');
	$this->objH->type=2; 
	$this->objH->align="center";

	if(!$useEdit) {
	$this->objH->str=$objLanguage->languageText('mod_sports_add','sports');
	}
	else {
	$this->objH->str=$objLanguage->languageText('mod_sports_modify','sports')." ".$this->objDBSports->getSportsById($id);
	}

	$objForm->addToForm($this->objH->show());
	
	//table to handle the form elements
	$objTable =& $this->newObject('htmltable','htmlelements');
	$objTable->width='80%';
	$objTable->attributes=" align='center'";
	$objTable->cellspacing='2';
	$objTable->cellpadding='2'; 
	$objTable->border='0';
		
	//row for the textinput
	$objTable->startRow();
	$objTextInput = new textinput('name');
	$objTextInput->size="53%";
	$objTextInput->value=($useEdit?$this->objDBSports->getSportsById($id):'');
	$objTable->addCell($objLanguage->languageText('mod_sports_sportname','sports'),"",NULL,"right");
	$objTable->addCell($objTextInput->show());
	$objTable->endRow();	
    
	//field for evaluation mode of the sport
	$evaluation = new textinput('evaluation',$this->objLanguage->languageText('mod_sportsadmin_goals','sportsadmin'));
	$evaluation->size = "53%";
    
	$objTable->startRow();
	$objTable->addCell($this->objLanguage->languageText('mod_sportsadmin_evaluation','sportsadmin'),"",NULL,"right");
	$objTable->addCell($evaluation->show());
	$objTable->endRow();
	
	//player number field
	$playerno =  new textinput('player_no');
	$playerno->size = "53%";
	
	$objTable->startRow();
	$objTable->addCell($this->objLanguage->languageText('mod_sportsadmin_playerno','sportsadmin'),"40%","top","right");
	$objTable->addCell($playerno->show());
	$objTable->endRow();
	 
	//row for the textarea
	$objTable->startRow();
	$objTextArea = new textarea('description');
	$objTextArea->value=($useEdit?$this->objDBSports->getSportsDescriptionById($id):'');
	$objTable->addCell($objLanguage->languageText('mod_sports_description','sports'),"40%","top","right");
	$objTable->addCell($objTextArea->show());
	$objTable->endRow();
	
    $this->loadClass('hiddeninput','htmlelements');
	
	//Save button
	$objTable->startRow();
	$objButton = $this->newObject('button', 'htmlelements');
	$objHidden = new hiddeninput('action',($useEdit?'modify':'save'));
	
	if($useEdit) {
	  $objHiddenId = new hiddeninput('id',$id);
	}
	$objButton = new button("save",$objLanguage->languageText("word_save"));   
	$objButton->setToSubmit();
	$objTable->addCell('');
	$objTable->addCell($objButton->show().''.$objHidden->show().''.($useEdit?$objHiddenId->show():''));
	
	
	$row = array( "<a href=\"". $this->uri(array('module'=>'sports',)). "\">".$objLanguage->languageText("word_cancel") . "</a>");	//word_cancel

	$objTable->addRow($row, '');
	$objTable->endRow();
	
	$objForm->addToForm($objTable->show());
	
	echo $objForm->show();
?>
