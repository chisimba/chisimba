<?php

// Load the classes.
$this->loadClass("form","htmlelements");
$this->loadClass("textinput","htmlelements");
$this->loadClass("button","htmlelements");
$this->loadClass("htmlheading","htmlelements");


// Show the heading.
$objHeading = new htmlheading();
$objHeading->type=1;
$objHeading->str =$objLanguage->languageText("mod_faq_editcategory","faq").' : <em>'.$list['categoryname'].'</em>';
echo $objHeading->show();

// Create the form.
$form = new form("createcategory", 
$this->uri(array('action'=>'editcategoryconfirm', 'id'=>$list['id'])));
$form->setDisplayType(1);

$textInput = new textinput("category",$list['categoryname']);
$textInput->size = 40;

$form->addToForm($textInput->show());
$form->addToForm("&nbsp;");
$button = new button("submit", $objLanguage->languageText("word_save"));
$button->setToSubmit();

$cancelButton = new button("submit", $objLanguage->languageText('word_cancel'));
$cancelButton->setOnClick("window.location='".$this->uri(NULL)."';");

$form->addToForm($button->show().' / '.$cancelButton->show());
// Show the form.
echo $form->show();
?>