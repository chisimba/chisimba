<?php
/**
 * @Model extension of controller that displays entries
 * @authors:Qhamani Fenama
 * @copyright 2007 University of the Western Cape
	 */
	//Create an instance of the css layout class
	$cssLayout = $this->newObject('csslayout', 'htmlelements');
	// Set columns to 2
	$cssLayout->setNumColumns(2);
	//get the sidebar object
	$this->leftMenu = $this->newObject('usermenu', 'toolbar');
	$this->loadClass ( 'htmlheading', 'htmlelements' );
	$this->objImView = $this->getObject ( 'sugviewer' );
	$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
	$objWashout = $this->getObject ( 'washout', 'utilities' );
	//Initialize left column
	$leftSideColumn = $this->leftMenu->show();
	$rightSideColumn = NULL;
	$middleColumn = NULL;

		//Create link icon and link to view template
				$this->loadClass('link', 'htmlelements');
				$objIcon = $this->newObject('geticon', 'htmlelements');
				$link = new link($this->uri(array(
					'action' => 'default'
				)));
				$objIcon->setIcon('prev');
				$link->link = $objIcon->show();
				$update = $link->show();
				// Create header with add icon and set the action
				$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
				$pgTitle->type = 1;
				$pgTitle->str = $objLanguage->languageText('mod_mxit_return', 'mxitdictionary') . "&nbsp;" . $update;
				//create array to hold data and set the language items
				$tableRow = array();
				$tableHd[] = $objLanguage->languageText('mod_mxit_word', 'mxitdictionary');
				$tableHd[] = $objLanguage->languageText('mod_mxit_definition', 'mxitdictionary');
				$tableHd[] = $objLanguage->languageText('mod_mxit_approve', 'mxitdictionary');
				$tableHd[] = $objLanguage->languageText('mod_mxit_reject', 'mxitdictionary');
		 
			//Create the table header for display
			$objTableClass = $this->newObject('htmltable', 'htmlelements');
			$objTableClass->addHeader($tableHd, "heading", '', 'left', 'left');
			$index = 0;
			$rowcount = 0;
			

	$ret = $objTableClass->show();
	$middleColumn = $pgTitle->show().$ret;
	$middleColumn .= $this->objImView->renderTopBoxen();

	$leftColumn = NULL;
	$rightColumn = NULL;

	$objPagination = $this->newObject ( 'pagination', 'navigation' );
	$objPagination->module = 'mxitdictionary';
	$objPagination->action = 'sugviewallajax';
	$objPagination->id = 'mxitdictionary';
	$objPagination->numPageLinks = $pages;
	$objPagination->currentPage = $pages - $pages;

	$middleColumn .= '<br/>'. $objPagination->show().'<br/>';

	$leftColumn .= $this->objImView->renderLeftBoxen();
	$rightColumn .= $this->objImView->renderRightBoxen();

	$cssLayout->setLeftColumnContent($leftSideColumn);
	$cssLayout->setRightColumnContent($rightSideColumn);
	//add middle column
	$cssLayout->setMiddleColumnContent($middleColumn);
	echo $cssLayout->show();
?>
