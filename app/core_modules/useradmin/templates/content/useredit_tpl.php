<?  
//	echo '<pre>';
//	print_r($userDetails);
//	echo '</pre>';
//
    echo '<h1>User Details for: '.$userDetails['firstname'].' '.$userDetails['surname'].'</h1>';  
    // Put User Admin menus if user is admin
    if ($isAdminUser) {
        echo $this->alphaBrowseList();
    }

    $this->loadclass('textinput','htmlelements');
    $this->loadclass('dropdown','htmlelements');
    $this->loadclass('button','htmlelements');

    function textinput($name,$type,$value=NULL)
    {
        $field=new textinput($name,$value);
        $field->fldType=$type;
        return $field->show();
    }

    // Breadcrumbs object
    $objTools=&$this->getObject('tools','toolbar');
    $link=$objLanguage->languageText('menu_userdetails');
    $objTools->addToBreadCrumbs(array($link));

    $objButtons=&$this->getObject('navbuttons','navigation');

    $this->loadclass('form','htmlelements');
    $objForm = new form('Form1', $this->uri(array(),'useradmin'));
    $objForm->displayType=3;
	// Action       
    $action='editapply';
    //$objForm->addToForm(textinput('userAdminAction','hidden',$action));
    $objForm->addToForm(textinput('action','hidden',$action));
    $objForm->addToForm(textinput('userId','hidden',$userDetails['userid']));
    $objForm->addToForm(textinput('isAdminUser','hidden',$isAdminUser));
    $objForm->addToForm(textinput('oldUsername','hidden',$userDetails['username']));
    //$objForm->addToForm(textinput('old_accesslevel','hidden',$userDetails['accesslevel']));

    $objFieldset =& $this->getObject('fieldsetex', 'htmlelements');
    $objFieldset->setLegend($objLanguage->languageText("heading_registeryourself",'useradmin').$helpIcon);
    $objFieldset->align='CENTER';
    $objFieldset->legendalign='CENTER';
    $objFieldset->width="50%";

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
    // Title
    $objDropdown = new dropdown('title');
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    foreach ($titles as $title)
    {
        $_title=trim($objLanguage->languageText($title,'useradmin'));
        $objDropdown->addOption($_title,$_title);
    }
    $objDropdown->setSelected($userDetails['title']);                              
    $objFieldset->addLabelledField(
       	$objLanguage->languageText('word_title'),
		$objDropdown->show()
	);
    // Firstname
    $objFieldset->addLabelledField(
		$objLanguage->languageText('phrase_firstname'),
		textinput('firstname','text',$userDetails['firstname'])
	);
    // Surname
    $objFieldset->addLabelledField(
    	$objLanguage->languageText('word_surname'),
		textinput('surname','text',$userDetails['surname'])
	);
    // Email
    $objFieldset->addLabelledField(
    	$objLanguage->languageText('phrase_emailaddress'),
		textinput('email','text',$userDetails['emailaddress'])
	);
    // Sex
    $objDropdown = new radio('sex');   
    $objDropdown->addOption('M', $objLanguage->languageText('word_male'));
    $objDropdown->addOption('F', $objLanguage->languageText('word_female'));    
    $objDropdown->setSelected($userDetails['sex']);
    $objFieldset->addLabelledField(
    	$objLanguage->languageText('word_sex'),
		$objDropdown->show()
	);
    // Country
    $objCountries=&$this->getObject('countries','utilities');
    $objDropdown = new dropdown('country');
    $objDropdown->addFromDB($objCountries->getAll(' order by name'), "printable_name", "iso", $userDetails['country']); 
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
	// User Image
    $startForm="<form name='fileupload' enctype='multipart/form-data' method='POST' action='".$this->uri(array('action'=>'imageupload'),'useradmin')."'>";
    //$startForm.="<input type='hidden' name='upload' value='1' />";
    //$startForm.="<input type='hidden' name='module' value='useradmin' />";
    //$startForm.="<input type='hidden' name='action' value='imageupload' />";
    //$startForm.="<input type='hidden' name='time' value='".time()."' />";   
    $form = '';
    $objImage=&$this->getObject('imageupload');
    $userpicture=$objImage->userpicture($userId);
	$form .= '<img src="'.$userpicture.'" />';
    if (($this->isAdmin)||($userDetails['userid']==$this->objUser->userId())){
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
    if ($userDetails['userid']==$this->objUser->userId()){
        $form .= "<input type='file' name='userFile' />";
        $form .= "<input type='submit' class='button' value='".$this->objLanguage->languageText('mod_useradmin_changepicture')."' />"; 
    }
	$form .= textinput('isAdminUser','hidden',$isAdminUser);
    $endForm = '</form>';
    echo 
		$startForm
		.$form
		.$endForm;
    if (($userDetails['userid']==$this->objUser->userId())&& (!$isLDAPUser)){ 
        $this->loadclass('href','htmlelements');
        $objHref=new href(
			$this->uri(array('action'=>'changepassword'),'useradmin'),
        	$objLanguage->languageText("word_change")." ".$objLanguage->languageText("word_password"),"class='pseudobutton'"
		);
	    echo "<br />";
        echo $objHref->show();
        if (!$this->isAdmin){
            $objHref=new href($this->uri(array('action'=>'selfdelete'),'useradmin'),
            $objLanguage->languageText('mod_useradmin_selfdelete0'),"class='pseudobutton'");
			echo '<br />';
            echo $objHref->show();
        }
    } 
	else if (($this->isAdmin)&& (!$isLDAPUser)){ 
        $this->loadclass('href','htmlelements');
        $objHref=new href(
			$this->uri(
				array(
					'action'=>'adminchangepassword',
					'userId'=>$userDetails['userid'],
					'username'=>$userDetails['username']
				),'useradmin'
			),
        	$objLanguage->languageText("mod_useradmin_changepassword2","Change Password"),"class='pseudobutton'"
		);
		echo '<br />';
        echo $objHref->show();
    }

    if ($isAdminUser) {
        echo $this->userAdminMenu();
    }
?>
