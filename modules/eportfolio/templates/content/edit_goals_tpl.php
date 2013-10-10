<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$type = 'Goal';
$categorytypeList = $this->objDbCategorytypeList->listCategorytype($type);
$mytype = 'Priority';
$mycategorytypeList = $this->objDbCategorytypeList->listCategorytype($mytype);
$objCheck = $this->loadClass('checkbox', 'htmlelements');
//$userid = $this->objUser->userId();
$mygoals = $this->objDbGoalsList->getUserGoals();
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_editGoals", 'eportfolio');
echo $objHeading->show();
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'editgoalsconfirm',
    'id' => $id
)));
$objTable = new htmltable();
$objTable->width = '100%';
$objTable->attributes = " align='left' border='0'";
$objTable->cellspacing = '5';
$row = array(
    "<b>" . $objLanguage->languageText("word_name") . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $objUser->fullName()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
/*
//parent goal label
$row = array(
"<b>" . $label = $objLanguage->languageText("mod_eportfolio_parent", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
//Goals Drop down list
$dropdown = new dropdown('parentid');
if (!empty($mygoals)) {
$dropdown->addOption('None', "-Root-");
foreach($mygoals as $goals) {
//Dont select the current goal
if ($id !== $goals['id']) {
$dropdown->addOption($goals['id'], $goals['shortdescription']);
$dropdown->setSelected($parentid);
}
}
} else {
$dropdown->addOption('None', "-Root-");
}
$row = array(
$dropdown->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//end Goals drop down list
*/
//type drop down list
$mydropdown = new dropdown('goal_type');
if (!empty($categorytypeList)) {
    foreach($categorytypeList as $categories) {
        $mydropdown->addOption($categories['id'], $categories['type']);
        $mydropdown->setSelected($goal_type);
    }
} else {
    $mydropdown->addOption('None', "-There are no Types-");
}
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_goalsType", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $mydropdown->show()
);
$objTable->addCell($row[0], Null, 'bottom', 'left');
$objTable->endRow();
//start calendar
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'bottom', 'right');
$startDateField = &$this->getObject('datepicker', 'htmlelements');
$startDateField->setName('start');
$startDateField->setDateFormat("YYYY-MM-DD");

//Remove the time
$start = explode(" ", $start);

$startDateField->setDefaultDate($start[0]);
$form->addRule('start', 'Please enter the start date', 'required');
$row = array(
    $startDateField->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//priority drop down list
$dropdown = new dropdown('priority');
if (!empty($mycategorytypeList)) {
    foreach($mycategorytypeList as $categories) {
        $dropdown->addOption($categories['id'], $categories['type']);
        $dropdown->setSelected($priority);
    }
} else {
    $dropdown->addOption('None', "-There are no Types-");
}
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_priority", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $dropdown->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//status text field
$textinput = new textinput("status", $status);
$textinput->size = 60;
$form->addRule('status', 'Please enter the status', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_status", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $textinput->show()
);
$objTable->addCell($row[0], Null, 'bottom', 'left');
$objTable->endRow();
//status date text box
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_statusDate", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'bottom', 'right');
$statusDateField = &$this->getObject('datepicker', 'htmlelements');
$statusDateField->setName('status_date');
$statusDateField->setDateFormat("YYYY-MM-DD");
//Remove the time
$status_date = explode(" ", $status_date);
$statusDateField->setDefaultDate($status_date[0]);
$form->addRule('status_date', 'Please enter the status date', 'required');
$row = array(
    $statusDateField->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//short description text field
$textinput = new textarea("shortdescription", $shortdescription);
$form->addRule('shortdescription', 'Please enter the short description', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $textinput->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Full description text field
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
//Add the WYSWYG editor
$editor = $this->newObject('htmlarea', 'htmlelements');
$editor->setName('longdescription');
$editor->height = '300px';
$editor->width = '450px';
$editor->setContent($longdescription);
$row = array(
    $editor->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_goals'
)));
//$objCancel->link = $buttonCancel->show();
$objCancel->link = $objLanguage->languageText("mod_filemanager_returnto", "filemanager") . " " . $objLanguage->languageText("mod_eportfolio_eportfoliohome", "eportfolio");
$linkCancel = $objCancel->show();
$row = array(
    $button->show()
);
$objTable->startRow();
$objTable->addCell('&nbsp;', 140, 'top', 'right');
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('&nbsp;', 140, 'top', 'right');
$objTable->addCell($linkCancel, Null, 'top', 'left');
$objTable->endRow();
$form->addToForm($objTable->show());
echo $form->show();
?>
