<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_addProduct", 'eportfolio');
echo $objHeading->show();
//Get User assertions, assignments
$Id = $this->_objGroupAdmin->getUserGroups($userPid);
$userassignments = $this->_objDBAssgnment->userAssignments($userId, NULL);
$userEssays = $this->_objDBEssay->getUserEssays($userId);
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'addproductconfirm'
)));
$objTable = new htmltable();
$objTable->width = '30';
$objTable->attributes = " align='center' border='0'";
$objTable->cellspacing = '12';
$row = array(
    "<b>" . $objLanguage->languageText("word_name") . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $objUser->fullName()
);
$objTable->addRow($row, NULL);
//type text box
$textinput = new textinput("producttype", "");
$textinput->size = 40;
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_producttype", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $textinput->show()
);
$objTable->addRow($row, NULL);
//comment text box
$textinput = new textinput("comment", "");
$textinput->size = 40;
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_wordComment", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $textinput->show()
);
$objTable->addRow($row, NULL);
//referential source text box
$textinput = new textinput("referential_source", "");
$textinput->size = 40;
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_referentialSource", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $textinput->show()
);
$objTable->addRow($row, NULL);
//referential identification text box
$textinput = new textinput("referential_id", "");
$textinput->size = 40;
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_referentialId", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $textinput->show()
);
$objTable->addRow($row, NULL);
//assertion id label
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_assertionSource", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
//assertion Drop down list
$dropdown = new dropdown('assertion_id');
if (!empty($Id)) {
    $i = 0;
    foreach($Id as $groupId) {
        $dropdown->addOption('None', "-Select Option-");
        //Get the group parent_id
        $parentId = $this->_objGroupAdmin->getParent($groupId);
        foreach($parentId as $myparentId) {
            $assertionId = $this->_objGroupAdmin->getName($myparentId['parent_id']);
            $list = $this->objDbAssertionList->listSingle($assertionId);
            if (!empty($list)) {
                $dropdown->addOption($list[0]['id'], $list[0]['rationale']);
            }
        }
    }
} else {
    $dropdown->addOption('None', "You dont have any assertions");
}
$row = array(
    $dropdown->show()
);
$objTable->addRow($row, NULL);
//assignment label
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_assignmentSource", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
//assignment Drop down list
$dropdown = new dropdown('assignment_id');
if (!empty($userassignments)) {
    $dropdown->addOption('None', "-Select Option-");
    foreach($userassignments as $myassignments) {
        $dropdown->addOption($myassignments['id'], $myassignments['name']);
    }
} else {
    $dropdown->addOption('None', "You dont have assignments");
}
$row = array(
    $dropdown->show()
);
$objTable->addRow($row, NULL);
//essay label
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_essaySource", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
//essay Drop down list
$dropdown = new dropdown('essay_id');
if (!empty($userEssays)) {
    $dropdown->addOption('None', "-Select Option-");
    foreach($userEssays as $myEssays) {
        $dropdown->addOption($myEssays['id'], $myEssays['name']);
    }
} else {
    $dropdown->addOption('None', "You dont have essays");
}
$row = array(
    $dropdown->show()
);
$objTable->addRow($row, NULL);
//date calendar
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
$startField = $this->objPopupcal->show('creation_date', 'yes', 'no', $creation_date);
$row = array(
    $startField
);
$objTable->addRow($row, NULL);
//short description text box
$textinput = new textinput("shortdescription", "");
$textinput->size = 40;
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_shortProduct", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $textinput->show()
);
$objTable->addRow($row, NULL);
//Full description text field
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>"
);
$objTable->addRow($row, NULL);
//Add the WYSWYG editor
$editor = $this->newObject('htmlarea', 'htmlelements');
$editor->setName('longdescription');
$editor->height = '300px';
$editor->width = '450px';
$longdescription = '';
$editor->setContent($longdescription);
$row = array(
    $editor->show()
);
$objTable->addRow($row, NULL);
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_product'
)));
$objCancel->link = $buttonCancel->show();
$linkCancel = $objCancel->show();
$row = array(
    $button->show() . ' / ' . $linkCancel
);
$objTable->addRow($row, NULL);
$form->addToForm($objTable->show());
echo $form->show();
?>
