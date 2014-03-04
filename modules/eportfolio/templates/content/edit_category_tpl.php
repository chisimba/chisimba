<?php
$hasAccess = $this->objEngine->_objUser->isContextLecturer();
$hasAccess|= $this->objEngine->_objUser->isAdmin();
$this->setVar('pageSuppressXML', true);
if (!$hasAccess) {
    // Redirect
    return $this->nextAction('main', array());
    break;
} else {
    // Load classes.
    $this->loadClass("form", "htmlelements");
    $this->loadClass("textinput", "htmlelements");
    $this->loadClass('textarea', 'htmlelements');
    $this->loadClass("button", "htmlelements");
    $this->loadClass("htmltable", 'htmlelements');
    $this->loadClass('dropdown', 'htmlelements');
    $objCheck = $this->loadClass('checkbox', 'htmlelements');
    $objWindow = &$this->newObject('windowpop', 'htmlelements');
    $objHeading = &$this->getObject('htmlheading', 'htmlelements');
    $objHeading->type = 1;
    $objHeading->str = $objLanguage->languageText("mod_eportfolio_editCategory", 'eportfolio');
    echo $objHeading->show();
    $form = new form("add", $this->uri(array(
        'module' => 'eportfolio',
        'action' => 'editcategoryconfirm',
        'id' => $id
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
    //category text box
    $category = new textinput("category", $category);
    $category->size = 60;
    $form->addRule('category', 'Please enter the Category', 'required');
    $row = array(
        "<b>" . $label = $objLanguage->languageText("mod_eportfolio_category", 'eportfolio') . ":</b>"
    );
    $objTable->addRow($row, NULL);
    $row = array(
        $category->show()
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
        'action' => 'view_category'
    )));
    //$objCancel->link = $buttonCancel->show();
    $objCancel->link = $objLanguage->languageText("mod_filemanager_returnto", "filemanager") . " " . $objLanguage->languageText("mod_eportfolio_eportfoliohome", "eportfolio");
    $linkCancel = $objCancel->show();
    $row = array(
        $button->show()
    );
    $objTable->addRow($row, NULL);
    $objTable->startRow();
    $objTable->addCell('&nbsp;', 140, 'top', 'right');
    $objTable->addCell($linkCancel, Null, 'top', 'left');
    $objTable->endRow();
    $form->addToForm($objTable->show());
    echo $form->show();
}
?>
