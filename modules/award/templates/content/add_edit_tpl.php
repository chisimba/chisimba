<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS SOC
*/

/**
* Edit template for the LRS SOC
* Author Warren Windvogel, Brent van Rensburg
*/
//Set layout template
$this -> setLayoutTemplate('layout_tpl.php');

//Create htmlheading for page header
$objH = $this->newObject('htmlheading', 'htmlelements');
$objH->type = '1';

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textinput class
$this->loadClass('textarea', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the label class
$this->loadClass('htmltable', 'htmlelements');

//Create table for edit form
$objFormTable = $this->newObject('htmltable', 'htmlelements');
$objFormTable->cellspacing = '2';
$objFormTable->cellpadding = '2';
$objFormTable->width = '90%';

$objEditGroupForm = new form('editSocmajorgroup', '');
		
switch($groupName)
{

   case 'major group':

    	if($majorGroupId)
    	{
        	$objH->str = $this->objLanguage->languageText('mod_lrssoc_editmajorgroup','award');
        	//Get major group data
        	$majorGroup = $this->objDbSocMajorGroup->getRow('id', $majorGroupId);
	        $majorGroupId = $majorGroup['id'];
	        $majorGroupDesc = $majorGroup['description'];
	        
	        //Set form action
	        $formAction = $this->uri(array('action'=>'savemajorgroup', 'id'=>$majorGroupId,'selected'=>'init_10'),'award');
			//Create new form object
			$objEditGroupForm = new form('editSocmajorgroup', $formAction);
			$objEditGroupForm->displayType = '3';
    	}
    	else 
    	{
    		//Set page header
       		$objH->str = $this->objLanguage->languageText('mod_lrssoc_addmajorgroup','award');
       		$majorGroupId = '';
       		$majorGroupDesc = '';
       		//Set form action
	        $formAction = $this->uri(array('action'=>'savemajorgroup', 'selected'=>'init_10'),'award');
			//Create new form object
			$objEditGroupForm = new form('editSocmajorgroup', $formAction);
			$objEditGroupForm->displayType = '3';
    	}
    	
        
		$txtId = new textinput("id", $majorGroupId, 'hidden');
		$txtDescription = new textarea('description', $majorGroupDesc);
		
		// Create a submit button
		$objSubmit = new button('submit'); 
		// Set the button type to submit
		$objSubmit->setToSubmit(); 
		// Use the language object to add the word
		$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_submit") . ' ');
		
		$btnCancel = new button('cancel');
		$location = $this->uri(array('action'=>'selectmajorgroup','selected'=>'init_10'), 'award');
		$btnCancel->setOnClick("javascript:window.location='$location'");
		$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');
		
		$objaddeditHeadTable = new htmlTable('lrsoc');
		$objaddeditHeadTable->cellspacing = 2;
		$objaddeditHeadTable->cellpadding = '2';
		$objaddeditHeadTable->width = '90%';
		
		$objaddeditHeadTable->startRow();
		$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrssoc_tbl_head_majgroup','award')."</i>");
		$objaddeditHeadTable->addCell("");
		$objaddeditHeadTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell($this->objLanguage->languageText('word_description'). ':','','top','','odd');
		$objFormTable->addCell($txtDescription->show());
		$objFormTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell("<br />");
		$objFormTable->addCell("<br />");
		$objFormTable->endRow();

		$objFormTable->startRow();
		$objFormTable->addCell($objSubmit->show().'  '.$btnCancel->show());
		$objFormTable->addCell($txtId->show());
		$objFormTable->endRow();

		//Add validation here
		$objEditGroupForm->addRule('description', $this->objLanguage->languageText('mod_lrssoc_valrequired'), 'required');
	
		//Add table to 
		$objEditGroupForm->addToForm($objaddeditHeadTable->show());
		$objEditGroupForm->addToForm($objFormTable->show());
		break;

    case 'sub major group':
    
    	if($subMajorGroupId)
    	{
        	$objH->str = $this->objLanguage->languageText('mod_lrssoc_editsubmajorgroup','award');
        	$subMajorGroup = $this->objDbSubMajorGroups->getRow('id', $subMajorGroupId);
	        $subMajorGroupDesc = $subMajorGroup['description'];
	        //Set form action
		    $formAction = $this->uri(array('action'=>'savesubmajorgroup', 'majorGroupId'=>$majorGroupId, 'id'=>$subMajorGroupId,'selected'=>'init_10'),'award');
			//Create new form object
			$objEditGroupForm = new form('savesubmajorgroup', $formAction);
			$objEditGroupForm->displayType = '3';
    	} else {
    		//Set page header
       		$objH->str = $this->objLanguage->languageText('mod_lrssoc_addsubmajorgroup','award');
       		$subMajorGroupDesc = '';
       		//Set form action
	        $formAction = $this->uri(array('action'=>'savesubmajorgroup', 'majorGroupId'=>$majorGroupId, 'selected'=>'init_10'),'award');
			//Create new form object
			$objEditGroupForm = new form('savesubmajorgroup', $formAction);
			$objEditGroupForm->displayType = '3';
    	}
    	
    	
    	$txtDescription = new textarea('description', $subMajorGroupDesc);
		
		// Create a submit button
		$objSubmit = new button('submit'); 
		// Set the button type to submit
		$objSubmit->setToSubmit(); 
		// Use the language object to add the word
		$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_submit") . ' ');
		
		$btnCancel = new button('cancel');
		$location = $this->uri(array('action'=>'selectsubmajorgroup', 'majorGroupId'=>$majorGroupId,'selected'=>'init_10'), 'award');
		$btnCancel->setOnClick("javascript:window.location='$location'");
		$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');
		
		$objaddeditHeadTable = new htmlTable('lrsoc');
		$objaddeditHeadTable->cellspacing = 2;
		$objaddeditHeadTable->cellpadding = '2';
		$objaddeditHeadTable->width = '90%';
		
		$objaddeditHeadTable->startRow();
		$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrssoc_tbl_head_submajgroup','award')."</i>");
		$objaddeditHeadTable->addCell("");
		$objaddeditHeadTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell($this->objLanguage->languageText('word_description'). ':','','top','','odd');
		$objFormTable->addCell($txtDescription->show());
		$objFormTable->endRow();

		$objFormTable->startRow();
		$objFormTable->addCell($objSubmit->show().'  '.$btnCancel->show());
		$objFormTable->endRow();

		//Add validation here
		$objEditGroupForm->addRule('description', $this->objLanguage->languageText('mod_lrssoc_valrequired'), 'required');
	
		//Add table to form
		$objEditGroupForm->addToForm($objaddeditHeadTable->show());
		$objEditGroupForm->addToForm($objFormTable->show());
        break;

    case 'minor group':  
    	if($minorGroupId)
    	{
        	$objH->str = $this->objLanguage->languageText('mod_lrssoc_editminorgroup','award');
        	$minorGroup = $this->objDbMinorGroups->getRow('id', $minorGroupId);
	        $minorGroupDesc = $minorGroup['description'];
	        
	        //Set form action
		    $formAction = $this->uri(array('action'=>'saveminorgroup', 'minor_groupid'=>$minorGroup['id'],
										   'submajor_groupid'=>$subMajorGroupId, 'major_groupid'=>$majorGroupId,
										   'selected'=>'init_10'),'award');
    	} else {
    		//Set page header
       		$objH->str = $this->objLanguage->languageText('mod_lrssoc_addminorgroup','award');
       		//Get sub major group data
	      //  $majorGroupId = '';
	        $minorGroupId = '';
	        $minorGroupDesc = '';
       		//Set form action
	        $formAction = $this->uri(array('action'=>'saveminorgroup', 'submajor_groupid'=>$subMajorGroupId,
										   'major_groupid'=>$majorGroupId, 'selected'=>'init_10'),'award');
    	}
    	
		$objEditGroupForm = new form('saveminorgroup', $formAction);
		$objEditGroupForm->displayType = '3';
		
		$txtDescription = new textarea('description', $minorGroupDesc);
		
		// Create a submit button
		$objSubmit = new button('submit'); 
		// Set the button type to submit
		$objSubmit->setToSubmit(); 
		// Use the language object to add the word
		$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_submit") . ' ');
		
		$btnCancel = new button('cancel');
		$location = $this->uri(array('action'=>'selectminorgroup', 'submajor_groupid'=>$subMajorGroupId,'selected'=>'init_10'), 'award');
		$btnCancel->setOnClick("javascript:window.location='$location'");
		$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');
		
		$objaddeditHeadTable = new htmlTable('lrsoc');
		$objaddeditHeadTable->cellspacing = 2;
		$objaddeditHeadTable->cellpadding = '2';
		$objaddeditHeadTable->width = '90%';
		
		$objaddeditHeadTable->startRow();
		$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrssoc_tbl_head_mingroup', 'award')."</i>");
		$objaddeditHeadTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell($this->objLanguage->languageText('word_description'). ':','','top','','odd');
		$objFormTable->addCell($txtDescription->show());
		$objFormTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell($objSubmit->show().'  '.$btnCancel->show());
		$objFormTable->endRow();
	
		//Add validation here
		$objEditGroupForm->addRule('description', $this->objLanguage->languageText('mod_lrssoc_valrequired','award'), 'required');
	
		//Add table to form
		$objEditGroupForm->addToForm($objaddeditHeadTable->show());
		$objEditGroupForm->addToForm($objFormTable->show());
        break;

    case 'unit group':
    	if(isset($unitGroupId)) {
        	$objH->str = $this->objLanguage->languageText('mod_lrssoc_editunitgroup','award');
        	$unitGroup = $this->objDbUnitGroups->getRow('id', $unitGroupId);
	        $unitGroupDesc = $unitGroup['description'];
	        
	        //Set form action
		    $formAction = $this->uri(array('action'=>'saveunitgroup', 'minor_groupid'=>$minorGroupId,
										   'submajor_groupid'=>$subMajorGroupId, 'major_groupid'=>$majorGroupId,
										   'unit_groupid'=>$unitGroupId, 'selected'=>'init_10'),'award');
    	} else {
    		//Set page header
       		$objH->str = $this->objLanguage->languageText('mod_lrssoc_addunitgroup','award');
       		$unitGroupDesc = '';
       		//Set form action
	        $formAction = $this->uri(array('action'=>'saveunitgroup', 'minor_groupid'=>$minorGroupId,
										   'submajor_groupid'=>$subMajorGroupId, 'major_groupid'=>$majorGroupId,
										   'selected'=>'init_10'),'award');
    	}
    	$objEditGroupForm = new form('saveunitgroup', $formAction);
		$txtDescription = new textarea('description', $unitGroupDesc);
		
		// Create a submit button
		$objSubmit = new button('submit'); 
		// Set the button type to submit
		$objSubmit->setToSubmit(); 
		// Use the language object to add the word
		$objSubmit->setValue(' ' . $this->objLanguage->languageText('word_submit') . ' ');
		
		$btnCancel = new button('cancel');
		$location = $this->uri(array('action'=>'selectunitgroup', 'minor_groupid'=>$minorGroupId,'selected'=>'init_10'), 'award');
		$btnCancel->setOnClick("javascript:window.location='$location'");
		$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');
		
		$objaddeditHeadTable = new htmlTable('lrsoc');
		$objaddeditHeadTable->cellspacing = 2;
		$objaddeditHeadTable->cellpadding = '2';
		$objaddeditHeadTable->width = '90%';
		
		$objaddeditHeadTable->startRow();
		$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrssoc_tbl_head_unitgroup','award')."</i>");
		$objaddeditHeadTable->addCell("");
		$objaddeditHeadTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell($this->objLanguage->languageText('word_description'). ':','','top','','odd');
		$objFormTable->addCell($txtDescription->show());
		$objFormTable->endRow();

		$objFormTable->startRow();
		$objFormTable->addCell($objSubmit->show().'  '.$btnCancel->show());
		$objFormTable->endRow();
	
		//Add validation here
		$objEditGroupForm->addRule('description', $this->objLanguage->languageText('mod_lrssoc_valrequired','award'), 'required');
	
		//Add table to form
		$objEditGroupForm->addToForm($objaddeditHeadTable->show());
		$objEditGroupForm->addToForm($objFormTable->show());
		break;
		
    case 'socname':
    	if(isset($socNameId))
    	{
			$objH->str = $this->objLanguage->languageText('mod_lrssoc_editsocname','award');
        	//Get major group data
        	$socName = $this->objDbSocNames->getRow('id', $socNameId);
        	
	        //Get sub major group data
	        $socNameName = $socName['name'];
		    $socNameId = $socName['id'];
	        
	        //Set form action
			$uri = array('action'=>'savesocname', 'id'=>$socNameId,'selected'=>'init_10');
			if ($searchterm != null) {
				$uri['searchterm'] = $searchterm;
			}
		    $formAction = $this->uri($uri);
			//Create new form object
			$objEditGroupForm = new form('savesocname', $formAction);
			$objEditGroupForm->displayType = '3';
    	}
    	else 
    	{
    		//Set page header
       		$objH->str = $this->objLanguage->languageText('mod_lrssoc_addsocname','award');

	        $socNameName = '';
		    $socNameId = '';
       		//Set form action
	        $uri = array('action'=>'savesocname', 'selected'=>'init_10');
			if ($searchterm != null) {
				$uri['searchterm'] = $searchterm;
			}
		    $formAction = $this->uri($uri);
			//Create new form object
			$objEditGroupForm = new form('savesocname', $formAction);
			$objEditGroupForm->displayType = '3';

    	}
    	
    	$txsocNameId = new textinput("socNameId", $socNameId, 'hidden');
    	$txtMajorId = new textinput("majorGroupId", $majorGroupId, 'hidden');
    	$txtSubId = new textinput("subMajorGroupId", $subMajorGroupId, 'hidden');
    	$txtMinorId = new textinput("minorGroupId", $minorGroupId, 'hidden');
    	$txtUnitId = new textinput("unitGroupId", $unitGroupId, 'hidden');
		$txtDescription = new textarea('description', $socNameName);
		$txtResults = new textinput('results', $results, 'hidden');
		$txtgroup = new textinput('group', $group, 'hidden');
		$txtsearchterm = new textinput('searchterm', $searchterm, 'hidden');
		$txtgroupId = new textinput('groupId', $groupId, 'hidden');
		
		// Create a submit button
		$objSubmit = new button('submit'); 
		$objSubmit->setToSubmit(); 
		$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_submit") . ' ');
		
		$btnCancel = new button('cancel');
		if ($searchterm != null) {
			$location = $this->uri(array('action'=>'search', 'searchterm'=>$searchterm, 'selected'=>'init_10'));
		} else {
			$location = $this->uri(array('action'=>'selectsocname', 'unit_groupid'=>$unitGroupId,'selected'=>'init_10'));
		}
		$btnCancel->setOnClick("javascript:window.location='$location'");
		$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');
		
		$objaddeditHeadTable = new htmlTable('lrsoc');
		$objaddeditHeadTable->cellspacing = 2;
		$objaddeditHeadTable->cellpadding = '2';
		$objaddeditHeadTable->width = '90%';
		
		$objaddeditHeadTable->startRow();
		$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrssoc_tbl_head_socname','award')."</i>");
		$objaddeditHeadTable->addCell("");
		$objaddeditHeadTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell($this->objLanguage->languageText('word_description'). ':','','top','','odd');
		$objFormTable->addCell($txtDescription->show());
		$objFormTable->endRow();
		
		$objFormTable->startRow();
		$objFormTable->addCell($txtMinorId->show().$txsocNameId->show().$txtResults->show().$txtgroupId->show());
		$objFormTable->addCell($txtMajorId->show().$txtUnitId->show().$txtgroup->show().$txtsearchterm->show());
		$objFormTable->endRow();

		$objFormTable->startRow();
		$objFormTable->addCell($objSubmit->show().'  '.$btnCancel->show());
		$objFormTable->addCell($txtSubId->show());
		$objFormTable->endRow();
	
		//Add validation here
		$objEditGroupForm->addRule('description', $this->objLanguage->languageText('mod_lrssoc_valrequired','award'), 'required');
	
		//Add table to form
		$objEditGroupForm->addToForm($objaddeditHeadTable->show());
		$objEditGroupForm->addToForm($objFormTable->show());
		break;       
}        

//Add content to the output layer
$middleColumnContent = $objH->show().$objEditGroupForm->show();

echo $middleColumnContent;

?>