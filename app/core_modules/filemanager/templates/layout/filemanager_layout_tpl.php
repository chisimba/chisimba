<?php

$this->loadClass('link', 'htmlelements');

// Create an Instance of the CSS Layout
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$objTreeFilter =& $this->getObject('dbfolder');

$leftColumn = '<h3>File Manager</h3>';

// $objFieldset =& $this->getObject('fieldset', 'htmlelements');
// $objFieldset->legend = 'Options';
// $leftColumn .= $objFieldset->show();


$leftColumn .= '<ul><li>Options</li></ul>';

if (!isset($folderId)) {
    $folderId = '';
}

$leftColumn .= $objTreeFilter->showUserFolders($folderId);

$cssLayout->setLeftColumnContent($leftColumn);

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();


?>