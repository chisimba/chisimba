<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class hrwizardops extends object {
	
	public function init()
	{
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objMailer = $this->getObject('mailer', 'mail');
		
	}
	
	public function parseCSV($csvfile)
	{
		$path = $this->objConfig->getcontentBasePath()."hrtmp/";
    	if(!file_exists($path))
    	{
    		mkdir($path, 0777);
    		chmod($path, 0777);
    	}
    	//ok, now unpack the files to the dir
    	$objFile = $this->getObject('dbfile', 'filemanager');
		$csv = $objFile->getFullFilePath($csvfile);
		$csvname = $objFile->getFileName($csvfile);
		@require_once "File/Archive.php"; 
		@File_Archive::extract($csv,$path);
		chdir($path);
		
		$row = 1;
		$handle = fopen($csv, "r");
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    		$num = count($data);
    		$row++;
    		$arr[] = $data;
		}
		fclose($handle);
		
		
		
		return $arr;
		
	}
	
	public function uploadDataFile($featurebox = TRUE)
    {
    	$this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
    	$this->objUser = $this->getObject('user', 'security');
    	
    	$fileform = new form('uploaddatafile', $this->uri(array(
            	'action' => 'uploaddatafile'
        	)));
        	
        //start a fieldset
        $filefieldset = $this->getObject('fieldset', 'htmlelements');
        $fileadd = $this->newObject('htmltable', 'htmlelements');
        $fileadd->cellpadding = 3;

        //file textfield
        $fileadd->startRow();
        $filelabel = new label($this->objLanguage->languageText('mod_hrwizard_file', 'hrwizard') .':', 'input_file');
        
        $objSelectFile->name = 'zipfile';
        $objSelectFile->restrictFileList = array('zip');
                
        $fileadd->addCell($filelabel->show());
        $fileadd->addCell($objSelectFile->show());
        $fileadd->endRow();
        
        //end off the form and add the buttons
        $this->objIMButton = &new button($this->objLanguage->languageText('word_next', 'hrwizard'));
        $this->objIMButton->setValue($this->objLanguage->languageText('word_next', 'hrwizard'));
        $this->objIMButton->setToSubmit();
        $filefieldset->addContent($fileadd->show());
        $fileform->addToForm($filefieldset->show());
        $fileform->addToForm($this->objIMButton->show());
        $fileform = $fileform->show();
        
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_hrwizard_uploadpdfzipfile", "hrwizard") , $fileform);
            return $ret;
        } else {
            return $fileform;
        }

    }
    
    public function unpackPdfs($fileid)
    {
    	//check if the temp directory exists or not..
    	$path = $this->objConfig->getcontentBasePath()."hrtmp/";
    	if(!file_exists($path))
    	{
    		mkdir($path, 0777);
    		chmod($path, 0777);
    	}
    	//ok, now unpack the files to the dir
    	$objFile = $this->getObject('dbfile', 'filemanager');
		$pdfzip = $objFile->getFullFilePath($fileid);
		$zipname = $objFile->getFileName($fileid);
		//echo $pdfzip;
		
		require_once "File/Archive.php"; 
		@File_Archive::extract($pdfzip,$path);
		chdir($path);
		exec('unzip -o '.$pdfzip);
		//cleanup
		unlink($path.$zipname);
		return;
    }
	
	public function uploadCSVFile($featurebox = TRUE)
    {
    	$this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
    	$this->objUser = $this->getObject('user', 'security');
    	
    	$fileform = new form('uploadcsvfile', $this->uri(array(
            	'action' => 'uploadcsvfile'
        	)));
        	
        //start a fieldset
        $filefieldset = $this->getObject('fieldset', 'htmlelements');
        $fileadd = $this->newObject('htmltable', 'htmlelements');
        $fileadd->cellpadding = 3;

        //file textfield
        $fileadd->startRow();
        $filelabel = new label($this->objLanguage->languageText('mod_hrwizard_file', 'hrwizard') .':', 'input_file');
        
        $objSelectFile->name = 'csvfile';
        $objSelectFile->restrictFileList = array('csv');
                
        $fileadd->addCell($filelabel->show());
        $fileadd->addCell($objSelectFile->show());
        $fileadd->endRow();
        
        //end off the form and add the buttons
        $this->objIMButton = &new button($this->objLanguage->languageText('word_next', 'hrwizard'));
        $this->objIMButton->setValue($this->objLanguage->languageText('word_next', 'hrwizard'));
        $this->objIMButton->setToSubmit();
        $filefieldset->addContent($fileadd->show());
        $fileform->addToForm($filefieldset->show());
        $fileform->addToForm($this->objIMButton->show());
        $fileform = $fileform->show();
        
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_hrwizard_uploadcsvfile", "hrwizard") , $fileform);
            return $ret;
        } else {
            return $fileform;
        }

    }
    
    public function sendMails($recarr, $bodyText, $subject)
    {
    	    	//	print_r($recarr); die();
    	foreach($recarr as $record)
    	{
    		//get the pdf associated with the employee number

    		$path = $this->objConfig->getcontentBasePath()."hrtmp/";
    		$file = $record[0] . ".pdf";
    		//print_r($record);
    		if(file_exists($file))
    		{
    			str_replace('&nbsp;', '\r\n', $bodyText);
    			$objMailer = $this->getObject('mailer', 'mail');
				$objMailer->setValue('to', array($record[1]));
				$objMailer->setValue('from', 'hr@uwc.ac.za');
				$objMailer->setValue('fromName', $this->objLanguage->languageText("mod_hrwizard_emailfromname", "hrwizard"));
				$objMailer->setValue('subject', $subject);
				$objMailer->setValue('body', strip_tags($bodyText));
				$objMailer->attach($file, $record[0]);
				//$objMailer->htmlMail();
				$objMailer->send();
				$objMailer->clearAttachments();
				$objMailer->clearAddresses();
				$retarr[] = array($record[1], $file);
				unlink($file);
    		}
    	}
    	
    	return $retarr;
    }
    
    public function bodyTextEditor($editor = TRUE, $featurebox = FALSE)
    {
    	try {
    		$objSelectFile = $this->newObject('selectfile', 'filemanager');
    		$objSelectFile2 = $this->newObject('selectfile', 'filemanager');
			$this->loadClass('form', 'htmlelements');
			$this->loadClass('textinput', 'htmlelements');
			$this->loadClass('textarea', 'htmlelements');
			$this->loadClass('button', 'htmlelements');
			//$this->loadClass('htmlarea', 'htmlelements');
			$this->loadClass('dropdown', 'htmlelements');
			$this->loadClass('label', 'htmlelements');
			$objCaptcha = $this->getObject('captcha', 'utilities');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
		$required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'system', 'Required').'</span>';
		$cform = new form('bodytext', $this->uri(array('action' => 'addmessage')));
		
		$cfieldset = $this->getObject('fieldset', 'htmlelements');
		$ctbl = $this->newObject('htmltable', 'htmlelements');
		$ctbl->cellpadding = 5;

		//start the inputs
		//pdfzip file textfield
        $ctbl->startRow();
        $zipfilelabel = new label($this->objLanguage->languageText('mod_hrwizard_zipfile', 'hrwizard') .':', 'input_zipfile');
        
        $objSelectFile->name = 'zipfile';
        $objSelectFile->restrictFileList = array('zip');
                
        $ctbl->addCell($zipfilelabel->show() . $required);
        $ctbl->addCell($objSelectFile->show());
        $ctbl->endRow();
        
        //csv file textfield
        $ctbl->startRow();
        $csvfilelabel = new label($this->objLanguage->languageText('mod_hrwizard_csvfile', 'hrwizard') .':', 'input_csvfile');
        $objSelectFile2->name = 'csvfile';
        $objSelectFile2->restrictFileList = array('csv');
                
        $ctbl->addCell($csvfilelabel->show() . $required);
        $ctbl->addCell($objSelectFile2->show());
        $ctbl->endRow();
        
        //message subject
        $commlabel = new label($this->objLanguage->languageText('mod_hrwizard_messagesubject', 'hrwizard') .':', 'input_msgsubinput');
		$ctbl->startRow();
		$ctbl->addCell($commlabel->show());
		$subj = new textinput('subject');
		$ctbl->addCell($subj->show());
		$ctbl->endRow();
		
        
		//textarea for the message
		$commlabel = new label($this->objLanguage->languageText('mod_hrwizard_message', 'hrwizard') .':', 'input_msginput');
		$ctbl->startRow();
		$ctbl->addCell($commlabel->show());
		if($editor == TRUE)
		{
			//echo "start";
			$comm = $this->getObject('htmlarea','htmlelements');
			$comm->setName('bodytext');
			$comm->height = 400;
			$comm->width = 420;
			$comm->setBasicToolBar();
			$ctbl->addCell($comm->showFCKEditor());
		}
		else {
			$comm = new textarea;
			$comm->setName('bodytext');
			$ctbl->addCell($comm->show());
		}
		$ctbl->endRow();
		
		//add some rules
		//$cform->addRule('csvfile', $this->objLanguage->languageText("mod_hrwizard_csvfilereq",'hrwizard'), 'required');
		//$cform->addRule('zipfile', $this->objLanguage->languageText("mod_hrwizard_zipfilereq",'hrwizard'), 'required');
 		//end off the form and add the buttons
		$this->objCButton = &new button($this->objLanguage->languageText('word_save', 'system'));
		$this->objCButton->setValue($this->objLanguage->languageText('word_save', 'system'));
		$this->objCButton->setToSubmit();

		$cfieldset->addContent($ctbl->show());
		$cform->addToForm($cfieldset->show());
		$cform->addToForm($this->objCButton->show());

		if($featurebox == TRUE)
		{
			$objFeaturebox = $this->getObject('featurebox', 'navigation');
			return $objFeaturebox->showContent($this->objLanguage->languageText("mod_hrwizard_msghead", "hrwizard"), $cform->show());
		}
		else {
			return $cform->show();
		}
	
    }
    
    public function uploadFiles()
    {
    	$this->loadClass('href', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
    	$this->objUser = $this->getObject('user', 'security');
    	
    	$fileform = new form('uploaddatafile', $this->uri(array(
            	'action' => 'uploaddatafile'
        	)));
        	
        //start a fieldset
        $filefieldset = $this->getObject('fieldset', 'htmlelements');
        $fileadd = $this->newObject('htmltable', 'htmlelements');
        $fileadd->cellpadding = 3;

        //pdfzip file textfield
        $fileadd->startRow();
        $filelabel = new label($this->objLanguage->languageText('mod_hrwizard_file', 'hrwizard') .':', 'input_zipfile');
        
        $objSelectFile->name = 'zipfile';
        $objSelectFile->restrictFileList = array('zip');
                
        $fileadd->addCell($filelabel->show());
        $fileadd->addCell($objSelectFile->show());
        $fileadd->endRow();
        
        //csv file textfield
        $fileadd->startRow();
        $filelabel = new label($this->objLanguage->languageText('mod_hrwizard_file', 'hrwizard') .':', 'input_csvfile');
        
        $objSelectFile->name = 'csvfile';
        $objSelectFile->restrictFileList = array('csv');
                
        $fileadd->addCell($filelabel->show());
        $fileadd->addCell($objSelectFile->show());
        $fileadd->endRow();
        
        //end off the form and add the buttons
        $this->objIMButton = &new button($this->objLanguage->languageText('word_next', 'hrwizard'));
        $this->objIMButton->setValue($this->objLanguage->languageText('word_next', 'hrwizard'));
        $this->objIMButton->setToSubmit();
        $filefieldset->addContent($fileadd->show());
        $fileform->addToForm($filefieldset->show());
        $fileform->addToForm($this->objIMButton->show());
        $fileform = $fileform->show();
        
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_hrwizard_uploadpdfzipfile", "hrwizard") , $fileform);
            return $ret;
        } else {
            return $fileform;
        }

    }

}
?>