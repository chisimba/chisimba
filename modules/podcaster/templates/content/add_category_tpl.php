<?php
//Add if admin
$hasAccess = $this->objUser->isAdmin();
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
    $objHeading->str = $objLanguage->languageText("mod_podcaster_addcategory", 'podcaster', 'Add category');
    echo $objHeading->show();
    $form = new form("add", $this->uri(array(
        'module' => 'podcaster',
        'action' => 'addcategoryconfirm'
    )));
    $objTable = new htmltable();
    $objTable->width = '30';
    $objTable->attributes = " align='center' border='0'";
    $objTable->cellspacing = '12';
    $row = array(
        "<b>" . $objLanguage->languageText("word_name", 'system', 'Name') . ":</b>"
    );
    $objTable->startRow();
    $objTable->addCell($row[0], 140, 'top', 'right');
    $row = array(
        $objUser->fullName()
    );
    $objTable->addCell($row[0], Null, 'top', 'left');
    $objTable->endRow();
    //category text box
    $category = new textinput("category", "");
    $category->size = 30;
    $form->addRule('category', 'Please enter the Category', 'required');
    $row = array(
        "<b>" . $label = $objLanguage->languageText("mod_podcaster_category", 'podcaster', 'Category') . ":</b>"
    );
    $objTable->startRow();
    $objTable->addCell($row[0], 140, 'top', 'right');
    $row = array(
        $category->show()
    );
    $objTable->addCell($row[0], Null, 'top', 'left');
    $objTable->endRow();
    //description text box
    $category = new textarea("description", "");
    $form->addRule('description', 'Please enter a Description of this category', 'required');
    $row = array(
        "<b>" . $label = $objLanguage->languageText("word_description", 'system', "Description") . ":</b>"
    );
    $objTable->startRow();
    $objTable->addCell($row[0], 140, 'top', 'right');
    $row = array(
        $category->show()
    );
    $objTable->addCell($row[0], Null, 'top', 'left');
    $objTable->endRow();
    //Save button
    $button = new button("submit", $objLanguage->languageText("word_save", 'system', 'Save')); //word_save
    $button->setToSubmit();

    // Show the cancel link
    $buttonCancel = new button("submit", $objLanguage->languageText("word_cancel", 'system', 'Cancel'));
    $objCancel = &$this->getObject("link", "htmlelements");
    $objCancel->link($this->uri(array(
        'module' => 'podcaster',
        'action' => 'viewcategories'
    )));
    
    $objCancel->link = $buttonCancel->show();
    //$objCancel->link = $objLanguage->languageText("word_cancel", 'system', 'Cancel');
    $linkCancel = $objCancel->show();
    $row = array(
        $button->show()
    );
    $objTable->startRow();
    $objTable->addCell('&nbsp;', 140, 'top', 'right');
    $objTable->addCell($row[0]. " ".$linkCancel, Null, 'top', 'left');
    $objTable->endRow();

    $form->addToForm($objTable->show());
    echo $form->show();
}
?>
