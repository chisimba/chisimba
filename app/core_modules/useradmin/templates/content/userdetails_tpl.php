<script type="text/javascript">
//<![CDATA[
    
    function checkAccountType(value)
    {
        if (value == 'ldap') {
            document.getElementById('input_useradmin_password').disabled = true;
            document.getElementById('input_useradmin_repeatpassword').disabled = true;
            document.getElementById('leaveblank').style.display = 'none';
            document.getElementById('ldappass').style.display = 'inline';
        } else {
            document.getElementById('input_useradmin_password').disabled = false;
            document.getElementById('input_useradmin_repeatpassword').disabled = false;
            document.getElementById('leaveblank').style.display = 'inline';
            document.getElementById('ldappass').style.display = 'none';
        }
    }
    
    function resetUsername()
    {
        document.getElementById('input_useradmin_username').value='<?php echo $user['username']; ?>';
    }
//]]>
</script>
<?php

$this->setVar('pageSuppressXML', TRUE);

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('mouseoverpopup', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$header = new htmlheading();
$header->str = $this->objLanguage->languageText('mod_userdetails_name', 'userdetails').' - '.$user['firstname'].' '.$user['surname'];
$header->type = 1;
echo $header->show();

$useridinput = new hiddeninput('id', $user['id']);

if ($this->getParam('message') == 'newusercreated') {
    echo '<span class="confirm">New User has been created</span><br /><br />';
}

if (isset($showconfirmation) && $showconfirmation) {
    echo '<div id="confirmationmessage">';
    if ($this->getParam('change') == 'details') {
        echo '<ul><li><span class="confirm">'.'User\'s details have been successfully changed'.'</span></li>';
        
        if ($this->getParam('passworderror') == 'passworddonotmatch') {
            echo '<li><span class="error">'.$this->objLanguage->languageText('mod_userdetails_repeatpasswordnotmatch', 'userdetails').'</span></li>';
        } else if ($this->getParam('passwordchanged') == TRUE) {
            echo '<li><span class="confirm">'.'User\'s password has been changed'.'</span></li>';
        } else {
            echo '<li><span class="warning">'.'User\'s password has NOT been changed'.'</span></li>';
        }

        
        echo '</ul>';
    }

    if ($this->getParam('change') == 'image') {

        echo '<ul>';
        switch ($this->getParam('message'))
        {
            case 'nopicturegiven':
                echo '<li><span class="error">'.ucfirst($this->objLanguage->languageText('word_error')).': '.$this->objLanguage->languageText('mod_userdetails_noimageprovided', 'userdetails').'</span></li>';
                break;
            case 'fileisnotimage':
                echo '<li><span class="error">'.ucfirst($this->objLanguage->languageText('word_error')).': '.$this->objLanguage->languageText('mod_userdetails_filenotimage', 'userdetails').'</span></li>';
                break;
            case 'imagechanged':
                echo '<li><span class="confirm">'.$this->objLanguage->languageText('mod_userdetails_userimagechanged', 'userdetails').'</span></li>';
                break;
            case 'userimagereset':
                echo '<li><span class="confirm">'.$this->objLanguage->languageText('mod_userdetails_userimagereset', 'userdetails').'</span></li>';
                break;
        }
        echo '</ul>';
    }

    echo '</div>';

    echo '
    <script type="text/javascript">

    function hideConfirmation()
    {
        document.getElementById(\'confirmationmessage\').style.display="none";
    }

    setTimeout("hideConfirmation()", 10000);
    </script>
    ';

}

// Array to hold error messages
$messages = array();

//Create Form Elements, as well detect associated problems

    $firstname = new textinput ('useradmin_firstname');
    $firstname->size = 30;
    $firstname->extra = ' maxlength="50"';
    $firstname->value = $user['firstname'];
    
    if ($mode == 'addfixup') {
        $firstname->value = $this->getParam('useradmin_firstname');
        
        if ($this->getParam('useradmin_firstname') == '') {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_enterfirstname', 'userdetails');
        }
    }
    
    $surname = new textinput ('useradmin_surname');
    $surname->size = 30;
    $surname->extra = ' maxlength="50"';
    $surname->value = $user['surname'];
    
    if ($mode == 'addfixup') {
        $surname->value = $this->getParam('useradmin_surname');
        
        if ($this->getParam('useradmin_surname') == '') {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_entersurname', 'userdetails');
        }
    }
    
    $email = new textinput ('useradmin_email');
    $email->size = 30;
    $email->extra = ' maxlength="25"';
    $email->value = $user['emailaddress'];
    
    if ($mode == 'addfixup') {
        $email->value = $this->getParam('useradmin_email');
        
        if ($this->getParam('useradmin_email') == '') {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_enteremailaddress', 'userdetails');
        } else if (!$this->objUrl->isValidFormedEmailAddress($this->getParam('useradmin_email'))) {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_entervalidemailaddress', 'userdetails');
        }
    }

if ($mode == 'addfixup') {

    switch ($problem)
    {
        case 'nopasswordforldap': $messages[] = 'If you want to convert this account from Network ID Authentication to a Site Authentication, please enter new passwords'; break;
        case 'ldappasswordnotmatching': $messages[] = 'You attempted to convert this account from Network ID Authentication to a Site Authentication, but the password did not match.'; break;
        case 'usernametaken': $messages[] = 'The username you have assigned to this user has already been taken'; break;
        default: break;
    }

}


if ($mode == 'addfixup' && count($messages) > 0) {
    echo '<ul><li><span class="error">'.$this->objLanguage->languageText('mod_userdetails_infonotsavedduetoerrors', 'userdetails').'</span>';
    
    echo '<ul>';
        foreach ($messages as $message)
        {
            echo '<li class="error">'.$message.'</li>';
        }
    echo '</ul></li></ul>';
}

$objBizCard = $this->getObject('userbizcard', 'useradmin');
$objBizCard->setUserArray($user);
$objBizCard->showResetImage = TRUE;
$objBizCard->resetModule = $this->getParam('module');

echo $objBizCard->show();






echo '<div id="formresults"></div>';





$form = new form ('updatedetails', $this->uri(array('action'=>'updateuserdetails')));
$form->addToForm($useridinput->show());

echo '<div style="width:70%; float:left; padding:5px; boorder:1px solid red;">';
echo '<h3>'.$this->objLanguage->languageText('phrase_userinformation', 'userdetails').':</h3>';


$table = $this->newObject('htmltable', 'htmlelements');

// Title
$table->startRow();
    $label = new label ($this->objLanguage->languageText('word_title', 'system'), 'input_useradmin_title');
    
    $objDropdown = new dropdown('useradmin_title');
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    foreach ($titles as $title)
    {
        $_title=trim($objLanguage->languageText($title));
        $objDropdown->addOption($_title,$_title);
    }
    
    if ($mode == 'addfixup') {
        $objDropdown->setSelected($this->getParam('useradmin_title'));
    } else {
        $objDropdown->setSelected($user['title']);
    }
    
    $table->addCell($label->show(), 140);
    $table->addCell('&nbsp;');
    $table->addCell($objDropdown->show());
$table->endRow();

// Firstname
$table->startRow();
    $label = new label ($this->objLanguage->languageText('phrase_firstname', 'system'), 'input_useradmin_firstname');
    
    
    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($firstname->show());
$table->endRow();

// Surname
$table->startRow();
    $label = new label ($this->objLanguage->languageText('word_surname', 'system'), 'input_useradmin_surname');
    
    
    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($surname->show());
$table->endRow();

// Staff Number
$table->startRow();
    $label = new label ('Staff/Student Number', 'input_useradmin_staffnumber');

    $staffNumber = new textinput('useradmin_staffnumber', $user['staffnumber']);
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($staffNumber->show());
$table->endRow();

// Email
$table->startRow();
    $label = new label ($this->objLanguage->languageText('phrase_emailaddress', 'system'), 'input_useradmin_email');
    

    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($email->show());
$table->endRow();

// Cell Number
$table->startRow();
    $label = new label ('Cell Number', 'input_useradmin_cellnumber');

    $cellNumber = new textinput('useradmin_cellnumber', $user['cellnumber']);
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($cellNumber->show());
$table->endRow();

// Sex
$table->startRow();
    $sexRadio = new radio ('useradmin_sex');
    $sexRadio->addOption('M', $this->objLanguage->languageText('word_male', 'system'));
    $sexRadio->addOption('F', $this->objLanguage->languageText('word_female', 'system'));
    $sexRadio->setBreakSpace(' &nbsp; ');
    
    $sexRadio->setSelected($user['sex']);
    
    if ($mode == 'addfixup') {
        $sexRadio->setSelected($this->getParam('useradmin_sex'));
    }
    
    $table->addCell($this->objLanguage->languageText('word_sex', 'system'));
    $table->addCell('&nbsp;');
    $table->addCell($sexRadio->show());
$table->endRow();

// Country
$table->startRow();
    //$objCountries=&$this->getObject('countries','utilities');
    $objCountries=&$this->getObject('languagecode','language');
    
    $table->addCell($this->objLanguage->languageText('word_country', 'system'));
    $table->addCell('&nbsp;');
    //if ($mode == 'addfixup') {
        $table->addCell($objCountries->country($user['country']));
    // } else {
    
    // }
$table->endRow();

// Spacer
$table->startRow();
    $table->addCell('&nbsp;');
    $table->addCell('&nbsp;');
    $table->addCell('&nbsp;');
$table->endRow();

$table->startRow();
    $table->addCell('Account Status');
    $table->addCell('&nbsp;');
    
    $accountStatusRadio = new radio('accountstatus');
    $accountStatusRadio->addOption(1, 'Active');
    $accountStatusRadio->addOption(0, 'Inactive');
    $accountStatusRadio->setBreakSpace(' ');
    //$accountTypeRadio->extra = 'onclick="checkAccountType(this.value);"';
    
    if ($mode == 'addfixup') {
        $accountStatusRadio->setSelected($this->getParam('accountstatus'));
    } else if ($user['isactive'] == '0') {
        $accountStatusRadio->setSelected(0);
    } else {
        $accountStatusRadio->setSelected(1);
    }
    
    $table->addCell($accountStatusRadio->show());
$table->endRow();

// Type of Account
if ($this->objConfig->getuseLDAP() == 'TRUE') {
    $table->startRow();
        $table->addCell('Type of Account');
        $table->addCell('&nbsp;');
        
        $accountTypeRadio = new radio('accounttype');
        $accountTypeRadio->addOption('ldap', ' Network ID Authentication');
        $accountTypeRadio->addOption('useradmin', ' Site / Database Authentication');
        $accountTypeRadio->setBreakSpace('<br />');
        $accountTypeRadio->extra = 'onclick="checkAccountType(this.value);"';
        
        if ($mode == 'addfixup') {
            $accountTypeRadio->setSelected($this->getParam('accounttype'));
        } else if ($user['howcreated'] == 'LDAP') {
            $accountTypeRadio->setSelected('ldap');
        } else {
            $accountTypeRadio->setSelected('useradmin');
        }
        
        $table->addCell($accountTypeRadio->show());
    $table->endRow();
} else {
    $accountType = new hiddeninput('accounttype', 'useradmin');
    $form->addToForm($accountType->show());
}

// Username
$table->startRow();
    $label = new label ('Username', 'input_useradmin_username');
    
    $textinput = new textinput ('useradmin_username');
    $textinput->size = 30;
    $textinput->extra = ' maxlength="25"';
    $textinput->value = $user['username'];
    
    $usernameAddition = '';
    
    if ($mode == 'addfixup') {
        $textinput->value = $this->getParam('useradmin_username');
        
        if ($this->getParam('useradmin_username') == '') {
            $messages[] = 'Username cannot be blank';
        }
        
        if ($problem == 'usernametaken') {
            $usernameAddition = ' * <span class="warning">Username Taken</span>';
            
            $usernameAddition .= ' - <a href="javascript:resetUsername();">';
            $usernameAddition .= 'Reset</a>';
        }
    }
    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($textinput->show().$usernameAddition);
$table->endRow();

// Password
$table->startRow();
    $label = new label ('Password', 'input_useradmin_password');
    
    $textinput = new textinput ('useradmin_password');
    $textinput->fldType = 'password';
    $textinput->size = 15;
    $textinput->extra = ' autocomplete="off"';
    
    if ($mode == 'addfixup') {
        $howcreated = strtoupper($this->getParam('accounttype'));
    } else {
        $howcreated = strtoupper($user['howcreated']);
    }
    
    $ldappass = 'password cannot be changed here';
    $leaveblank = 'leave blank to keep existing one';
    
    if ($mode == 'addfixup' && $this->getParam('accounttype') == 'useradmin') {
        $leaveblank = '* Required';
    } else if ($howcreated == 'LDAP'){
        $leaveblank = '* Required if you want Site Authentication';
    }
    
    if ($howcreated == 'LDAP') {
        $textinput->extra .= ' disabled="disabled"';
        $passMsg = '<span id="leaveblank" style="display:none;">'.$leaveblank.'</span>';
        $ldapMsg = '<span id="ldappass">'.$ldappass.'</span>';
    } else {
        $passMsg = '<span id="leaveblank">'.$leaveblank.'</span>';
        $ldapMsg = '<span id="ldappass" style="display:none;">'.$ldappass.'</span>';
    }
    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    
    
    $table->addCell($textinput->show().' - '.$passMsg.$ldapMsg);
$table->endRow();

// Repeat Password
$table->startRow();
    $label = new label ('Repeat Password', 'input_useradmin_repeatpassword');
    
    $textinput = new textinput ('useradmin_repeatpassword');
    $textinput->fldType = 'password';
    $textinput->size = 15;
    $textinput->extra = ' autocomplete="off"';
    
    if ($howcreated == 'LDAP') {
        $textinput->extra .= ' disabled="disabled"';
    }
    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($textinput->show());
$table->endRow();



$form->addToForm($table->show());

$button = new button ('submitform', $this->objLanguage->languageText('mod_useradmin_updatedetails'));
$button->setToSubmit();
// $button->setOnClick('validateForm()');

$form->addToForm('<p>'.$button->show().'</p>');

$form->addRule('useradmin_firstname',$this->objLanguage->languageText('mod_userdetails_enterfirstname', 'userdetails'),'required');
$form->addRule('useradmin_surname',$this->objLanguage->languageText('mod_userdetails_entersurname', 'userdetails'),'required');
$form->addRule('useradmin_email',$this->objLanguage->languageText('mod_userdetails_enteremailaddress', 'userdetails'),'required');
$form->addRule('useradmin_email', $this->objLanguage->languageText('mod_userdetails_entervalidemailaddress', 'userdetails'), 'email');



echo $form->show();

echo '</div>';

echo '<div><div style="width:25%;  float: left; padding: 5px;">';
echo '<h3>'.$this->objLanguage->languageText('phrase_userimage', 'userdetails').':</h3>';



$objModule = $this->getObject('modules', 'modulecatalogue');

$changeimageform = new form('changeimage', $this->uri(array('action'=>'changeimage')));
$changeimageform->addToForm($useridinput->show());




if ($objModule->checkIfRegistered('filemanager')) {

    
    
    
    $objSelectFile = $this->getObject('selectimage', 'filemanager');
    $objSelectFile->name = 'imageselect';
    $objSelectFile->restrictFileList = array('jpg', 'gif', 'png', 'jpeg', 'bmp');
    $changeimageform->addToForm($objSelectFile->show());

    $button = new button ('changeimage', $this->objLanguage->languageText('phrase_updateimage', 'userdetails'));
    $button->setToSubmit();
    
    $changeimageform->addToForm('<br />'.$button->show());

    

}

echo $changeimageform->show();

echo '</div>';
echo '</div>';






$returnlink = new link($this->uri(NULL));
$returnlink->link = 'Return to User Administration';
echo '<br clear="left" />'.$returnlink->show();
?>