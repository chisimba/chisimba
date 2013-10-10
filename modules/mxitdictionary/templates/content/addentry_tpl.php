<?php
	/**
	 * Model extension of controller that displays the interface for adding entries
	 * @authors: Qhamani Fenama
	 * @copyright 2007 University of the Western Cape
	 * @modified to work with the mxitdictionary module by: Qhamani Fenama.
	 */
	

	$cssLayout = &$this->newObject('csslayout', 'htmlelements');
	// Set columns to 2
	$cssLayout->setNumColumns(2);
	// get the sidebar object
	$this->leftMenu = $this->newObject('usermenu', 'toolbar');
	// Initialize left column
	$leftSideColumn = $this->leftMenu->show();
	$rightSideColumn = NULL;
	$middleColumn = NULL;
	// Create link icon and link to view template
	$this->loadClass('link', 'htmlelements');
	$objIcon = $this->newObject('geticon', 'htmlelements');
	$link = new link($this->uri(array('action' => 'default')));
	$objIcon->setIcon('prev');
	$link->link = $objIcon->show();
	$update = $link->show();
	// Create header with add icon
	$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
	$pgTitle->type = 1;
	$pgTitle->str = $objLanguage->languageText('mod_mxit_return', 'mxitdictionary'). "&nbsp;" . $update;
	$this->objUser = $this->getObject('user', 'security');
	$cform = new form('mxitdictionary', $this->uri(array('action' => 'addentry')));


	//start a fieldset
	$cfieldset = $this->getObject('fieldset', 'htmlelements');
	$ct = $this->newObject('htmltable', 'htmlelements');
	$ct->cellpadding = 5;
	//value textfield
	$ct->startRow();
	$ctvlabel = new label($this->objLanguage->languageText('mod_mxit_word', 'mxitdictionary').':','input_cvalue');
	$ctv = new textinput('word');
	$ct->addCell($ctvlabel->show());
	$ct->addCell($ctv->show());
	$ct->endRow();
	//value textfield
	$ct->startRow();
	$ctvlabel = new label($this->objLanguage->languageText('mod_mxit_definition', 'mxitdictionary').':','input_cvalue');
	$ctv = new textinput('definition');
	$ct->addCell($ctvlabel->show());
	$ct->addCell($ctv->show());
	$ct->endRow();
	//end off the form and add the button
	$this->objconvButton = new button($this->objLanguage->languageText('mod_mxit_wordsave', 'mxitdictionary'));
	$this->objconvButton->setValue($this->objLanguage->languageText('mod_mxit_wordsave', 'mxitdictionary'));
	$this->objconvButton->setToSubmit();
	$cfieldset->addContent($ct->show());
	$cform->addToForm($cfieldset->show());
	$cform->addToForm($this->objconvButton->show());
	$cform = $cform->show();
	//create a featurebox and show all the input
	$objFeatureBox = $this->getObject('featurebox', 'navigation');
	$ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_mxit_add", "mxitdictionary") , $cform);
	$middleColumn = $pgTitle->show().$ret;
	// Create link back to my view template
	$objBackLink = &$this->getObject('link', 'htmlelements');
	$objBackLink->link($this->uri(array('module' => 'mxit')));
	$objBackLink->link = $objLanguage->languageText('mod_mxit_return', 'mxitdictionary');
	//add left column
	$cssLayout->setLeftColumnContent($leftSideColumn);
	$cssLayout->setRightColumnContent($rightSideColumn);
	//add middle column
	$cssLayout->setMiddleColumnContent($middleColumn);
	echo $cssLayout->show();
?>
