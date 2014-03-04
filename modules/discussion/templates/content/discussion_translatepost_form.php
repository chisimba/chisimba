<?php
//Sending display to 1 column layout
ob_start();

//POP languages from array that have already been translated.

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');


$header = new htmlheading();
$header->type=1;
$header->str=$this->objLanguage->languageText('mod_discussion_translateposttitle', 'discussion');
echo $header->show();


echo $postDisplay;

$translateForm = new form('translatePostForm', $this->uri( array('action'=>'savetranslation', 'type'=>$discussiontype)));
$translateForm->displayType = 3;

$formTable = $this->getObject('htmltable', 'htmlelements');
$formTable->width='99%';
$formTable->align='center';
$formTable->cellpadding = 10;

$formTable->startRow();

    $languageLabel = new label($this->objLanguage->languageText('word_language').':', 'input_language');
    $formTable->addCell($languageLabel->show(), 100);
    
    $languageList = $this->newObject('dropdown', 'htmlelements');
    $languageList->name = 'language';
    $languageCodes = & $this->newObject('languagecode','language');
    
    // Sort Associative Array by Language, not ISO Code
    asort($languageCodes->iso_639_2_tags->codes);
    
    // Remove existing languages that have already been translated
    foreach ($postLanguages AS $postLanguage)
    {
        unset ($languageCodes->iso_639_2_tags->codes[$postLanguage['language']]); 
    }
    
    foreach ($languageCodes->iso_639_2_tags->codes as $key => $value) {
        $languageList->addOption($key, $value);
    }
    $languageList->setSelected($languageCodes->getISO($this->objLanguage->currentLanguage()));
    $formTable->addCell($languageList->show());

$formTable->endRow();

$formTable->startRow();
    $subjectLabel = new label($this->objLanguage->languageText('word_subject').':', 'input_title');
    $formTable->addCell($subjectLabel->show(), 100);
    
    $titleInput = new textinput('title');
    $titleInput->size = 50;
    
    //$titleInput->value = $defaultTitle;
    
    $formTable->addCell($titleInput->show());

$formTable->endRow();

$formTable->startRow();

$formTable->addCell($this->objLanguage->languageText('word_message').':', 140);

$editor=&$this->newObject('htmlarea','htmlelements');
$editor->setName('message');
$editor->setContent('');
$editor->setRows(20);
$editor->setColumns('100');

$formTable->addCell($editor->show());

$formTable->endRow();

$formTable->startRow();

$formTable->addCell(' ');

$submitButton = new button('submitform', $this->objLanguage->languageText('word_submit'));
$submitButton->cssClass = 'save';
$submitButton->setToSubmit();

$cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$cancelButton->cssClass = 'cancel';
$returnUrl = $this->uri(array('action'=>'thread', 'id'=>$post['topic_id'], 'type'=>$discussiontype));
$cancelButton->setOnClick("window.location='$returnUrl'");

$formTable->addCell($submitButton->show().' / '.$cancelButton->show());

$formTable->endRow();

$translateForm->addToForm($formTable);

$hiddenTangentInput = new textinput('post');
$hiddenTangentInput->fldType = 'hidden';
$hiddenTangentInput->value = $post['post_id'];
$translateForm->addToForm($hiddenTangentInput->show());

echo $translateForm->show();

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>