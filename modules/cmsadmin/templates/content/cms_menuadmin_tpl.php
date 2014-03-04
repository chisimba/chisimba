<?php
$treeMenu =  $this->newObject('buildtree', 'cmsadmin');
 $this->appendArrayVar('headerParams', $this->getJavascriptFile('tree.js', 'cms'));
$css = '<link rel="stylesheet" type="text/css" media="all" href="'.$this->getResourceURI("tree.css", 'cmsadmin').'" />';

$this->appendArrayVar('headerParams', $css);
//Set to automatically render htmllist into tree menu
$this->appendArrayVar('bodyOnLoad', 'autoInit_trees()');

$logoFooter = '';
//Insert script for generating tree menu
//$js = $this->getJavascriptFile('xpmenuv21.js', 'publicportal');
//$this->appendArrayVar('headerParams', $js);
$caljs = $this->getJavascriptFile('a.js', 'cmsadmin');
//Include tree menu css script
//$css = '<link rel="stylesheet" type="text/css" media="all" href="skins/uwcportal2/portallink.css" />';
//$this->appendArrayVar('headerParams', $css);

$treeLink =  $this->uri(array('action' => 'addnewmenu', 'pageid'=>'[-ID-]'), 'cmsadmin');

 if (!isset($editForm)) {
    $editForm = '';
}
if (!isset($pageId)) {
	$pageId = null;
}
if (!isset($content)) {
    $content = '';
} else {
    $content .= '<p />';
}
$link = & $this->newObject('link', 'htmlelements');

$link->link = 'Add new menu';
$link->href = $this->uri(array('action' => 'addnewmenu', 'pageid' => '0', 'add' => 'true'), 'cmsadmin');
$editMenu = $link->show();

if (isset($menuNodeParent)) {
    $link->link = 'Add new menu item';
    $link->href = $this->uri(array('action' => 'addnewmenu', 'pageid' => $menuNodeParent, 'add' => 'true'), 'cmsadmin');
    $editMenu .= " | ". $link->show();
}
$link->link = 'Add new menu item child';
$link->href = $this->uri(array('action' => 'addnewmenu', 'pageid' => $pageId, 'add' => 'true'), 'cmsadmin');
$editMenu .= " | ". $link->show();


$link->link = 'Move up';
$link->href = $this->uri(array('action' => 'moveup', 'pageid' => $pageId), 'cmsadmin');
$editMenu .= " | ". $link->show();

$link->link = 'Move down';
$link->href = $this->uri(array('action' => 'movedown', 'pageid' => $pageId), 'cmsadmin');
$editMenu .= " | ". $link->show();

$deleteLink = '<a href="'.$this->uri(array('action' => 'deletemenu', 'pageid' => $pageId), 'cmsadmin').'" onclick=" if (confirm(\'Are you sure you want to delete this menu item?\') == \'0\') {return false;}">Delete</a>';
$editMenu .= " | ". $deleteLink;


$mainContent = $editMenu.'<br />'.$editForm.'<br />'.$content;



$contentTable=$this->newObject('htmltable','htmlelements');

$contentTable->width='100%';
$contentTable->border='0';
$contentTable->cellspacing='0';
$contentTable->cellpadding='30';

$contentTable->startRow();
$contentTable->addCell('&nbsp;','','','left','','bgcolor="#FFFFFF"');
$contentTable->addCell($treeMenu->show($this->currentPageId, '0', 'standardtree', null, null, 999999, $treeLink, FALSE, FALSE),'270','top','left','','bgcolor="#FFFFFF"');
$contentTable->addCell($mainContent,'500','top','left','','bgcolor="#FFFFFF"');
$contentTable->addCell('&nbsp;','','','left','','bgcolor="#FFFFFF"');
$contentTable->endRow();

echo $contentTable->show();


?>
