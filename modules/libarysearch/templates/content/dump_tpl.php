<?php

		// These proloaders enbabe interaction with the graphical tools.
		$this->objDBUser= $this->getObject('user','security');
		$this->loadClass('link', 'htmlelements');
	    	$this->loadClass('radio', 'htmlelements');
		$this->loadClass('dropdown', 'htmlelements');
		$this->loadClass('checkbox', 'htmlelements');
		$this->loadClass('button', 'htmlelements');
		$this->loadClass('textinput','htmlelements');
		$this->loadClass('textarea','htmlelements');
		$this->loadClass('form','htmlelements');
		


		//Text Input
		$objForm = new form('testform');
		$url=$this->uri(array('action'=>'add'),'htmlelements');
		$objForm->setAction($this->uri(array('action'=>'save'),'libarysearch'));
		$objForm->setDisplayType(2);

		$objElement = new textinput('textbox', null ,null , 50); 
		$objElement->setValue('search Library databases');
		$objElement->label=' Search';
		$text = $objElement->show().'<br />';
		
		$objForm->addToForm($text);
		
		
		// Search form

	$searchForm = &new form('searchform',$this->uri(array('action'=>'search','cat'=>'all'),'libarysearch'));
	$searchForm->displayType = 3;
	$srchStr = &new textinput('srchstr',$this->getParam('srchstr'),null,'90');
	$srchButton = &new button('search');
	$srchButton->setValue($this->objLanguage->languageText('word_search'));
	$srchButton->setToSubmit();
	$srchType = &new dropdown('srchtype');
	$srchType->addOption('UWC Catalogue ',$this->objLanguage->languageText('mod_libarysearch_modname','libarysearch'));
	$srchType->addOption('UCT Catalogue ',$this->objLanguage->languageText('mod_libarysearch_modname1','libarysearch'));
	$srchType->addOption('STELLIES Catalogue ',$this->objLanguage->languageText('mod_libarysearch_modname2','libarysearch'));
	$srchType->addOption('CPUT Catalogue',$this->objLanguage->languageText('mod_libarysearch_modname3','libarysearch'));
	$srchType->setSelected($this->getParam('srchtype'));
	$srch = $srchType->show().$srchButton->show();

	$objForm->addToForm("<center>".$srch."</center>");

		
		

//===========================++ Display Section ++==============================




		//Button
		$objElement = new button('mybutton');
		$objElement->setValue('Normal Button');
		$objElement->setOnClick('alert(\'An onclick Event\')');
		$button=$objElement->show().'<br />';

		//Submit Button
		$objElement = new button('mybutton');
		$objElement->setToSubmit();
		$objElement->label='Search Catalogue';
		$objElement->setValue('Submit Button');
		$submit=$objElement->show().'<br />';

		
		//add submit button to the form;
		//$objForm->addToForm($objElement);
		

		

	echo $objForm->show();
	
	$forumLink = new link($this->uri(array( 'module'=> 'forum', 'action' => 'forum', 'id' => $forum['forum_id'])));
    $forumLink->link = $forum['forum_name'];
    $navlink = $forumLink->show();
//==============================================================================


 

?>
