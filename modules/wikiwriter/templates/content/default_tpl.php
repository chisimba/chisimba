<?php

/**
*Template for the default page of the WikiWriter 
*
*@author  Ryan Whitney, ryan@greenlikeme.org
*@package wikiwriter
*/

// Load the necessary objects
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmlTable', 'htmlelements');

// Instantiate the Form and build
$objForm = new form('publisher', $this->uri(array()));

	// Add hidden fields (action, urllist)
	$objForm->addToForm(new textinput('action', 'publish', 'hidden'));
	$objForm->addToForm(new textinput('URLList', '', 'hidden'));
	
	// Create a Table for form layout
	$objFormTable = new htmltable();
		$objFormTable->border = 1;
		$objFormTable->cellspacing = 1;
		$objFormTable->cellpadding = 4;

		// On the first row, add the textarea for taking URLs
		$objFormTable->startRow();
			$taURLList = new textarea('URLLoader', NULL, 10, 60);
			$objFormTable->addCell($taURLList->show());
		$objFormTable->endRow();

		// On the second row, add the add urls and submit buttons
		$objFormTable->startRow();
			// Add URLs button
			$btnAddURLs = new button('addurls', 'Add URLs');
			$btnAddURLs->setOnClick('addURLs(this.form)');

			// Submit Button
			$btnSubmit = new button('publish', 'Publish PDF');
			$btnSubmit->setToSubmit();

			$objFormTable->addCell($btnAddURLs->show() . "&nbsp;&nbsp;&nbsp;" . $btnSubmit->show());
		$objFormTable->endRow();

	// Add table to the Form
	$objForm->addToForm($objFormTable->show());

// Print Page 
echo '<script type="text/javascript" src="modules/wikiwriter/resources/wikiwriter.js"/>';
echo $objForm->show();


?>
