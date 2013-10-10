<?php

    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=ucwords($objLanguage->code2Txt("mod_workgroupadmin_createworkgroup",'workgroupadmin'));
	echo $pageTitle->show();
    
    // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("button","htmlelements");
    // Display form.
	$form = new form("createForm", 
		$this->uri(array(
	    	'module'=>'workgroupadmin',
			'action'=>'createConfirm'
		))
	);
	$form->setDisplayType(3);
	$form->addToForm(ucwords($objLanguage->code2Txt("mod_workgroupadmin_workgroupname",'workgroupadmin'))."<br/>");
	$form->addToForm(new textinput("newworkgroup",""));
    $form->addToForm("<br/>");
	$submitButton = new button("submit", $objLanguage->languageText("word_save"));
	$submitButton->setToSubmit();
    $cancelButton = new button("submit", $objLanguage->languageText("word_cancel"));
    $cancelButton->setOnClick("window.location='".$this->uri(array())."';");
	$form->addToForm($submitButton->show().' / '.$cancelButton->show());
	echo $form->show();
?>