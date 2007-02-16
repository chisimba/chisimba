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
    
//]]>
</script>
<?php

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
$header->str ='Add New User';
$header->type = 1;
echo $header->show();


$form = new form ('savenewuser', $this->uri(array('action'=>'savenewuser')));

// Array to hold error messages
$messages = array();

//Create Form Elements, as well detect associated problems

    $firstname = new textinput ('useradmin_firstname');
    $firstname->size = 30;
    $firstname->extra = ' maxlength="50"';
    
    if ($mode == 'addfixup') {
        $firstname->value = $this->getParam('useradmin_firstname');
        
        if ($this->getParam('useradmin_firstname') == '') {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_enterfirstname', 'userdetails');
        }
    }
    
    $surname = new textinput ('useradmin_surname');
    $surname->size = 30;
    $surname->extra = ' maxlength="50"';
    
    if ($mode == 'addfixup') {
        $surname->value = $this->getParam('useradmin_surname');
        
        if ($this->getParam('useradmin_surname') == '') {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_entersurname', 'userdetails');
        }
    }
    
    $email = new textinput ('useradmin_email');
    $email->size = 30;
    $email->extra = ' maxlength="25"';
    
    if ($mode == 'addfixup') {
        $email->value = $this->getParam('useradmin_email');
        
        if ($this->getParam('useradmin_email') == '') {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_enteremailaddress', 'userdetails');
        } else if (!$this->objUrl->isValidFormedEmailAddress($this->getParam('useradmin_email'))) {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_entervalidemailaddress', 'userdetails');
        }
    }

if ($mode == 'addfixup') {

    foreach ($problems as $problem)
    {
        $messages[] = $this->explainProblemsInfo($problem);
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


echo '<div id="formresults"></div>';










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
    $table->addCell($firstname->show().' * <span class="warning">Required</span>');
$table->endRow();

// Surname
$table->startRow();
    $label = new label ($this->objLanguage->languageText('word_surname', 'system'), 'input_useradmin_surname');
    
    
    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($surname->show().' * <span class="warning">Required</span>');
$table->endRow();

// Staff Number
$table->startRow();
    $label = new label ('Staff/Student Number', 'input_useradmin_staffnumber');

    $staffNumber = new textinput('useradmin_staffnumber');
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($staffNumber->show());
$table->endRow();

// Email
$table->startRow();
    $label = new label ($this->objLanguage->languageText('phrase_emailaddress', 'system'), 'input_useradmin_email');
    

    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($email->show().' * <span class="warning">Required</span>');
$table->endRow();

// Cell Number
$table->startRow();
    $label = new label ('Cell Number', 'input_useradmin_cellnumber');

    $cellNumber = new textinput('useradmin_cellnumber');
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
    
    $sexRadio->setSelected('M');
    
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
        $table->addCell($objCountries->country());
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
    
    $usernameAddition = ' * <span class="warning">Required</span>';
    
    if ($mode == 'addfixup') {
        $textinput->value = $this->getParam('useradmin_username');
        
        if ($this->getParam('useradmin_username') == '') {
            $messages[] = 'Username cannot be blank';
        }
        
        if (in_array('usernametaken', $problems)) {
            $usernameAddition = ' * <span class="warning">Username Taken</span>';
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
    $textinput->extra = ' autocomplete="off" maxlength="10"';
    
    if ($mode == 'addfixup') {
        $howcreated = strtoupper($this->getParam('accounttype'));
    } else {
        $howcreated = 'useradmin';
    }
    
    $ldappass = '';
    $leaveblank = ' * Required';
    
    if ($mode == 'addfixup' && $this->getParam('accounttype') == 'useradmin') {
        $leaveblank = ' * <span class="warning">Required</span>';
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
    
    
    $table->addCell($textinput->show().' '.$passMsg.$ldapMsg);
$table->endRow();

// Repeat Password
$table->startRow();
    $label = new label ('Repeat Password', 'input_useradmin_repeatpassword');
    
    $textinput = new textinput ('useradmin_repeatpassword');
    $textinput->fldType = 'password';
    $textinput->size = 15;
    $textinput->extra = ' autocomplete="off" maxlength="10"';
    
    if ($howcreated == 'LDAP') {
        $textinput->extra .= ' disabled="disabled"';
    }
    
    $table->addCell($label->show());
    $table->addCell('&nbsp;');
    $table->addCell($textinput->show());
$table->endRow();







$form->addRule('useradmin_firstname',$this->objLanguage->languageText('mod_userdetails_enterfirstname', 'userdetails'),'required');
$form->addRule('useradmin_surname',$this->objLanguage->languageText('mod_userdetails_entersurname', 'userdetails'),'required');
$form->addRule('useradmin_email',$this->objLanguage->languageText('mod_userdetails_enteremailaddress', 'userdetails'),'required');
$form->addRule('useradmin_username', "Please enter the username for the user" ,'required');
$form->addRule('useradmin_email', $this->objLanguage->languageText('mod_userdetails_entervalidemailaddress', 'userdetails'), 'email');

// Add User Info Side to form
$form->addToForm('<div style="width:60%; float:left; padding:5px;">');
$form->addToForm('<h3>'.$this->objLanguage->languageText('phrase_userinformation', 'userdetails').':</h3>');
$form->addToForm($table->show());
$form->addToForm('</div>');
    
$objModule = $this->getObject('modules', 'modulecatalogue');
if ($objModule->checkIfRegistered('filemanager')) {
    $form->addToForm('<div><div style="width:25%;  float: left; padding: 5px;">');
    $form->addToForm('<h3>'.$this->objLanguage->languageText('phrase_userimage', 'userdetails').':</h3>');



    

 
    $objSelectFile = $this->getObject('selectimage', 'filemanager');
    $objSelectFile->name = 'imageselect';
    $objSelectFile->restrictFileList = array('jpg', 'gif', 'png', 'jpeg', 'bmp');
    
    if ($mode == 'addfixup') {
        $objSelectFile->setDefaultFile($this->getParam('imageselect'));
    }
    $form->addToForm($objSelectFile->show());

    $form->addToForm('</div>');
    $form->addToForm('</div>');
}
$button = new button ('submitform', 'Add New User');
$button->setToSubmit();
// $button->setOnClick('validateForm()');

$form->addToForm('<br clear="left" /><br /><p>'.$button->show().'</p>');

echo $form->show();








$returnlink = new link($this->uri(NULL));
$returnlink->link = 'Return to User Administration';
echo '<br clear="left" />'.$returnlink->show();
?>