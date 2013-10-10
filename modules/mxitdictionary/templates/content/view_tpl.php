<?php
/**
 * @Model extension of controller that displays entries
 * @authors:Qhamani Fenama
 * @copyright 2007 University of the Western Cape
 */
	// Create an instance of the css layout class
	$cssLayout = $this->newObject('csslayout', 'htmlelements');
	// Set columns to 2
	$cssLayout->setNumColumns(4);
	// get the sidebar object
	$this->leftMenu = $this->newObject('usermenu', 'toolbar');
	$this->loadClass ( 'htmlheading', 'htmlelements' );
	$this->objImView = $this->getObject ( 'viewer' );
	$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
	$objWashout = $this->getObject ( 'washout', 'utilities' );
	// Initialize left column
	$leftSideColumn = $this->leftMenu->show();
	$rightSideColumn = NULL;
	$middleColumn = NULL;

		if($objUser->isAdmin())
			{
				$objAddIcon = $this->newObject('geticon', 'htmlelements');
				$objLink = $this->uri(array('action' => 'link'));
				$objAddIcon->setIcon("add", "gif");
				$objAddIcon->alt = $objLanguage->languageText('mod_mxit_addicon', 'mxitdictionary');
				$add = $objAddIcon->getAddIcon($objLink);

				//add the notification link
				$objAddIcon1 = $this->newObject('geticon', 'htmlelements');
				$objLink1 = $this->uri(array('action' => 'link3'));
				$objAddIcon1->setIcon("view", "gif");
				$objAddIcon1->alt = $objLanguage->languageText('mod_mxit_viewicon', 'mxitdictionary');
				$add1 = $objAddIcon1->getAddIcon($objLink1);
					
					$pgTitle1 = &$this->getObject('htmlheading', 'htmlelements');
					$pgTitle1->type = 1;
					$pgTitle1->str = $objLanguage->languageText('mod_mxit_ahead', 'mxitdictionary'). "&nbsp;" .$add."<br/>".$objLanguage->languageText('mod_mxit_not', 'mxitdictionary').$add1;
			}
			else
			{
				$objAddIcon = $this->newObject('geticon', 'htmlelements');
				$objLink = $this->uri(array('action' => 'link2'));
				$objAddIcon->setIcon("add", "gif");
				$objAddIcon->alt = $objLanguage->languageText('mod_mxit_addicon', 'mxitdictionary');
				$add = $objAddIcon->getAddIcon($objLink);
				// Create header with add icon
					$pgTitle1 = &$this->getObject('htmlheading', 'htmlelements');
					$pgTitle1->type = 1;
					$pgTitle1->str = $objLanguage->languageText('mod_mxit_head', 'mxitdictionary'). "&nbsp;" . $add. "&nbsp;";
			}	
		

			//create array to hold data and set the language items
			$tableRow = array();
			$tableHd[] = $objLanguage->languageText('mod_mxit_word', 'mxitdictionary');
			$tableHd[] = $objLanguage->languageText('mod_mxit_definition', 'mxitdictionary');
			//check if the user is admin
			if($objUser->isAdmin())
			{
			$tableHd[] = $objLanguage->languageText('mod_mxit_delete', 'mxitdictionary');
			$tableHd[] = $objLanguage->languageText('mod_mxit_edit', 'mxitdictionary');
			}
		 
			//Create the table header for display
			$objTableClass = $this->newObject('htmltable', 'htmlelements');
			$objTableClass->addHeader($tableHd, "heading", '', 'left', 'left');
			$index = 0;
			$rowcount = 0;
			

	$ret = $objTableClass->show();
	$middleColumn = $pgTitle1->show().$ret;
	$middleColumn .= $this->objImView->renderTopBoxen();

	$leftColumn = NULL;
	$rightColumn = NULL;

	$objPagination = $this->newObject ( 'pagination', 'navigation' );
	$objPagination->module = 'mxitdictionary';
	$objPagination->action = 'viewallajax';
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
