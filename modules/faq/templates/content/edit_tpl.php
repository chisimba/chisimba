<?php

// Load classes.
$this->loadHTMLElement('form');
$this->loadHTMLElement('textarea');
$this->loadHTMLElement('button');
$this->loadHTMLElement('label');
$this->loadHTMLElement('htmlheading');
$this->loadHTMLElement('hiddeninput');

$header = new htmlheading();
$header->type = 1;
$header->str =$objLanguage->languageText("faq_sayitedit");

echo $header->show();

// Display form.
$form = new form('edit', $this->uri(array('action'=>'editconfirm')));

$form->setDisplayType(1);

$label = new label ($objLanguage->languageText('faq_category', 'faq'), 'input_category');
$form->addToForm("<strong>{$label->show()}:</strong>");

$dropdown = new dropdown('category');
foreach ($categories as $category) {
    $dropdown->addOption($category["id"],$category["categoryname"]);
}

$dropdown->setSelected($item["categoryid"]);

$form->addToForm($dropdown);

$label = new label ($objLanguage->languageText("word_question"), 'input_question');
$form->addToForm("<strong>" . $label->show() . ":</strong>");
$form->addToForm(new textarea("question", $item["question"], 5, 80));

$formTable = $this->newObject('htmltable', 'htmlelements');
$taglabel = new label ($this->objLanguage->languageText('mod_faq_tags', 'faq', 'Category Tag'), 'tagslabel');
$str = '';
foreach ($tags as $tag){
$str .= $tag.',';
    }
$faqTagsField = new textarea('faqtags',$str,5,80);

$formTable->startRow();
$formTable->addCell("<b>".$taglabel->show().":</b>");
$formTable->endRow();

$formTable->startRow();
$formTable->addCell($faqTagsField->show().'<br />&nbsp;');
$formTable->endRow();

$form->addToForm($formTable->show());
$label = new label ($objLanguage->languageText("word_answer"), 'input_answer');
$form->addToForm("<strong>" . $label->show() . ":</strong>");

$answer = $this->newObject('htmlarea', 'htmlelements');
$answer->name = 'answer';
$answer->value = $item['answer'];
$answer->setDefaultToolBarSetWithoutSave();
$form->addToForm($answer->show());


$button = new button("submitform", $objLanguage->languageText("word_save"));
$button->setToSubmit();

$cancelButton =new button("submit", $objLanguage->languageText("word_cancel"));
$cancelButton->setOnClick("window.location='".$this->uri(array('action'=>'view', 'category'=>$item['categoryid']))."';");

$form->addToForm($button->show().' / '.$cancelButton->show());


$hiddenInput = new hiddeninput('id', $item['id']);
$form->addToForm($hiddenInput->show());

echo $form->show();

?>