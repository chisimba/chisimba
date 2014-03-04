<?php
// Show the heading.
$objHeading = $this->newObject('htmlheading','htmlelements');

$objHeading->type=1;
$objHeading->str =$objLanguage->languageText("mod_faq_addcategory","faq");
echo $objHeading->show();
// Load the classes.
$this->loadClass("form","htmlelements");
$this->loadClass("textinput","htmlelements");
$this->loadClass("button","htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('label', 'htmlelements');
    // Create the form.
$form = new form("createcategory", $this->uri(array('action'=>'addcategoryconfirm')));
$formTable = $this->newObject('htmltable', 'htmlelements');

$textInput = new textinput("category", NULL);
$textInput->size = 40;

$taglabel = new label ($this->objLanguage->languageText('mod_faq_categorytags', 'faq', 'Category Tag'), 'tagslabel');
$catlabel = new label ($this->objLanguage->languageText('mod_faq_addcategory', 'faq', 'Add Category'), 'catlabel');
$faqTags = new textarea('faqtags');

$formTable->startRow();
$formTable->addCell($catlabel->show());
$formTable->addCell($textInput->show().'<br />&nbsp;');
$formTable->endRow();

$formTable->startRow();
$formTable->addCell($taglabel->show());
$formTable->addCell($faqTags->show().'<br />&nbsp;');
$formTable->endRow();


$form->setDisplayType(1);
$form->addToForm($formTable->show());
$form->addToForm("&nbsp;");
$button = new button("submit", $objLanguage->languageText("word_save"));
$button->setToSubmit();

$cancelButton = new button("submit", $objLanguage->languageText('word_cancel'));
$cancelButton->setOnClick("window.location='".$this->uri(NULL)."';");


$form->addToForm($button->show().' / '.$cancelButton->show());
//$form->addRule('category', 'Please enter the name of the category', 'required');
// Show the form.
echo $form->show();

?>