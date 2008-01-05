<?php


$this->appendArrayVar('headerParams',"<script type='text/javascript' src='core_modules/modulecatalogue/resources/remote.js'></script>");
$this->loadClass('checkbox','htmlelements');
$this->loadClass('link','htmlelements');

$objH = $this->getObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_heading','modulecatalogue');

$objH2 = $this->newObject('htmlheading','htmlelements');
$objH2->type=3;
$objH2->str = $this->objLanguage->languageText('mod_modulecatalogue_remoteheading','modulecatalogue');

$hTable = $this->getObject('htmltable','htmlelements');
$hTable->cellpadding = 2;
$hTable->id = 'unpadded';
$hTable->width='100%';
$hTable->startRow();
$hTable->addCell($objH->show());
$hTable->endRow();
$hTable->startRow();
$hTable->addCell($objH2->show());
$hTable->endRow();
$hTable->startRow();
$hTable->addCell('&nbsp;');
$hTable->endRow();

$registeredModules = $this->objModule->getModuleNames();
$localModules = $this->objModFile->getLocalModuleList();
$lMods = array_merge($registeredModules, $localModules);
$lMods = array_unique($lMods);
sort($lMods);

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->cellpadding = 2;
$objTable->id = 'unpadded';
$objTable->width='100%';

$masterCheck = new checkbox('arrayList[]');
//$masterCheck->extra = 'onclick="javascript:baseChecked(this);"';

$head = array($masterCheck->show(),'&nbsp;',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'),
$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue'));
$objTable->addHeader($head,'heading','align="left"');
$newMods = array();
$class = 'odd';

$link = new link();
$link->link = $this->objLanguage->languageText('mod_modulecatalogue_dlandinstall','modulecatalogue');
$icon = $this->newObject('getIcon','htmlelements');
foreach ($modules as $module) {
	if (!in_array($module['id'],$lMods)) {
		$link->link('javascript:;');
		$link->extra = "onclick = 'javascript:downloadModule(\"{$module['id']}\",\"{$module['name']}\");'";
		$class = ($class == 'even')? 'odd' : 'even';
		$newMods[] = $module['id'];
		$icon->setModuleIcon($module['id']);
		$modCheck = new checkbox('arrayList[]');
		$modCheck->cssId = 'checkbox_'.$module['id'];
		$modCheck->setValue($module['id']);
		//$modCheck->extra = 'onclick="javascript:toggleChecked(this);"';

		$objTable->startRow();
		$objTable->addCell($modCheck->show(),20,null,null,$class);
		$objTable->addCell($icon->show(),30,null,null,$class);
		$objTable->addCell("<div id='link_{$module['id']}'><b>{$module['name']}</b></div>",null,null,null,$class);
		$objTable->addCell("<div id='download_{$module['id']}'>".$link->show()."</div>",'40%',null,null,$class);
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell('&nbsp;',20,null,'left',$class);
		$objTable->addCell('&nbsp;',30,null,'left',$class);
		$objTable->addCell($module['desc'].'<br />&nbsp;',null,null,'left',$class, 'colspan="2"');
		$objTable->endRow();
	}
}

$objTable2 = $this->newObject('htmltable','htmlelements');
$objTable2->cellpadding = 2;
$objTable2->id = 'unpadded';
$objTable2->width='100%';

$masterCheck2 = new checkbox('arrayList[]');
//$masterCheck->extra = 'onclick="javascript:baseChecked(this);"';

$head2 = array($masterCheck2->show(),'&nbsp;',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'),
$this->objLanguage->languageText('mod_modulecatalogue_upgrade','modulecatalogue'));
$objTable2->addHeader($head2,'heading','align="left"');
$newMods2 = array();
$class2 = 'odd';

$link2 = new link();
$link2->link = $this->objLanguage->languageText('mod_modulecatalogue_upgrade','modulecatalogue');
$icon2 = $this->newObject('getIcon','htmlelements');
foreach ($modules as $umod)
{
	// check the versions too...
	// $umod are the remote mods
	foreach($registeredModules as $regmods)
	{
		if($umod['id'] == $regmods['module_id'])
		{
			// check the version floats
			$umod['ver'] = (float)$umod['ver'];
			$regmods['module_version'] = (float)$regmods['module_version'];
			if($umod['ver'] > $regmods['module_version'])
			{
				$upgradables = TRUE;
				log_debug($umod['name']." can be upgraded!");
				$link2->link('javascript:;');
				$link2->extra = "onclick = 'javascript:downloadModuleUpgrade(\"{$umod['id']}\",\"{$umod['name']}\");'";
				$class2 = ($class2 == 'even')? 'odd' : 'even';
				$newMods2[] = $umod['id'];
				$icon2->setModuleIcon($umod['id']);
				$modCheck2 = new checkbox('arrayList[]');
				$modCheck2->cssId = 'checkbox_'.$umod['id'];
				$modCheck2->setValue($umod['id']);
				//$modCheck->extra = 'onclick="javascript:toggleChecked(this);"';

				$objTable2->startRow();
				$objTable2->addCell($modCheck2->show(),20,null,null,$class2);
				$objTable2->addCell($icon2->show(),30,null,null,$class2);
				$objTable2->addCell("<div id='link_{$umod['id']}'><b>{$umod['name']}</b></div>",null,null,null,$class2);
				$objTable2->addCell("<div id='download_{$umod['id']}'>".$link2->show()."</div>",'40%',null,null,$class2);
				$objTable2->endRow();
				$objTable2->startRow();
				$objTable2->addCell('&nbsp;',20,null,'left',$class2);
				$objTable2->addCell('&nbsp;',30,null,'left',$class2);
				$objTable2->addCell($umod['desc'].'<br />&nbsp;',null,null,'left',$class2, 'colspan="2"');
				$objTable2->endRow();
			}


		}
	}
}
	if (empty($newMods)) {
		$objTable->startRow();
		$objTable->addCell("<span class='empty'>".$this->objLanguage->languageText('mod_modulecatalogue_noremotemods','modulecatalogue').'</span>',null,null,'left',null, 'colspan="4"');
		$objTable->endRow();
	}
	if ($upgradables != TRUE) {
		$objTable2->startRow();
		$objTable2->addCell("<span class='empty'>".$this->objLanguage->languageText('mod_modulecatalogue_noremoteupgrades','modulecatalogue').'</span>',null,null,'left',null, 'colspan="4"');
		$objTable2->endRow();
	}
	echo $hTable->show().$objTable2->show()."<br />".$objTable->show();

?>