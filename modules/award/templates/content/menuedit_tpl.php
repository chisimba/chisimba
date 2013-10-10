<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

$objH = &$this->newObject('htmlheading','htmlelements');
$objH->type = 2;
$objH->str = $this->objLanguage->languageText('mod_lrs_menueditheading', 'award');

$objTable = &$this->newObject('htmltable','htmlelements');
$objTable->cellspacing= $objTable->cellpadding = 2;
$objTable->width ='70%';

$backLink = &$this->newObject('link','htmlelements');
$backLink->link($this->uri(array('action'=>'admin','selected'=>'init_10'),'award'));
$backLink->link = $this->objLanguage->languageText('word_back');

$icon = $this->getObject('geticon','htmlelements');
$icon->setIcon('edit');

$objTable->startHeaderRow();
$objTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_optionname', 'award'));
$objTable->addHeaderCell($this->objLanguage->languageText('word_edit'));
$objTable->endHeaderRow();

$options = $this->lrsNav->getAll("ORDER BY id");
$class = 'odd';
foreach ($options as $option) {
	$wordGo = $this->objLanguage->languageText('word_go');
	$wordCancel = $this->objLanguage->languageText('word_cancel');
	$icon->extra = " onclick='javascript:editMenuItem(\"{$option['id']}\",\"$wordGo\",\"$wordCancel\")' style='cursor:pointer'";
	$objTable->startRow($class);
	$objTable->addCell("<div id='div_{$option['id']}'>{$option['name']}</div>");
	$objTable->addCell($icon->show(),'10%');
	$objTable->endRow();
	$class = ($class == 'odd')? 'even' : 'odd';
}

$objTable->startRow();
$objTable->addCell(' ');
$objTable->addCell($backLink->show(),'10%',null,'right');
$objTable->endRow();

$content = $objH->show().$objTable->show();
$resourceUri = $this->getResourceUri('admin.js');
$this->appendArrayVar('headerParams',"<script type='text/javascript' src='$resourceUri'></script>");
echo $content;

?>