<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$type = 'Activity';
$categorytypeList = $this->objDbCategorytypeList->listCategorytype($type);
//	$objLabel =& $this->newObject('label', 'htmlelements');
$usercontexts = $this->getUserContexts();
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_addactivity", 'eportfolio');
echo $objHeading->show();
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'addactivityconfirm'
)));
$objTable = new htmltable();
$objTable->width = '100%';
$objTable->attributes = " align='left' border='0'";
$objTable->cellspacing = '5';
$objTable->startRow();
$row = array(
    "<b>" . $objLanguage->languageText("word_name") . ":</b>"
);
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $objUser->fullName()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//contexttitle drop down field
$objTable->startRow();
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'top', 'right');
//Context Drop down list
$dropdown = new dropdown('contexttitle');
if (!empty($usercontexts)) {
    $dropdown->addOption('None', "-Select Option-");
    foreach($usercontexts as $mycontext) {
        $dropdown->addOption($mycontext['contextcode'], $mycontext['title']);
    }
} else {
    $dropdown->addOption('None', "You are not registered for any course");
}
$row = array(
    $dropdown->show()
);
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//end Context drop down list
//type drop down list
$mydropdown = new dropdown('activityType');
if (!empty($categorytypeList)) {
    foreach($categorytypeList as $categories) {
        $mydropdown->addOption($categories['id'], $categories['type']);
    }
} else {
    $mydropdown->addOption('None', "-There are no Types-");
}
$objTable->startRow();
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_activitytype", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $mydropdown->show()
);
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//activity start text box
$objTable->startRow();
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'bottom', 'right');
$activityStart = &$this->getObject('datepicker', 'htmlelements');
$activityStart->setName('activityStart');
$form->addRule('activityStart', 'Please enter Activity Start Date', 'required');
$row = array(
    $activityStart->show()
);
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//activity finish text box
$objTable->startRow();
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'bottom', 'right');
$activityFinish = &$this->getObject('datepicker', 'htmlelements');
$activityFinish->setName('activityFinish');
//$form->addRule('activityFinish', 'Please enter Activity Finish Date', 'required');
$row = array(
    $activityFinish->show()
);
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//short description text box
$objTable->startRow();
$shortdescription = new textarea("shortdescription", "");
$form->addRule('shortdescription', 'Please enter a short description', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $shortdescription->show()
);
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//long description text field
$objTable->startRow();
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'top', 'right');
//Add the WYSWYG editor
$editor = $this->newObject('htmlarea', 'htmlelements');
$editor->setName('longdescription');
$editor->height = '300px';
$editor->width = '550px';
$longdescription = '';
//To set the basic toolbar
//$editor->setBasicToolBar();
$editor->setContent($longdescription);
//$objTable->addCell($editor->showFCKEditor(), NULL, "top", "center", NULL, "colspan=\"2\"");
$row = array(
    $editor->show()
);
//$form->addRule('longdescription', 'Please enter the long description','required');
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_activity'
)));
//$objCancel->link = $buttonCancel->show();
$objCancel->link = $objLanguage->languageText("mod_filemanager_returnto", "filemanager") . " " . $objLanguage->languageText("mod_eportfolio_eportfoliohome", "eportfolio");
$linkCancel = $objCancel->show();
$row = array(
    $button->show()
);
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->startRow();
$objTable->startRow();
$objTable->addCell('&nbsp;', 140, 'top', 'right');
$objTable->addCell($linkCancel, Null, 'top', 'left');
$objTable->endRow();
$form->addToForm($objTable->show());
echo $form->show();
?>
