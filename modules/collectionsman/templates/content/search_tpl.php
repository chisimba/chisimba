<?php

$objForm = $this->getObject('html5form', 'html5elements');
$objTable = $this->getObject('html5table', 'html5elements');

$document = new DOMDocument();

$form = $objForm->form($document, 'GET');
$document->appendChild($form);

$form->appendChild($objForm->hidden($document, 'module', $this->moduleName));
$form->appendChild($objForm->hidden($document, 'action', 'search'));
$form->appendChild($objForm->text($document, 'q', $query, 'Enter your query', NULL, TRUE, TRUE, TRUE));
$form->appendChild($objForm->submit($document, 'Search'));

$headers = array('Title', 'Date Created');
$edit = array('action' => 'edit');
$delete = array('action' => 'delete');

$form->appendChild($objTable->table($document, NULL, $headers, $contents, $edit, $delete, $this->moduleName));

$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_collectionsman_search', 'collectionsman');
$header->type = 1;

$leftMenu = $this->newObject('usermenu', 'toolbar');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($header->show().$document->saveHTML());
echo $cssLayout->show();
