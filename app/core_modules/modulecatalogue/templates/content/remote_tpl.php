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

// system type header
$objH3 = $this->newObject('htmlheading','htmlelements');
$objH3->type=3;
$objH3->str = $this->objLanguage->languageText('mod_modulecatalogue_systypeheading','modulecatalogue')." ".$this->objConfig->getSystemType();

// upgrades header
$objH4 = $this->newObject('htmlheading','htmlelements');
$objH4->type=3;
$objH4->str = $this->objLanguage->languageText('mod_modulecatalogue_availableupgrades','modulecatalogue');

// new modules header
$objH5 = $this->newObject('htmlheading','htmlelements');
$objH5->type=3;
$objH5->str = $this->objLanguage->languageText('mod_modulecatalogue_newmodules','modulecatalogue');


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
$objTable->id = 'unpadded1';
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
$objTable2->id = 'unpadded2';
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

// system types one click install
$objTable3 = $this->newObject('htmltable','htmlelements');
$objTable3->cellpadding = 2;
$objTable3->id = 'unpadded3';
$objTable3->width='100%';

$masterCheck3 = new checkbox('arrayList[]');
//$masterCheck->extra = 'onclick="javascript:baseChecked(this);"';

$head3 = array($masterCheck3->show(),'&nbsp;',$this->objLanguage->languageText('mod_modulecatalogue_sysname','modulecatalogue'),
$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue'));
$objTable3->addHeader($head3,'heading','align="left"');
$newMods3 = array();
$class3 = 'odd';

$link3 = new link();
$link3->link = $this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue');
$icon3 = $this->newObject('getIcon','htmlelements');

$iconcheck = $this->newObject('getIcon', 'htmlelements');
$iconcheck->setIcon('greentick');
// get the system types...
$types = array();
$types = $this->objCatalogueConfig->getCategories();
// add in the current type
$objTable3->startRow();
$objTable3->addCell(''); //$modCheck3->show(),20,null,null,$class3);
$objTable3->addCell($icon3->show(),30,null,null,$class3);
$objTable3->addCell("<div id='link_basicsysonly'><b>Basic System Only</b></div>",null,null,null,$class3);
$objTable3->addCell($iconcheck->show()); //"<div id='download_{$type['id']}'>".$link3->show()."</div>",'40%',null,null,$class3);
$objTable3->endRow();

foreach ($types as $type)
{
	// grab a list of all modules in the category for the check.
	$catmods[$type] = array_keys($this->objCatalogueConfig->getCategoryList($type));
	// loop through the modules in the cat to check if they are all installed.
	foreach($catmods[$type] as $checks)
	{
		//echo $checks;
		$check[] = $this->objModule->checkIfRegistered($checks);
	}
	// check for a false in the array and if there is one, download else show tick
	if(in_array(false, $check))
	{
		$link3->link($this->uri(array('action' => 'downloadsystemtype', 'type' => $type, ))); //'javascript:;');
		//$link3->extra = "onclick = 'javascript:alert(\"{$type}\");'";
		$class3 = ($class3 == 'even')? 'odd' : 'even';
		$newMods3[] = $type;
		$itype = str_replace(' ','_', $type);
		$itype = strtolower($itype);
		$icon3->setModuleIcon($itype);
		$modCheck3 = new checkbox('arrayList[]');
		$modCheck3->cssId = 'checkbox_'.$itype;
		$modCheck3->setValue($type);
				
		$objTable3->startRow();
		$objTable3->addCell('&nbsp;',20,null,null,$class3);
		$objTable3->addCell($icon3->show(),30,null,null,$class3);
		$objTable3->addCell("<div id='link_{$itype}'><b>{$type}</b></div>",null,null,null,$class3);
		$objTable3->addCell("<div id='download_{$itype}'>".$link3->show()."</div>",'40%',null,null,$class3);
		$objTable3->endRow();
		$objTable3->startRow();
		$objTable3->addCell('&nbsp;',20,null,'left',$class3);
		$objTable3->addCell('&nbsp;',30,null,'left',$class3);
		$objTable3->addCell('&nbsp;',30,null,'left',$class3);
		$objTable3->addCell('&nbsp;',30,null,'left',$class3); //$type.'<br />&nbsp;',null,null,'left',$class3, 'colspan="2"');
		$objTable3->endRow();
	}
	else {
		// no falses in the array which means that all the relevant modules are installed already
		$link3->link($this->uri(array('action' => 'downloadsystemtype', 'type' => $type, ))); //'javascript:;');
		//$link3->extra = "onclick = 'javascript:alert(\"{$type}\");'";
		$class3 = ($class3 == 'even')? 'odd' : 'even';
		$newMods3[] = $type;
		$itype = str_replace(' ','_', $type);
		$itype = strtolower($itype);
		$icon3->setModuleIcon($itype);
		$modCheck3 = new checkbox('arrayList[]');
		$modCheck3->cssId = 'checkbox_'.$itype;
		$modCheck3->setValue($type);
				
		$objTable3->startRow();
		$objTable3->addCell('&nbsp;',20,null,null,$class3);
		$objTable3->addCell($icon3->show(),30,null,null,$class3);
		$objTable3->addCell("<div id='link_{$itype}'><b>{$type}</b></div>",null,null,null,$class3);
		$objTable3->addCell("<div id='download_{$itype}'>".$iconcheck->show()."</div>",'40%',null,null,$class3);
		$objTable3->endRow();
		$objTable3->startRow();
		$objTable3->addCell('&nbsp;',20,null,'left',$class3);
		$objTable3->addCell('&nbsp;',30,null,'left',$class3);
		$objTable3->addCell('&nbsp;',30,null,'left',$class3);
		$objTable3->addCell('&nbsp;',30,null,'left',$class3); //$type.'<br />&nbsp;',null,null,'left',$class3, 'colspan="2"');
		$objTable3->endRow();
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
if (empty($types)) {
	$objTable3->startRow();
	$objTable3->addCell("<span class='empty'>".$this->objLanguage->languageText('mod_modulecatalogue_nosystypesavail','modulecatalogue').'</span>',null,null,'left',null, 'colspan="4"');
	$objTable->endRow();
}
echo $hTable->show()."<br />".$objH4->show().$objTable2->show()."<br />".$objH3->show().$objTable3->show()."<br />".$objH5->show().$objTable->show();

?>