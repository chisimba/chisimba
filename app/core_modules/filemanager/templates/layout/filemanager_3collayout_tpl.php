<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$searchForm = new form('filesearch', $this->uri(array('action'=>'search')));
$searchForm->method = 'GET';
$hiddenInput = new hiddeninput('module', 'filemanager');
$searchForm->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('action', 'search');
$searchForm->addToForm($hiddenInput->show());

$textinput = new textinput('filequery', $this->getParam('filequery'));
$searchForm->addToForm($textinput->show());

$button = new button ('search', $this->objLanguage->languageText('word_search', 'system', 'Search'));
$button->setToSubmit();
$searchForm->addToForm($button->show());





$objFolders = $this->getObject('dbfolder');

$header = new htmlheading();
$header->type = 3;
$header->str = $this->objLanguage->languageText('mod_filemanager_name', 'filemanager', 'File Manager');

$leftColumn = $header->show();

$tagCloudLink = new link ($this->uri(array('action'=>'tagcloud')));
$tagCloudLink->link = 'Tag Cloud';

$leftColumn .= $searchForm->show().'<ul><li>'.$tagCloudLink->show().'</li></ul>';

if (!isset($folderId)) {
    $folderId = '';
}

//$leftColumn .= $objFolders->showUserFolders($folderId);
$leftColumn .= $objFolders->getTree('users', $this->objUser->userId(), 'dhtml', $folderId);

$leftColumn .= '<br /><br /><br />';

if ($this->contextCode != '' && $this->getParam('context') != 'no') {
    $leftColumn .= $objFolders->getTree('context', $this->contextCode, 'dhtml', $folderId);
}



// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent($leftColumn);
$cssLayout->numColumns = 3;


// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setRightColumnContent($rightColumn);

// Display the Layout
echo $cssLayout->show();


?>