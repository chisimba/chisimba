<?php

class geoops extends object 
{
	
	public function init()
	{
		$this->objLanguage = $this->getObject('language', 'language');
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objMailer = $this->getObject('mailer', 'mail');
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

        //file textfield$this->loadClass('label', 'htmlelements')
        $fileadd->startRow();
        $filelabel = new label($this->objLanguage->languageText('mod_geonames_file', 'geonames') .':', 'input_file');
        
        $objSelectFile->name = 'zipfile';
        $objSelectFile->restrictFileList = array('zip');
                
        $fileadd->addCell($filelabel->show());
        $fileadd->addCell($objSelectFile->show());
        $fileadd->endRow();
        
        //end off the form and add the buttons
        $this->objIMButton = &new button($this->objLanguage->languageText('word_upload', 'system'));
        $this->objIMButton->setValue($this->objLanguage->languageText('word_upload', 'system'));
        $this->objIMButton->setToSubmit();
        $filefieldset->addContent($fileadd->show());
        $fileform->addToForm($filefieldset->show());
        $fileform->addToForm($this->objIMButton->show());
        $fileform = $fileform->show();
        
        if ($featurebox == TRUE) {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_geonames_uploadzipfile", "geonames") , $fileform);
            return $ret;
        } else {
            return $fileform;
        }

    }
    
    public function unpackPdfs($fileid)
    {
    	//ok, now unpack the files to the dir
    	$objFile = $this->getObject('dbfile', 'filemanager');
		$geozip = $objFile->getFullFilePath($fileid);
		$zipname = $objFile->getFileName($fileid);
		//echo $geozip, $zipname; die();
		
		exec('unzip -o '.$geozip);
		//cleanup
		//unlink($geozip);
		$file = explode(".",$zipname);
		$file = $file[0].".txt";
		return $file;
    }
    
    public function parseCSV($csvfile)
	{
		$this->objUser = $this->getObject('user', 'security');
		$userid = $this->objUser->userId();
		$this->objDbGeo = $this->getObject('dbgeonames');
		// $row = 1;
		$handle = fopen($csvfile, "r");
		while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
    		// $num = count($data);
    		// $row++;
    		//var_dump($data); die();
    		@$insarr = array('userid' => $userid, 'geonameid' => $data[0], 'name' => $data[1], 'asciiname' => $data[2], 'alternatenames' => $data[3], 
            						'latitude' => $data[4], 'longitude' => $data[5], 'featureclass' => $data[6], 'featurecode' => $data[7], 
            						'countrycode' => $data[8], 'cc2' => $data[9], 'admin1code' => $data[10], 'admin2code' => $data[11], 
            						'population' => $data[12], 'elevation' => $data[13], 'gtopo30' => $data[14], 'timezoneid' => $data[15], 
            						'moddate' => $data[16]
            						);
            		$this->objDbGeo->insertRecord($insarr);
    		//$arr[] = $data;
		}
		fclose($handle);
		return TRUE;
		
	}
    
    public function searchForm()
    {
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        
        $form = new form ('search', $this->uri(array('action'=>'search')));

        $label = new label ($this->objLanguage->languageText('phrase_searchfor', 'geonames', 'Search for').': ', 'input_location');

        $location = $this->getParam('location');

        $locationInput = new textinput ('location', $location);
        $button = new button ('dosearch', 'Go');
        $button->setToSubmit();

        $form->addToForm($label->show().$locationInput->show().' '.$button->show());

        return $form->show();
    }
}
