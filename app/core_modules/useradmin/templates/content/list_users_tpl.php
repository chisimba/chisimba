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
	
?>