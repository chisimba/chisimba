<?php

$this->loadClass('link', 'htmlelements');

// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);
$objFolders = $this->getObject('dbfolder');
$objQuotas = $this->getObject('dbquotas');

$leftColumn = '<h3>File Manager</h3>';

$indexLink = new link ($this->uri(array('action'=>'indexfiles')));
$indexLink->link = 'Index Files';

$tagCloudLink = new link ($this->uri(array('action'=>'tagcloud')));
$tagCloudLink->link = 'Tag Cloud';

$leftColumn .= '<ul><li>Search</li><li>'.$tagCloudLink->show().'</li><li>'.$indexLink->show().'</li></ul>';

if (!isset($folderId)) {
    $folderId = '';
}

//$leftColumn .= $objTreeFilter->showUserFolders($folderId);
$leftColumn .= '<div class="filemanagertree">'.$objFolders->getTree('users', $this->objUser->userId(), 'dhtml', $folderId).$objQuotas->getQuotaGraph('users/'.$this->objUser->userId()).'</div>';

$leftColumn .= '<br /><br /><br />';

//$cssLayout->setLeftColumnContent($leftColumn);

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();


?>
