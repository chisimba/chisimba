<?php

$this->loadClass('link', 'htmlelements');

// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objTreeFilter = $this->getObject('dbfolder');

$leftColumn = '<h3>File Manager</h3>';

$indexLink = new link ($this->uri(array('action'=>'indexfiles')));
$indexLink->link = 'Index Files';

$tagCloudLink = new link ($this->uri(array('action'=>'tagcloud')));
$tagCloudLink->link = 'Tag Cloud';

$leftColumn .= '<ul><li>Search</li><li>'.$tagCloudLink->show().'</li><li>'.$indexLink->show().'</li></ul>';

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