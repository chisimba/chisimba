<?php
    // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("button","htmlelements");
	$this->loadClass("htmltable", 'htmlelements');
	$this->loadClass('dropdown', 'htmlelements');
//	$objLabel =& $this->newObject('label', 'htmlelements');
	$objWindow =& $this->newObject('windowpop','htmlelements');
	$objHeading =& $this->getObject('htmlheading','htmlelements');
	$objHeading->type=1;
	$objHeading->str =$objLanguage->languageText("mod_readinglist_add",'readinglist');
	echo $objHeading->show();
	$form = new form("add",
		$this->uri(array(
	    		'module'=>'readinglist',
	   		'action'=>'addConfirm'
	)));
	$objTable = new htmltable();
	$objTable->width='30';
	$objTable->attributes=" align='center' border='0'";
	$objTable->cellspacing='12';
	$objTable->cellpadding='12';
	$row = array("<b>".$objLanguage->code2Txt("word_name").":</b>",
	$objUser->fullName());
	$objTable->addRow($row, 'odd');
	$row = array("<b>".ucwords($objLanguage->code2Txt("mod_context_context", 'context')).":</b>",
	$contextTitle);
	$objTable->addRow($row, 'odd');

    	//Author text box
	$textinput = new textinput("author","");
	$textinput->size = 70;
	$row=array("<b>".$label = $objLanguage->languageText("mod_readinglist_author",'readinglist').":</b>",
	$textinput->show());

    	//Title text box
	$objTable->addRow($row, 'even');
	$textinput = new textinput("title","");
	$textinput->size = 70;
	$row = array("<b>".$label = $objLanguage->languageText("mod_readinglist_title",'readinglist').":</b>",
	$textinput->show());

 	//Publisher text field
	$objTable->addRow($row, 'even');
	$textinput = new textinput("publisher","");
	$textinput->size = 70;
	$row = array("<b>".$label = $objLanguage->languageText("mod_readinglist_publisher",'readinglist').":</b>",
	$textinput->show());

 	//Publishing Year select field
	$objTable->addRow($row, 'even');
 	$table = $this->newObject('htmltable', 'htmlelements');
	$addDropdown = new dropdown('publishingYear');
	$year=array("yr_80", "yr_81", "yr_82", "yr_83", "yr_84", "yr_85", "yr_86", "yr_87", "yr_88", "yr_89", "yr_90", "yr_91", "yr_92", "yr_93", "yr_94", "yr_95", "yr_96", "yr_97", "yr_98", "yr_99", "yr_00", "yr_01", "yr_02", "yr_03", "yr_04", "yr_05", "yr_06", "yr_07", "yr_08", "yr_09", "yr_10", "yr_11", "yr_12", "yr_13");
	foreach ($year as $row)
	{
    		$row=trim($this->objLanguage->languageText($row));
    		$addDropdown->addOption($row,$row);
	}

	$table->startRow();
	$row=array("<b>".$label = $objLanguage->languageText("mod_readinglist_year",'readinglist')."</b>",
	$addDropdown->show());
	$table->addCell($addDropdown->show());
	$table->endRow();




/*	$textinput = new textinput("publishingYear","");
	$textinput->size = 4;
	$row = array("<b>".$label = $objLanguage->languageText("mod_readinglist_year",'readinglist').":</b>",
	$textinput->show());
*/
    	//Link text field
	$objTable->addRow($row, 'even');
	$textinput = new textinput("link","");
	$textinput->size = 70;
	$row = array("<b>".$label = $objLanguage->languageText("mod_readinglist_link",'readinglist').":</b>",
	$textinput->show());

	//Publication text field
	$objTable->addRow($row, 'even');
	$textinput = new textinput("publication","");
	$textinput->size = 70;
	$row = array("<b>".$label = $objLanguage->code2Txt("mod_readinglist_publication",'readinglist').":</b>",
	$textinput->show());
	$objTable->addRow($row, 'even');

 	//Country select box
	$objTable->startRow();
   	$countries=&$this->getObject('languagecode','language');
    	$objTable->addCell($this->objLanguage->languageText("mod_readinglist_country", 'readinglist'));
        $objTable->addCell($countries->countryAlpha($this->getParam('country')));
	$objTable->endRow();

 	//Language text box
	$textinput = new textinput("language","");
	$textinput->size = 70;
	$row = array("<b>".$label = $objLanguage->languageText("mod_readinglist_language",'readinglist').":</b>",
	$textinput->show());
	$objTable->addRow($row, 'even');

    	//Save button
	$button = new button("submit",
	$objLanguage->languageText("word_save"));    //word_save
	$button->setToSubmit();
	$row = array($button->show());
	$objTable->addRow($row, 'even');
	$row = array( "<a href=\"". $this->uri(array(
	'module'=>'readinglist',)). "\">".
	$objLanguage->languageText("word_cancel") . "</a>");	//word_cancel
	$objTable->addRow($row, 'even');
	$form->addToForm($objTable->show());
	echo $form->show();
?>
