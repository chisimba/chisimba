<?  
    function textinput($name,$type,$value=NULL)
    {
        $field=new textinput($name,$value);
        $field->fldType=$type;
        return $field->show();
    }

//	echo '<pre>';
//	print_r($userDetails);
//	echo '</pre>';
//
	if ($mode == 'add') {
	    echo '<h1>User Details for new user</h1>';
	}
	else if ($mode == 'edit') {
	    echo '<h1>User Details for: '.$userDetails['firstname'].' '.$userDetails['surname'].'</h1>';
	}

    $message=$this->getVar('message');
    if ($message){
        echo "<h2>".$objLanguage->languageText('word_problem').': '.$objLanguage->languageText($message)."</h2>";
    }
	
    // Put User Admin menus if user is admin
    if ($isAdminUser) {
        echo $this->alphabetBrowser();
    }

    $this->loadclass('textinput','htmlelements');
    $this->loadclass('dropdown','htmlelements');
    $this->loadclass('button','htmlelements');
    $this->loadclass('radio','htmlelements');

    // Breadcrumbs object
    $objTools=&$this->getObject('tools','toolbar');
    $link=$objLanguage->languageText('menu_userdetails');
    $objTools->addToBreadCrumbs(array($link));

    $objButtons=&$this->getObject('navbuttons','navigation');

    $this->loadclass('form','htmlelements');
	switch($mode){
		case 'add': 
			$uri = $this->uri(array('action'=>'addapply'),'useradmin');
			break;
		case 'edit': 
			$uri = $this->uri(array('action'=>'editapply'),'useradmin');
			break;
		default:
			die('Unknown mode!');
			;
	} // switch
    $objForm = new form('Form1', $uri);
    $objForm->displayType=3;
	// Action       
    //$objForm->addToForm(textinput('userAdminAction','hidden',$action));
    //$objForm->addToForm(textinput('action','hidden',$action));
	if ($mode == 'edit') {
		$objForm->addToForm(textinput('isAdminUser','hidden',$isAdminUser));
	    $objForm->addToForm(textinput('userId','hidden',$userDetails['userid']));
	    $objForm->addToForm(textinput('oldUsername','hidden',$userDetails['username']));
	}
    //$objForm->addToForm(textinput('isAdminUser','hidden',$isAdminUser));
    //$objForm->addToForm(textinput('old_accesslevel','hidden',$userDetails['accesslevel']));

    $objFieldset =& $this->getObject('fieldsetex', 'htmlelements');
    $objFieldset->setLegend('');
    //$objFieldset->align='CENTER';
    //$objFieldset->legendalign='CENTER';
    //$objFieldset->width="50%";

	// UserID
	if ($mode == 'add') {
        $objFieldset->addLabelledField(
			$objLanguage->languageText('word_userId','useradmin','userId'),
			textinput('userId','text')
		);
	    $idbutton="<a onclick=\"document.Form1.userId.value=Math.round(Math.random()*1000)+'".date('ydi')."';\">\n";
	    $idbutton.="<input type='button' class='button' value='".$objLanguage->languageText("hyperlink_generaterandomnumber",'useradmin')."'>\n";
	    $idbutton.="</a>\n";
        $objFieldset->addLabelledField(
	    	'',
			$idbutton
		);
	}

	if ($mode == 'add') {
        $objFieldset->addLabelledField(
        	$objLanguage->languageText('word_username'),
			textinput('username','text')
		);
	    
	}	
	else if ($mode == 'edit') {
	    // Username
	    // LDAP users may not change their username
	    if ($userDetails['howCreated']=='LDAP'){
	        $objFieldset->addLabelledField(
				$objLanguage->languageText('word_username'),
				$userDetails['username'].textinput('username','hidden',$userDetails['username'])
			);
	    } else {
	        $objFieldset->addLabelledField(
	        	$objLanguage->languageText('word_username'),
				textinput('username','text',$userDetails['username'])
			);
	    }
	 }
    // Title
    $objDropdown = new dropdown('title');
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    foreach ($titles as $title)
    {
        $_title=trim($objLanguage->languageText($title,'useradmin'));
        $objDropdown->addOption($_title,$_title);
    }
	if ($mode == 'edit') {
	    $objDropdown->setSelected($userDetails['title']);
	}
    $objFieldset->addLabelledField(
       	$objLanguage->languageText('word_title'),
		$objDropdown->show()
	);
    // Firstname
    $objFieldset->addLabelledField(
		$objLanguage->languageText('phrase_firstname'),
		textinput('firstname','text',$mode == 'add' ? '' : $userDetails['firstname'])
	);
    // Surname
    $objFieldset->addLabelledField(
    	$objLanguage->languageText('word_surname'),
		textinput('surname','text',$mode == 'add' ? '' : $userDetails['surname'])
	);

	if ($mode == 'add') {
	    // Password
	    $objFieldset->addLabelledField(
	    	$objLanguage->languageText('word_password'),
			textinput('password','password')
		);
	    
	    // Password confirm
	    $objFieldset->addLabelledField(
	    	$objLanguage->languageText('word_password'),
			textinput('passwd','password')
		);
	}
    // Email
    $objFieldset->addLabelledField(
    	$objLanguage->languageText('phrase_emailaddress'),
		textinput('email','text',$mode == 'add' ? '' : $userDetails['emailaddress'])
	);
    // Sex
    $objDropdown = new radio('sex');   
    $objDropdown->addOption('M', $objLanguage->languageText('word_male'));
    $objDropdown->addOption('F', $objLanguage->languageText('word_female'));    
	if ($mode == 'edit') {
	    $objDropdown->setSelected($userDetails['sex']);
	}
    $objFieldset->addLabelledField(
    	$objLanguage->languageText('word_sex'),
		$objDropdown->show()
	);
    // Country
    $objCountries=&$this->getObject('countries','utilities');
    $objDropdown = new dropdown('country');
    $objDropdown->addFromDB(
		$objCountries->getAll(' order by name'), 
		"printable_name", 
		"iso", 
		$mode == 'add' ? 'ZA' : $userDetails['country']
	);
    $objFieldset->addLabelledField(
    	$objLanguage->languageText('word_country'),
		$objDropdown->show()
	);
    // Save Button    
    $submitButton = new button('updatedetails');
    $submitButton->setValue($objLanguage->languageText('mod_useradmin_updatedetails'));
    $submitButton->setToSubmit();    
    $objFieldset->addLabelledField(
    	'',
		$submitButton->show()
	);
    $objForm->addToForm($objFieldset->show());
	echo $objForm->show();
	if ($mode == 'edit') {
		// User Image
	    $form = '';
	    $form.="<form name='fileupload' enctype='multipart/form-data' method='POST' action='".$this->uri(array('action'=>'imageupload'),'useradmin')."'>";
	    //$startForm.="<input type='hidden' name='upload' value='1' />";
	    //$startForm.="<input type='hidden' name='module' value='useradmin' />";
	    //$startForm.="<input type='hidden' name='action' value='imageupload' />";
	    //$startForm.="<input type='hidden' name='time' value='".time()."' />";   
	    $objImage=&$this->getObject('imageupload');
	    $userpicture=$objImage->userpicture($userDetails['userid']);
		$form .= '<img src="'.$userpicture.'" />';
	    if ($this->isAdmin || ($userDetails['userid']==$this->objUser->userId())){
	        if (!preg_match('/default\.jpg/', $userpicture)) {
	            $form .= 
				'<a href="'.
					$this->uri(
						array(
							'action'=>'imagereset',
							'userId'=>$userDetails['userid'], 
							'isAdminUser' => $isAdminUser
						)
					)
				.'" class="pseudobutton">'.$objLanguage->languageText("phrase_reset_image").'</a>';
	        }
	    }             
	    if ($this->isAdmin || ($userDetails['userid']==$this->objUser->userId())){
			$form .= textinput('isAdminUser','hidden',$isAdminUser);
			$form .= textinput('userId','hidden',$userDetails['userid']);
	        $form .= "<input type='file' name='userFile' />";
	        $form .= "<input type='submit' class='button' value='".$this->objLanguage->languageText('mod_useradmin_changepicture','useradmin')."' />"; 
	    }
	    $form .= '</form>';
	    echo $form;
		// Change password
	    if ($userDetails['userid']==$this->objUser->userId() && !$isLDAPUser){ 
	        $this->loadclass('href','htmlelements');
	        $objHref=new href(
				$this->uri(array('action'=>'changepassword','userId'=>$userDetails['userid']),'useradmin'),
	        	$objLanguage->languageText("word_change",'useradmin')." ".$objLanguage->languageText("word_password",'useradmin'),"class='pseudobutton'"
			);
		    echo "<br />";
	        echo $objHref->show();
	        if (!$this->isAdmin){
	            $objHref=new href(
					$this->uri(array('action'=>'selfdelete'),'useradmin'),
	            	$objLanguage->languageText('mod_useradmin_selfdelete0','useradmin'),"class='pseudobutton'"
				);
				echo '<br />';
	            echo $objHref->show();
	        }
	    } 
		else if ($this->isAdmin && !$isLDAPUser){ 
	        $this->loadclass('href','htmlelements');
	        $objHref=new href(
				$this->uri(
					array(
						'action'=>'adminchangepassword',
						'userId'=>$userDetails['userid'],
						'username'=>$userDetails['username']
					),'useradmin'
				),
	        	$objLanguage->languageText("mod_useradmin_changepassword2",'useradmin', "Change Password"),"class='pseudobutton'"
			);
			echo '<br />';
	        echo $objHref->show();
	    }
	}
    if ($isAdminUser) {
        echo $this->userAdminMenu();
    }
?>
