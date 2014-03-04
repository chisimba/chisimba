<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

class fbform extends object
{
    
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject("language", "language");
			$this->loadClass('form', 'htmlelements');
			$this->loadClass('href', 'htmlelements');
			$this->loadClass('label', 'htmlelements');
			$this->loadClass('textinput', 'htmlelements');
			$this->loadClass('textarea', 'htmlelements');
			$this->objUser = $this->getObject('user', 'security');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}

	public function dfbform($insarr)
	{   
       
		$objCaptcha = $this->getObject('captcha', 'utilities');
		$required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'system', 'Required').'</span>';
		$dfbform = new form('save', $this->uri(array(
		'action' => 'save'
		)));

		//start a fieldset
		$fbfieldset = $this->getObject('fieldset', 'htmlelements');
		
		$fbtable = $this->newObject('htmltable', 'htmlelements');
		$fbtable->cellpadding = 3;
        
        
       
        
		//name textfield
		$fbtable->startRow();
		$fbnamelabel = new label($this->objLanguage->languageText('mod_feedback_thename', 'feedback') .':', 'input_fbname');
		$fbname = new textinput('fbname');
		if(isset($insarr['fbname']))
		{
			$fbname->setValue($insarr['fbname']);
		}
		$fbtable->addCell($fbnamelabel->show().$required);
		$fbtable->addCell($fbname->show());
		$fbtable->endRow();
        
		//email textfield
		$fbtable->startRow();
		$fbemaillabel = new label($this->objLanguage->languageText('mod_feedback_email', 'feedback') .':', 'input_fbemail');
		$fbemail = new textinput('fbemail');
		if(isset($insarr['fbemail']))
		{
			$fbemail->setValue($insarr['fbemail']);
		}
		$fbtable->addCell($fbemaillabel->show().$required);
		$fbtable->addCell($fbemail->show());
		$fbtable->endRow();

       
		// addition started
            $objDb = $this->getObject('dbfb_questions');
            $questions = $objDb->get_questions();
            //$questions =  $objDb->get_questions();
            $responses_array_size = count($questions);
            for($i = 0; $i < $responses_array_size; $i++){
                //echo "question ".$questions[$i]['fb_question']."<br/>";
                $fbtable->startRow();
		        $fbwlabel = new label($questions[$i]['fb_question'], '');
                $objInput = new textinput('inputQuestion_id_'.($i + 1), $questions[$i]['puid']);
                //echo 'inputQuestion_id_'.($i + 1).": ".$questions[$i]['puid'].": ".$questions[$i]['fb_question']."<br/>";
                $objInput->fldType = 'hidden';
                //$hidden.= $objInput->show();
		        $fbw = new textarea('inputQuestion_'.($i + 1));
		        if(isset($insarr['']))
		        {
			        $fbw->setValue($insarr['fbw']);
		        }
               //$fbtable->addCell($objInput->show());
                $dfbform->addToForm($objInput->show());
		        $fbtable->addCell($fbwlabel->show());
		        $fbtable->addCell($fbw->show());
		        $fbtable->endRow();
            }
           $objInput = new textinput('responses_array_size', $responses_array_size);
           $objInput->fldType = 'hidden';
           $dfbform->addToForm($objInput->show());
        //addition ended
		$fbtable->startRow();
		$captcha = new textinput('request_captcha');
		$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'input_request_captcha');
		$fbtable->addCell(stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')).'<br /><div id="captchaDiv">'.$objCaptcha->show().'</div>'.$captcha->show().$required.'  <a href="javascript:redraw();">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>');
		$fbtable->endRow();
		
		//add rules
		$dfbform->addRule('fbname', $this->objLanguage->languageText("mod_feedback_phrase_needname", "feedback") , 'required');
		$dfbform->addRule('fbemail', $this->objLanguage->languageText("mod_feedback_phrase_needemail", "feedback") , 'required');
		$dfbform->addRule('request_captcha', $this->objLanguage->languageText("mod_feedback_captchaval",'feedback'), 'required');

		//end off the form and add the buttons
		$this->objSaveButton = &new button($this->objLanguage->languageText('word_save', 'system'));
		$this->objSaveButton->setValue($this->objLanguage->languageText('word_save', 'system'));
		$this->objSaveButton->setToSubmit();
		$fbfieldset->addContent($fbtable->show());
		$dfbform->addToForm($fbfieldset->show());
		$dfbform->addToForm($this->objSaveButton->show());
		$dfbform = $dfbform->show();
       
        
		//featurebox it...
		$objFbFeaturebox = $this->getObject('featurebox', 'navigation');
		return $objFbFeaturebox->show($this->objLanguage->languageText("mod_feedback_feedback", "feedback"), $dfbform);
	}
	
	public function thanks()
	{
		$backlink = new href($this->uri(array(),'_default'), $this->objLanguage->languageText("mod_feedback_back", "feedback"), NULL);
		$tamsg = $this->objLanguage->languageText("mod_feedback_thanksmsg", "feedback") . "<br /><br />" . $backlink->show();
		$objFbFeaturebox = $this->getObject('featurebox', 'navigation');
		return $objFbFeaturebox->show($this->objLanguage->languageText("mod_feedback_thanks", "feedback"), $tamsg);
	}
	
	 /**
     * Method to display the login box for prelogin  operations
     *
     * @param bool $featurebox
     * @return string
     */
    public function loginBox($featurebox = FALSE)
    {
        $objLogin = $this->getObject('logininterface', 'security');
        if ($featurebox == FALSE) {
            return $objLogin->renderLoginBox('feedback');
        } else {
            $objFeatureBox = $this->getObject('featurebox', 'navigation');
            return $objFeatureBox->show($this->objLanguage->languageText("word_login", "system") , $objLogin->renderLoginBox('feedback'));
        }
    }
}
?>
