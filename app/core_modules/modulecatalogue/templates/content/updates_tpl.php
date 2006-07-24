<?php
$this->loadClass('link','htmlelements');

$objH = $this->newObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_updates','modulecatalogue');

$h2 = $this->newObject('htmlheading','htmlelements');
$h2->type=3;
$h2->str = $this->objLanguage->languageText('mod_modulecatalogue_newupdates','modulecatalogue').':';
$fname = $this->objModFile->findregisterfile($patch['modname']);

$updateAll = &$this->getObject('link','htmlelements');
$updateAll->link($this->uri(array('action'=>'updateall')));
$updateAll->link = $this->objLanguage->languageText('mod_modulecatalogue_updateall','modulecatalogue');

$objTable = &$this->getObject('htmltable','htmlelements');
$objTable->startRow();
$objTable->addCell($h2->show(),null,null,'left');
$objTable->addCell($updateAll->show(),null,null,'right');
$objTable->endRow();
if (!empty($patchArray)) {
	foreach ($patchArray as $patch) {
		$uri = $this->uri(array('action'=>'patch','mod'=>$patch['module_id'],'patchver'=>$patch['new_version']),'modulecatalogue');
		$link = &new Link($uri);
		$pIcon = &$this->getObject('geticon','htmlelements');
		$pIcon->setModuleIcon('update');
		$pIcon->alt = $link->link;
		$link->link = $this->objLanguage->languageText('mod_modulecatalogue_applypatch','modulecatalogue');
		$time = date("d/m/y",filemtime($this->objModFile->findRegisterFile($patch['module_id'])));
		$str .= '<b>'.ucwords($patch['module_id'])."</b> - $time - <b>Version:</b> {$patch['new_version']} {$pIcon->show()}{$link->show()}<br />
			<b>{$this->objLanguage->languageText('word_description','modulecatalogue')}:</b> {$patch['desc']}<hr />";
	}
} else {
	$str = $this->objLanguage->languageText('mod_modulecatalogue_noupdates','modulecatalogue');
}

$content = $objH->show().$objTable->show().$str;
echo $content;

?>