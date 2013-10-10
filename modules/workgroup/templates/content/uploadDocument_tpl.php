<?php
	$this->loadClass('form','htmlelements');
	$this->loadclass('textinput','htmlelements');	
	$this->loadclass('textarea','htmlelements');
	$this->loadclass('button','htmlelements');	
    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    
   
	if ($editMode)
	{
		$pageTitle->str= "Edit File";
		$id = $this->getParam('fileid');
		$fileInfo = $this->objDbFiles->getFileDetails($id);
		$action = $this->uri(array('action'=>'editfileupload', 'fileid'=> $id ));
		$title = $fileInfo['title'];
		$fileId = $fileInfo['fileid'];
		$description = $fileInfo['description'];
		$version = $fileInfo['version'];
	} else {
		$pageTitle->str=$this->objLanguage->languageText("mod_workgroup_heading_upload",'workgroup');
		$action = $this->uri(array('action'=>'uploadconfirm'));
		$title = null;
		$description = null ;
		$version = null;
	}
	
	 echo $pageTitle->show();	
	$objForm = new form('fileupload',$action);
	//$objForm->extra = " enctype='multipart/form-data'";
	
	$objSelectFile = $this->newObject('selectfile', 'filemanager');
	
	//$objSelectFile->name = 'fileupload';
	$file = $this->getParam('fileupload');
	$objSelectFile->name = 'fileupload';

	//$objForm->addToForm($objSelectFile->show());
	$objSelectFile->workgroup = TRUE;
	
	
	if ($editMode)
	{
		$objSelectFile->setDefaultFile($fileId);
	}
	
	$objForm->displayType = 4;
	//$objForm->addToFormEx($objSelectFile->show());
	$objForm->addToFormEx($objLanguage->languageText('mod_workgroup_file','workgroup'),$objSelectFile);
	$objForm->addToFormEx($objLanguage->languageText('mod_workgroup_title','workgroup'),new textinput('title',$title, null, 100));
	$objForm->addToFormEx($objLanguage->languageText('mod_workgroup_description','workgroup'),new textarea('description',$description));
	$objForm->addToFormEx($objLanguage->languageText('mod_workgroup_version','workgroup'),new textinput('version',$version, null, 100));
	$objElement = new button('back');
	$objElement->setValue($this->objLanguage->LanguageText('word_back'));
	$returnUrl = $this->uri(array('action'=>''));
	$objElement->setOnClick("window.location = '$returnUrl'");
	
	$objForm->addToFormEx("", "<input type='submit' class='button' value='".$this->objLanguage->languageText('word_submit')."' />"." ".$objElement->show());
	echo $objForm->show();
?>