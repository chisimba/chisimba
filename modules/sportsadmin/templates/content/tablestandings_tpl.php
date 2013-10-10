<?php
  
	$this->setLayoutTemplate('sports_layout_tpl.php');
	  // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("textarea","htmlelements");
	$this->loadClass('hiddeninput','htmlelements');
	$this->loadClass("button","htmlelements");
	$objLabel =& $this->newObject('label', 'htmlelements');
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
	$objForm->displayType=3; 
		
//Add the form title to the form
	$objForm->addToForm('<div align="center">');
	$this->objH =& $this->getObject('htmlheading', 'htmlelements');
	$this->objH->type=3; //Heading <h3>
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
	$objTable->attributes=" align='center' border=0";
	$objTable->cellspacing='2';
	$objTable->cellpadding='2'; 
		
	//row for the textinput
	$objTable->startRow();
	$objTextInput = new textinput('name');
	$objTextInput->size="53%";
	$objTextInput->value=($useEdit?$this->objDBSports->getSportsById($id):'');
	$objTable->addCell($objLanguage->languageText('mod_sports_sportname','sports'),"",NULL,"right");
	$objTable->addCell($objTextInput->show());
	$objTable->endRow();	
	
		
	//row for the textarea
	$objTable->startRow();
	$objTextArea = new textarea('description');
	$objTextArea->value=($useEdit?$this->objDBSports->getSportsDescriptionById($id):'');
	$objTable->addCell($objLanguage->languageText('mod_sports_description','sports'),"40%","top","right");
	$objTable->addCell($objTextArea->show());
	$objTable->endRow();

	//Save button
	$objTable->startRow();
	$objButton = $this->newObject('button', 'htmlelements');
	$objHidden = new hiddeninput('action',($useEdit?'modify':'save'));
	if($useEdit) {
	$objHiddenId = new hiddeninput('id',$id);
	}
	$objButton = new button("save",$objLanguage->languageText('word_save','system'));   
	$objButton->setToSubmit();
	$objTable->addCell($objButton->show().''.$objHidden->show().''.($useEdit?$objHiddenId->show():''));
	
	
	$row = array( "<a href=\"". $this->uri(array('module'=>'sports',)). "\">".$objLanguage->languageText('word_cancel','system') . "</a>");	//word_cancel
	$objTable->addRow($row, '');
	
	$objForm->addToForm($objTable->show());
	
	echo $objForm->show();
?>
