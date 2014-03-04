<?php
$this->setSession("canvas","rtt");

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$form = new form('demo', $this->uri(array('action' => "joindemo")));
$button = new button('join', $this->objLanguage->languageText('mod_rtt_try', 'rtt', 'Try'));
$button->setToSubmit();



$label = new label($this->objLanguage->languageText('mod_rtt_nickname', 'rtt', 'Enter your nickname'), 'input_name');
$textinput = new textinput('name');


$form->addToForm($label->show().'&nbsp;');
$form->addToForm($textinput->show());
$form->addToForm($button->show());

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);
$title=$this->objLanguage->languageText('mod_rtt_demotitle','rtt','Chisimba Realtime Tools Demo. Try it now!');
$heading = new htmlHeading();
$heading->type = 1;
$heading->str = $title;

$cssLayout->setLeftColumnContent($this->objDbRtt->getDownloadsStory());

// Add Right Column
$cssLayout->setMiddleColumnContent( $heading->show().$form->show().$this->objDbRtt->getDemoContent());

//Output the content to the page
echo $cssLayout->show();


?>
