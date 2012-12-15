<?php


// Create and output page heading.
$headingObj = $this->newObject('htmlheading', 'htmlelements');
$headingObj->htmlheading($heading, 1);
$content="";
$content.= $headingObj->show();

// Create and output message.
$content.=  '<p>' . htmlspecialchars($messageText) . '.</p>';
$backButton = new button('back', $this->objLanguage->languageText('mod_filemanager_backtopreviouspage', 'filemanager'));
$backButton->setToSubmit();
$form = new form('accessform', $this->uri(array('action' => $action,$id=>$idvalue)));
$form->addToForm($content.'<br/>');
$form->addToForm($backButton->show());
echo $form->show();
?>
