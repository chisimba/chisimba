<?php

$html5common = $this->getObject('html5common', 'html5elements');
$html5form = $this->getObject('html5form', 'html5elements');
$html5table = $this->getObject('html5table', 'html5elements');

$document = new DOMDocument('1.0');

$document->appendChild($html5common->link($document, array('action'=>'edit'), 'triplestore', $this->objLanguage->languageText('mod_triplestore_add', 'triplestore')));

$form = $html5form->form($document, 'POST', array('action'=>'search'), 'triplestore');
$document->appendChild($form);

$p = $document->createElement('p');
$form->appendChild($p);

$headers = array();
foreach (array('id', 'subject', 'predicate', 'object') as $field) {
    $header = $this->objLanguage->languageText('mod_triplestore_'.$field, 'triplestore');
    $p->appendChild($html5form->text($document, $field, $this->getParam($field), $header));
    $headers[] = $header;
}

$p->appendChild($html5form->submit($document, $this->objLanguage->languageText('mod_triplestore_filter', 'triplestore')));

if (count($this->triples) > 0) {
    $title = $this->objLanguage->languageText('mod_triplestore_triples', 'triplestore');
    $table = $html5table->table($document, $title, $headers, $this->triples, array('action'=>'edit'), array('action'=>'delete'), 'triplestore');
    $document->appendChild($table);
} else {
    $document->appendChild($html5common->paragraph($document, $this->objLanguage->languageText('mod_triplestore_empty', 'triplestore')));
}

echo $document->saveHTML();
