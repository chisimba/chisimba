<?php
$this->loadClass('link','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');

$objH = $this->newObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_updates','modulecatalogue');

$h2 = $this->newObject('htmlheading','htmlelements');
$h2->type=3;
$h2->str = $this->objLanguage->languageText('mod_modulecatalogue_newupdates','modulecatalogue').':';
//$fname = $this->objModFile->findregisterfile($patch['modname']);

$updateAll = &$this->getObject('link','htmlelements');
$updateAll->link($this->uri(array('action'=>'makepatch')));
$updateAll->link = $this->objLanguage->languageText('mod_modulecatalogue_makepatch','modulecatalogue');
$makePatch = $updateAll->show();
$updateAll->link($this->uri(array('action'=>'xml')));
$date = date("d/m/y",filemtime($this->objConfig->getsiteRootPath().'/modules/modulecatalogue/resources/catalogue.xml'));
$updateAll->link = str_replace('[DATE]',$date,$this->objLanguage->languageText('mod_modulecatalogue_updatexml','modulecatalogue'));
$updateCat = $updateAll->show();
$updateAll->link($this->uri(array('action'=>'updateall')));
$updateAll->link = $this->objLanguage->languageText('mod_modulecatalogue_updateall','modulecatalogue');

$objTable = &$this->getObject('htmltable','htmlelements');
$objTable->startRow();
$objTable->addCell($h2->show(),null,null,'left');
$objTable->addCell($updateAll->show()."<br/>$updateCat",null,null,'right');//<br/>$makePatch
$objTable->endRow();
$tString = '';
if (isset($output)) {
	$msg = &$this->getObject('timeoutmessage','htmlelements');
	$msg->message = '';
	foreach($output as $mod) {
		foreach ($mod as $key => $value) {
			switch ($key) {
				case 'current':
					$ver = $value;
					break;
				case 'old':
					$old = $value;
					break;
				case 'modname':
					$module = $value;
					break;
					//default:
					//$tempver = (float)str_replace('_','.',$key);
					//$str .= "<b>{$this->objLanguage->languageText('mod_modulecatalogue_version','modulecatalogue')} $tempver</b><br/>$value<br/>";
			}

		}

		$success = str_replace('[OLDVER]',"<b>$old</b>",$this->objLanguage->languageText('mod_modulecatalogue_updatesuccess','modulecatalogue'));
		$success = str_replace('[NEWVER]',"<b>$ver</b>",$success);
		$msg->message .= "<b>$module</b> $success<br/>";
	}
	$tString = $msg->show();
}

$updateAll->link($this->uri(array('action'=>'patchall')));
$updateAll->link = $this->objLanguage->languageText('mod_modulecatalogue_patchall','modulecatalogue');
$objT2 = &$this->newObject('htmltable','htmlelements');
$objT2->startRow();
$objT2->addCell($updateAll->show(),null,null,'right');
$objT2->endRow();

$str = '';
if (!empty($patchArray)) {
	foreach ($patchArray as $patch) {
		$uri = $this->uri(array('action'=>'update','mod'=>$patch['module_id'],'patchver'=>$patch['new_version']),'modulecatalogue');
		$link = &new Link($uri);
		$pIcon = &$this->getObject('geticon','htmlelements');
		$pIcon->setModuleIcon($patch['module_id']);
		$modIcon=$pIcon->show();
		$pIcon->setModuleIcon('update');
		$pIcon->alt = $link->link;
		$link->link = $this->objLanguage->languageText('mod_modulecatalogue_applypatch','modulecatalogue');
		$time = date("d/m/y",filemtime($this->objModFile->findRegisterFile($patch['module_id'])));
		$str .= '<b>'.$modIcon.ucwords($patch['module_id'])." version:</b> {$patch['new_version']} - $time {$pIcon->show()}{$link->show()}<br />
			<b>{$this->objLanguage->languageText('mod_modulecatalogue_description','modulecatalogue')}:</b> {$patch['desc']}<hr />";
	}
	$patchAll = $objT2->show();
} else {
	$str = $this->objLanguage->languageText('mod_modulecatalogue_noupdates','modulecatalogue');
	$patchAll = '';
}

$searchForm = &new form('searchform',$this->uri(array('action'=>'search','cat'=>'all'),'modulecatalogue'));
$searchForm->displayType = 3;
$srchStr = &new textinput('srchstr',$this->getParam('srchstr'),null,'21');
$srchButton = &new button('search');
$srchButton->setValue($this->objLanguage->languageText('word_search'));
$srchButton->setToSubmit();
$srchType = &new dropdown('srchtype');
$srchType->addOption('name',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'));
$srchType->addOption('description',$this->objLanguage->languageText('mod_modulecatalogue_description','modulecatalogue'));
$srchType->addOption('both',$this->objLanguage->languageText('word_all'));
$srchType->setSelected($this->getParam('srchtype'));
$srch = $srchType->show().$srchButton->show();

$hTable = $this->newObject('htmltable','htmlelements');
$hTable->startRow();
$hTable->addCell(null,null,null,'left');
$hTable->addCell($srchStr->show(),null,'bottom','right');
$hTable->endRow();
$hTable->startRow();
$hTable->addCell($objH->show(),null,null,'left');
$hTable->addCell($srch,null,'top','right');
$hTable->endRow();
$searchForm->addToForm($hTable->show());

$content = $searchForm->show().$tString.$objTable->show().$str.$patchAll;
echo $content;

?>