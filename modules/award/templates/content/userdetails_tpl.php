<?php
    
    $this->loadclass('textinput','htmlelements');
    $this->loadclass('dropdown','htmlelements');
    $this->loadclass('form','htmlelements');
    $this->loadclass('button','htmlelements');
    
    $objDbUserReg = $this->getObject('dbuserreg');
    $objNewForm= new form('form1', $this->uri(array('action'=>'saveuser')));         
    
	if(isset($lrsUserId)) {
	    $userRow = $this->objUser->getRow('userid', $lrsUserId);
	    $lrsUserRow = $objDbUserReg->getRow('userid', $lrsUserId);
	    
	    $lrsid = $lrsUserRow['id'];
	    $id = $userRow['id'];
	    $setUserName = $userRow['username'];
	    $setTitle = $userRow['title'];
	    $setFirstName = $userRow['firstname'];
	    $setSurname = $userRow['surname'];
	    $setPosition = $lrsUserRow['position'];
	    $setTradeUnion = $lrsUserRow['tuid'];
	    $setEmail = $userRow['emailaddress'];
	    $setSex = $userRow['sex'];
	    $setCountry = $userRow['country'];
	  
    } else {	    
	    $setUserName = '';
	    $setTitle = '';
	    $setFirstName = '';
	    $setSurname = '';
	    $setPosition = '';
	    $setTradeUnion = '';
	    $setEmail = '';
	    $setSex = '';
	    $setCountry = '';
	    $lrsid = '';
	    $id = '';
		$lrsUserId = '';
    }    
    
    $objTblclass = $this->newObject('htmltable','htmlelements');
    $objTblclass->width='70%';
    $objTblclass->attributes=" border=0";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';
    
    $objDrop2= new dropdown('title');
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    foreach ($titles as $row)
    {
        $row=$objLanguage->languageText($row);
        $objDrop2->addOption($row,$row);
    }
    $objDrop2->setSelected($setTitle);
 
	if(isset($lrsUserId))
	{
	    $phrase_new_user=$objLanguage->languageText("word_edit")." ".$objLanguage->languageText('word_user');
	}else {
	    $phrase_new_user=$objLanguage->languageText("word_new")." ".$objLanguage->languageText('word_user');
	}
	//create heading
	$header = $this->getObject('htmlheading','htmlelements');
	$header->type = 1;
	$header->str = $phrase_new_user;
	$objNewForm->addToForm($header->show());
	
	$message = '';
	$error = $this->getParam('error');
	if(isset($error))
	{
		$message = "<span class = 'error'>".$this->getParam('message')."</span>";

	    $objTblerror = $this->newObject('htmltable','htmlelements');
    	$objTblerror->width='70%';
    	
		$objTblerror->startRow();
		$objTblerror->addCell($message);
		$objTblerror->endRow();
		$objNewForm->addToForm($objTblerror->show());
	}
    
    //$objTblclass->addCell("<h1>".$phrase_new_user."</h1>".$this->textinput('userId','hidden',rand(1000000000,9999999999)), "", NULL, null, NULL, 'colspan=2');

    $objTblclass->startRow();
    $objTblclass->addCell("<i>".$this->objLanguage->languageText('mod_lrs_enter_user_details', 'award')."</i>");
    $objTblclass->endRow();

    $txtUserName = new textinput('username', $setUserName);
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_username'), null, null, null, 'odd');
    $objTblclass->addCell($txtUserName->show());
    $objTblclass->endRow();

    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_title'), null, null, null, 'odd');
    $objTblclass->addCell($objDrop2->show());
    $objTblclass->endRow();
    
    $txtFirstName = new textinput('firstname', $setFirstName);
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('phrase_firstname'), null, null, null, 'odd');
    $objTblclass->addCell($txtFirstName->show());
    $objTblclass->endRow();
    
    $txtSurname = new textinput('surname', $setSurname);
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_surname'), null, null, null, 'odd');
    $objTblclass->addCell($txtSurname->show());
    $objTblclass->endRow();
    
    $txtPosition = new textinput('position', $setPosition);
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_position'), null, null, null, 'odd');
    $objTblclass->addCell($txtPosition->show());
    $objTblclass->endRow();
    
    $tuDrop = new dropdown('tuId');
	$tus = $this->objDbParty->getAll("ORDER BY name");
	$tuDrop->addOption('',$this->objLanguage->languageText('word_none'));
	$tuDrop->addFromDb($tus,'abbreviation','id');
	$tuDrop->setSelected($setTradeUnion);
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('phrase_tradeunion'), null, null, null, 'odd');
    $objTblclass->addCell($tuDrop->show());
    $objTblclass->endRow();
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_password'), null, null, null, 'odd');
    $password = new textinput('password', null, 'password');
	$objTblclass->addCell($password->show());
    $objTblclass->endRow();
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('phrase_repassword'), null, null, null, 'odd');
    $passwd = new textinput('passwd', null, 'password');
	$objTblclass->addCell($passwd->show());
    $objTblclass->endRow();
    
    $txtEmail = new textinput('email', $setEmail);
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_email'), null, null, null, 'odd');
    $objTblclass->addCell($txtEmail->show());
    $objTblclass->endRow();

    // sex
    $objDrop3= new dropdown('sex');
    $titles=array("M", "F");
    foreach ($titles as $row)
    {
        $objDrop3->addOption($row,$row);
    }
    $objDrop3->setSelected($setSex);
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_sex'), null, null, null, 'odd');
    $objTblclass->addCell($objDrop3->show());
    $objTblclass->endRow();
    
    // country
    $countries=$this->getObject('countries','utilities');
    
    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_country'), null, null, null, 'odd');
    $objTblclass->addCell($countries->getDropdown('country',$setCountry));
    $objTblclass->endRow();

    $txtlrsUserId = new textinput('userId', $lrsUserId, 'hidden');
    $txtlrsId = new textinput('lrsId', $lrsid, 'hidden');
    $txtid = new textinput('id', $id, 'hidden');
    
    $row=array($txtlrsUserId->show(),$txtlrsId->show().$txtid->show());
    $objTblclass->addRow($row,'even');

    $btnSubmit = new button('submit');
	$btnSubmit->setToSubmit();
	$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');
	
	$btnCancel = new button('cancel');
	$location = $this->uri(array('action'=>'viewuserlist', 'selected'=>'init_10'), 'award');
	$btnCancel->setOnClick("javascript:window.location='$location'");
	$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');
 
    $row=array($btnSubmit->show().'  '.$btnCancel->show(),'&nbsp');
    $objTblclass->addRow($row);
    
    //Add validation here
	$objNewForm->addRule('username', $this->objLanguage->languageText('mod_lrs_username_valrequired', 'award'), 'required');
	$objNewForm->addRule('title', $this->objLanguage->languageText('mod_lrs_title_valrequired', 'award'), 'required');
	$objNewForm->addRule('firstname', $this->objLanguage->languageText('mod_lrs_firstname_valrequired', 'award'), 'required');
	$objNewForm->addRule('surname', $this->objLanguage->languageText('mod_lrs_surname_valrequired', 'award'), 'required');
	$objNewForm->addRule('position', $this->objLanguage->languageText('mod_lrs_position_valrequired', 'award'), 'required');
	$objNewForm->addRule('tuId', $this->objLanguage->languageText('mod_lrs_tuId_valrequired', 'award'), 'required');
	if ($lrsUserId == NULL) {
		$objNewForm->addRule('password', $this->objLanguage->languageText('mod_lrs_password_valrequired', 'award'), 'required');
		$objNewForm->addRule('passwd', $this->objLanguage->languageText('mod_lrs_passwd_valrequired', 'award'), 'required');
	}
	$objNewForm->addRule('email', $this->objLanguage->languageText('mod_lrs_email_valrequired', 'award'), 'required');
	$objNewForm->addRule('sex', $this->objLanguage->languageText('mod_lrs_sex_valrequired', 'award'), 'required');
	$objNewForm->addRule('country', $this->objLanguage->languageText('mod_lrs_country_valrequired', 'award'), 'required');
    
    //print $objTblclass->show();
    $objNewForm->addToForm($objTblclass->show());
    $objNewForm->displayType=3;

    echo $objNewForm->show();
?>