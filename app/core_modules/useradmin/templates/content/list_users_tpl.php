<?
	echo "<h1>"./*$this->objLanguage->languageText('mod_useradmin_name','useradmin').' - '.*/$title."</h1>";
	$objAlphabet=& $this->getObject('alphabet','navigation');
	$linkarray=array('action'=>'ListUsers','how'=>'surname','searchField'=>'LETTER');
	$url=$this->uri($linkarray,'useradmin');        
	echo 
		'<p>'
		.$this->objLanguage->languageText('mod_useradmin_browsebysurname','useradmin')
		.' '
		.$objAlphabet->putAlpha(
		$url, 
		TRUE, 
		$this->objLanguage->languageText('mod_useradmin_listallusers','useradmin')
		)
		.'</p>';	
    $objForm=&$this->newObject('form','htmlelements');
    $objButton=&$this->newObject('button','htmlelements');
    $objTextinput=&$this->newObject('textinput','htmlelements');
    $objForm->form('BatchUserDelete',$this->uri(array('action'=>'batchdelete')));
    $objTextinput->textinput('module','useradmin');
    $objTextinput->fldType='hidden';
    $objForm->addToForm($objTextinput->show());
    $objTextinput->textinput('action','batchdelete');
    $objTextinput->fldType='hidden';
    $objForm->addToForm($objTextinput->show());    
    $objTextinput->textinput('how',$this->getParam('how'));
    $objTextinput->fldType='hidden';
    $objForm->addToForm($objTextinput->show());    
    $objTextinput->textinput('searchField',$this->getParam('searchField'));
    $objTextinput->fldType='hidden';
    $objForm->addToForm($objTextinput->show());
    $objForm->addToForm($usersTable);    
    $objButton->button('submit',$objLanguage->languageText('mod_useradmin_deletesected','useradmin')); 
    $objButton->setToSubmit(); 
    $objForm->addToForm('<p align="center">'.$objButton->show().'</p>'); 
    print $objForm->show();    
	echo '<br />';
	$this->loadclass('form', 'htmlelements');
	$this->loadclass('textinput', 'htmlelements');
	$this->loadclass('radio', 'htmlelements');
	$this->loadclass('button', 'htmlelements');
	$this->loadclass('link', 'htmlelements');
	$objAlphabet=& $this->getObject('alphabet','navigation');      
	$searchform = new form ('searchuser', 'index.php');
	$searchform->method= 'GET';
	$searchform->addToForm('<div>'); // align="center"
	$textinput = new textinput('module', 'useradmin');
	$textinput->fldType = 'hidden';
	$searchform->addToForm($textinput->show());
	$textinput = new textinput('action', 'listusers');
	$textinput->fldType = 'hidden';
	$searchform->addToForm($textinput->show());
	$textinput = new textinput ('searchField');
	$textinput->size = '40';
	$searchform->addToForm($textinput->show().'<br />');
	$radio = new radio ('how');
	$radio->addOption('username', $this->objLanguage->languageText('word_username','useradmin'));
	$radio->addOption('surname', $this->objLanguage->languageText('word_surname','useradmin'));
	$radio->addOption('emailaddress', $this->objLanguage->languageText('phrase_emailaddress','useradmin'));
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
	$listFieldset->addContent('<p>'.$objAlphabet->putAlpha($url).'</p>');        
	//$url=$this->uri(array('action'=>'listUnused'));        
	$addNewLink = new link($this->uri(array('action'=>'add')));
	$addNewLink->link = 'Add New User';        
	$cleanupLink = new link($this->uri(array('action'=>'listunused')));
	$cleanupLink->link = $this->objLanguage->languageText('mod_useradmin_cleanup','useradmin');        
	//$table = $this->getObject('htmltable', 'htmlelements');
	//$table->startRow();
	//$table->addCell($listFieldset->show().$bottom);
	//$table->addCell($searchFieldset->show(), '30%');
	//$table->endRow();
	//$table->show();
	echo 
		$searchFieldset->show()
		.$listFieldset->show()
		.$addNewLink->show()
		.' / '
		.$cleanupLink->show();
?>