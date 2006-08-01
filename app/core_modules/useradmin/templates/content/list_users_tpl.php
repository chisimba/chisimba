<?
	print "<h1>"./*$this->objLanguage->languageText('mod_useradmin_name','useradmin').' - '.*/$title."</h1>\n";

	echo $this->alphabetBrowser();

    $objForm=&$this->newObject('form','htmlelements');
    $objButton=&$this->newObject('button','htmlelements');
    $objTextinput=&$this->newObject('textinput','htmlelements');

    //Start building up the form
    $objForm->form('BatchUserDelete',$this->uri(array('action'=>'batchdelete')));
    // Hidden text fields 
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

    // The main table so far 
    $output=$this->getvar('userdata'); 
    $objForm->addToForm($output);    
    // the submit button 
    $objButton->button('submit',$objLanguage->languageText('mod_useradmin_deletesected','useradmin')); 
    $objButton->setToSubmit(); 
    $objForm->addToForm('<p align="center">'.$objButton->show().'</p>'); 
    print $objForm->show();    
	echo '<br />'.$this->userAdminMenu();
?>
