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
if (empty($newMods)) {
    $objTable->startRow();
    $objTable->addCell("<span class='empty'>".$this->objLanguage->languageText('mod_modulecatalogue_noremotemods','modulecatalogue').'</span>',null,null,'left',null, 'colspan="4"');
    $objTable->endRow();
}
echo $hTable->show().$objTable->show();

?>