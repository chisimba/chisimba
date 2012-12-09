<?php

// Get language strings.
$headingText = $this->objLanguage->languageText('mod_filemanager_nosecurefolder_heading', 'filemanager');

// Create and output page heading.
$heading = $this->newObject('htmlheading', 'htmlelements');
$heading->htmlheading($headingText, 1);
$content="";
$content.= $heading->show();

// Create and output message.
$content.=  '<p>' . htmlspecialchars($messageText) . '.</p>';
$backButton = new button('back', $this->objLanguage->languageText('mod_filemanager_backtopreviouspage', 'filemanager'));
$backButton->setToSubmit();
$form = new form('accessform', $this->uri(array('action' => $action,$id=>$idvalue)));
$form->addToForm($content.'<br/>');
$form->addToForm($backButton->show());
echo $form->show();
?>
