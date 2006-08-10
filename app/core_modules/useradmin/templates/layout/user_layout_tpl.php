<?php

//create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$this->userMenuBar=& $this->getObject('usermenu','toolbar');
 

//set columns to 2
$cssLayout->setNumColumns(2);

//add left column
	$this->loadclass('form', 'htmlelements');
	$this->loadclass('textinput', 'htmlelements');
	$this->loadclass('radio', 'htmlelements');
	$this->loadclass('button', 'htmlelements');
	$this->loadclass('link', 'htmlelements');
	$searchform = new form ('searchuser', 'index.php');
	$searchform->method= 'GET';
	$searchform->addToForm('<div align="left">'); // align="center"
	$textinput = new textinput('module', 'useradmin');
	$textinput->fldType = 'hidden';
	$searchform->addToForm($textinput->show());
	$textinput = new textinput('action', 'listusers');
	$textinput->fldType = 'hidden';
	$searchform->addToForm($textinput->show());
	$textinput = new textinput ('searchField');
	$textinput->size = '20';
	$searchform->addToForm($textinput->show().'<br />');
	$radio = new radio ('how');
	$radio->addOption('username', $this->objLanguage->languageText('word_username'));
	$radio->addOption('surname', $this->objLanguage->languageText('word_surname'));
	$radio->addOption('emailaddress', $this->objLanguage->languageText('phrase_emailaddress'));
	$radio->setSelected('username');
	$searchform->addToForm($radio->show().'<br />');        
	$submitbutton = new button ('search', $this->objLanguage->languageText('heading_customSearch','useradmin'));
	$submitbutton->setToSubmit();
	$searchform->addToForm($submitbutton->show());
	$searchform->addToForm('</div>');        
	$searchFieldset =& $this->getObject('fieldset', 'htmlelements');
	$searchFieldset->setLegend($this->objLanguage->languageText('mod_useradmin_searchforuser','useradmin'));
	$searchFieldset->addContent($searchform->show());
	$listFieldset =& $this->newObject('fieldset', 'htmlelements');
	$listFieldset->setLegend($this->objLanguage->languageText('mod_useradmin_browsebysurname','useradmin'));
	$linkarray=array('action'=>'ListUsers','how'=>'surname','searchField'=>'LETTER');
	$url=$this->uri($linkarray,'useradmin');        
	$addNewLink = new link($this->uri(array('action'=>'add')));
	$addNewLink->link = 'Add New User';        
	$cleanupLink = new link($this->uri(array('action'=>'listunused')));
	$cleanupLink->link = $this->objLanguage->languageText('mod_useradmin_cleanup','useradmin');        
	
	$display = $searchFieldset->show()
		.$addNewLink->show()
		.' / '
		.$cleanupLink->show();
$cssLayout->setLeftColumnContent($display);

//set middle content
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show(); 
  
?>