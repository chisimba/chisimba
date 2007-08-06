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
$header->str = $this->objLanguage->languageText('mod_userdetails_name', 'userdetails');
$header->type = 1;
echo $header->show();


if (isset($showconfirmation) && $showconfirmation) {
    echo '<div id="confirmationmessage">';
    if ($this->getParam('change') == 'details') {
        echo '<ul><li><span class="confirm">'.$this->objLanguage->languageText('mod_userdetails_detailssuccessfullyupdate', 'userdetails').'</span></li>';
        
        if ($this->getParam('passworderror') == 'passworddonotmatch') {
            echo '<li><span class="error">'.$this->objLanguage->languageText('mod_userdetails_repeatpasswordnotmatch', 'userdetails').'</span></li>';
        } else if ($this->getParam('passwordchanged') == TRUE) {
            echo '<li><span class="confirm">'.$this->objLanguage->languageText('mod_userdetails_passwordupdated', 'userdetails').'</span></li>';
        } else {
            echo '<li><span class="warning">'.$this->objLanguage->languageText('mod_userdetails_passwordnotchanged', 'userdetails').'</span></li>';
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
    $email->extra = ' maxlength="100"';
    $email->value = $user['emailaddress'];
    
    if ($mode == 'addfixup') {
        $email->value = $this->getParam('useradmin_email');
        
        if ($this->getParam('useradmin_email') == '') {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_enteremailaddress', 'userdetails');
        } else if (!$this->objUrl->isValidFormedEmailAddress($this->getParam('useradmin_email'))) {
            $messages[] = $this->objLanguage->languageText('mod_userdetails_entervalidemailaddress', 'userdetails');
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
$objBizCard->resetModule = 'userdetails';

echo $objBizCard->show();






echo '<div id="formresults"></div>';





$form = new form ('updatedetails', $this->uri(array('action'=>'updateuserdetails')));

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
    $objCountries=$this->getObject('languagecode','language');
    
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

// Username
$table->startRow();
    $table->addCell($this->objLanguage->languageText('word_username', 'system'));
    $table->addCell('&nbsp;');
    $table->addCell($user['username']);
$table->endRow();

if (strtolower($user['howcreated']) != 'ldap') {


    // Password
    $table->startRow();
        $label = new label ($this->objLanguage->languageText('word_password', 'system'), 'input_useradmin_password');
        
        $textinput = new textinput ('useradmin_password');
        $textinput->fldType = 'password';
        $textinput->size = 15;
        $textinput->extra = ' autocomplete="off"';
        
        $table->addCell($label->show());
        $table->addCell('&nbsp;');
        $table->addCell($textinput->show().' - '.$this->objLanguage->languageText('phrase_leavepasswordblank', 'userdetails'));
    $table->endRow();

    // Repeat Password
    $table->startRow();
        $label = new label ($this->objLanguage->languageText('phrase_repeatpassword', 'userdetails'), 'input_useradmin_repeatpassword');
        
        $textinput = new textinput ('useradmin_repeatpassword');
        $textinput->fldType = 'password';
        $textinput->size = 15;
        $textinput->extra = ' autocomplete="off"';
        

        
        $table->addCell($label->show());
        $table->addCell('&nbsp;');
        $table->addCell($textinput->show());
    $table->endRow();
} else {
    // Password
    $table->startRow();
        
        $table->addCell('Password');
        $table->addCell('&nbsp;');
        $table->addCell('<em>Using Network ID Password</em>');
    $table->endRow();
}



$form->addToForm($table->show());

$button = new button ('submitform', $this->objLanguage->languageText('mod_useradmin_updatedetails', 'useradmin'));
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





$returnlink = new link($this->uri(NULL, '_default'));
$returnlink->link = 'Return to Home Page';
echo '<br clear="left" />'.$returnlink->show();
?>