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
$header->str = $this->objLanguage->languageText('mod_eportfolio_title', 'eportfolio') . ' ' . $this->objLanguage->languageText('word_for') . ' ' . $objUser->getSurname();
$header->type = 2;
echo $header->show();
if (isset($showconfirmation) && $showconfirmation) {
    echo '<div id="confirmationmessage">';
    if ($this->getParam('change') == 'details') {
        echo '<ul><li><span class="confirm">' . $this->objLanguage->languageText('mod_userdetails_detailssuccessfullyupdate', 'userdetails') . '</span></li>';
        echo '</ul>';
    }
    /*
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
    */
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
$surname = new textinput('eportfolio_surname');
$surname->size = 30;
$surname->extra = ' maxlength="50"';
$surname->value = $user['surname'];
if ($mode == 'addfixup') {
    $surname->value = $this->getParam('eportfolio_surname');
    if ($this->getParam('eportfolio_surname') == '') {
        $messages[] = $this->objLanguage->languageText('mod_eportfolio_entersurname', 'eportfolio');
    }
}
$othernames = new textinput('eportfolio_othernames');
$othernames->size = 30;
$othernames->extra = ' maxlength="50"';
$othernames->value = $user['firstname'];
if ($mode == 'addfixup') {
    $othernames->value = $this->getParam('eportfolio_othernames');
    if ($this->getParam('eportfolio_othernames') == '') {
        $messages[] = $this->objLanguage->languageText('mod_eportfolio_enterothernames', 'eportfolio');
    }
}
if ($mode == 'addfixup' && count($messages) > 0) {
    echo '<ul><li><span class="error">' . $this->objLanguage->languageText('mod_eportfolio_infonotsavedduetoerrors', 'eportfolio') . '</span>';
    echo '<ul>';
    foreach($messages as $message) {
        echo '<li class="error">' . $message . '</li>';
    }
    echo '</ul></li></ul>';
}
$objBizCard = $this->getObject('userbizcard', 'useradmin');
$objBizCard->setUserArray($user);
$objBizCard->showResetImage = FALSE;
$objBizCard->resetModule = 'userdetails';
// echo $objBizCard->show();
echo '<div id="formresults"></div>';
$form = new form('updatedetails', $this->uri(array(
    'action' => 'updateuserdetails'
)));
echo '<div style="width:70%; float:left; padding:5px; boorder:1px solid red;">';
$table = new htmltable();
$table->width = '40';
$table->attributes = " align='center' border='0'";
$table->cellspacing = '12';
// Title
$table->startRow();
$label = new label($this->objLanguage->languageText('word_title', 'system') , 'input_eportfolio_title');
$objDropdown = new dropdown('eportfolio_title');
$titles = array(
    "title_mr",
    "title_miss",
    "title_mrs",
    "title_ms",
    "title_dr",
    "title_prof",
    "title_rev",
    "title_assocprof"
);
foreach($titles as $title) {
    $_title = trim($objLanguage->languageText($title));
    $objDropdown->addOption($_title, $_title);
}
if ($mode == 'addfixup') {
    $objDropdown->setSelected($this->getParam('eportfolio_title'));
} else {
    $objDropdown->setSelected($user['title']);
}
$table->addCell($label->show() , 140);
$table->addCell('&nbsp;');
$table->addCell($objDropdown->show());
$table->endRow();
// Surname
$table->startRow();
$label = new label($this->objLanguage->languageText('word_surname', 'system') , 'input_eportfolio_firstname');
$table->addCell($label->show());
$table->addCell('&nbsp;');
$table->addCell($surname->show());
$table->endRow();
// othernames
$table->startRow();
$label = new label($this->objLanguage->languageText('phrase_othernames', 'eportfolio') , 'input_eportfolio_othernames');
$table->addCell($label->show());
$table->addCell('&nbsp;');
$table->addCell($othernames->show());
$table->endRow();
// Spacer
$table->startRow();
$table->addCell('&nbsp;');
$table->addCell('&nbsp;');
$table->addCell('&nbsp;');
$table->endRow();
$form->addToForm($table->show());
$button = new button('submitform', $this->objLanguage->languageText('mod_useradmin_updatedetails', 'system'));
$button->setToSubmit();
// $button->setOnClick('validateForm()');
//Save button
$button = new button("submit", $objLanguage->languageText("mod_useradmin_updatedetails", "system")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_contact'
)));
$objCancel->link = $buttonCancel->show();
$linkCancel = $objCancel->show();
$form->addToForm('<p>' . $button->show() . ' / ' . $linkCancel . '</p>');
$form->addRule('eportfolio_surname', $this->objLanguage->languageText('mod_eportfolio_entersurname', 'eportfolio') , 'required');
$form->addRule('eportfolio_othernames', $this->objLanguage->languageText('mod_eportfolio_enterothernames', 'eportfolio') , 'required');
echo $form->show();
echo '</div>';
echo '<div><div style="width:25%;  float: left; padding: 5px;">';
// echo '<h3>'.$this->objLanguage->languageText('phrase_userimage', 'userdetails').':</h3>';
$objModule = $this->getObject('modules', 'modulecatalogue');
$changeimageform = new form('changeimage', $this->uri(array(
    'action' => 'changeimage'
)));
if ($objModule->checkIfRegistered('filemanager')) {
    $objSelectFile = $this->getObject('selectimage', 'filemanager');
    $objSelectFile->name = 'imageselect';
    $objSelectFile->restrictFileList = array(
        'jpg',
        'gif',
        'png',
        'jpeg',
        'bmp'
    );
    $changeimageform->addToForm($objSelectFile->show());
    $button = new button('changeimage', $this->objLanguage->languageText('phrase_updateimage', 'userdetails'));
    $button->setToSubmit();
    $changeimageform->addToForm('<br />' . $button->show());
}
// echo $changeimageform->show();
echo '</div>';
echo '</div>';
?>
