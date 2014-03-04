<?php
$ret = "";
// Load classes.
$this->loadHTMLElement('form');
$this->loadHTMLElement('textarea');
$this->loadHTMLElement('button');
$this->loadHTMLElement('label');
$this->loadHTMLElement('htmlheading');
$this->loadHTMLElement('hiddeninput');

$header = new htmlheading();
$header->type = 1;
$header->str =$objLanguage->languageText("faq_sayitadd");

$ret .= $header->show();

// Display form.
$form = new form("add",
    $this->uri(array(
        'module'=>'faq',
        'action'=>'addconfirm'
)));
$form->setDisplayType(1);

$label = new label ($objLanguage->languageText("faq_category","faq"), 'input_category');
$form->addToForm("<b>" . $label->show() . ":</b>");

$dropdown = new dropdown('category');
foreach ($categories as $item) {
    $dropdown->addOption($item["id"],$item["categoryname"]);
}

$dropdown->setSelected($categoryId);
$form->addToForm($dropdown);

$label = new label ($objLanguage->languageText("mod_faq_question",'faq','Question'), 'input_question');
$form->addToForm("<b>" . $label->show() . ":</b>");
$form->addToForm(new textarea("question", NULL, 5, 80));

$formTable = $this->newObject('htmltable', 'htmlelements');
$taglabel = new label ($this->objLanguage->languageText('mod_faq_tags', 'faq', 'Category Tag'), 'tagslabel');
$faqTags = new textarea('faqtags',NULL,5,80);

$formTable->startRow();
$formTable->addCell("<b>".$taglabel->show().":</b>");
$formTable->endRow();

$formTable->startRow();
$formTable->addCell($faqTags->show().'<br />&nbsp;');
$formTable->endRow();

$form->addToForm($formTable->show());

$label = new label ($objLanguage->languageText("word_answer"), 'input_answer');
$form->addToForm("<b>" . $label->show() . ":</b>");
$answer = $this->newObject('htmlarea', 'htmlelements');
$answer->name = 'answer';
$form->addToForm($answer->show());



$button = new button("submit", $objLanguage->languageText("word_add"));
$button->setToSubmit();

$cancelButton =new button("submitform", $objLanguage->languageText("word_cancel"));
$cancelButton->setOnClick("window.location='".$this->uri(array('action'=>'view', 'category'=>$categoryId))."';");

$form->addToForm($button->show().' / '.$cancelButton->show());

$ret .= $form->show();

echo "<div class='faq_main'>$ret</div>"
?>