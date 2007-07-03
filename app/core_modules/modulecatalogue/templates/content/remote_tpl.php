<?
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

$objTable = &$this->newObject('htmltable','htmlelements');
$objTable->cellpadding = 2;
$objTable->id = 'unpadded';
$objTable->width='100%';

$masterCheck = new checkbox('arrayList[]');
//$masterCheck->extra = 'onclick="javascript:baseChecked(this);"';

$head = array($masterCheck->show(),'&nbsp;',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue'));
$objTable->addHeader($head,'heading','align="left"');
$newMods = array();
$class = 'odd';

$link = new link();
$link->link = $this->objLanguage->languageText('word_download');
$icon = $this->newObject('getIcon','htmlelements');
foreach ($modules as $module) {
    if (!in_array($module,$lMods) || $module{0} == 'a') {
        $info = $this->objRPCClient->getModuleDescription($module);
        $doc = simplexml_load_string($info);
        $modName = (string)$doc->array->data->value[0]->string;
        $modDesc = (string)$doc->array->data->value[1]->string;

        $link->link('javascript:;');
        $link->extra = "onclick = 'javascript:downloadModule(\"$module\",\"$modName\");'";
        $class = ($class == 'even')? 'odd' : 'even';
        $newMods[] = $module;
        $icon->setModuleIcon($module);
        $modCheck = new checkbox('arrayList[]');
		$modCheck->cssId = 'checkbox_'.$module;
        $modCheck->setValue($module);
        //$modCheck->extra = 'onclick="javascript:toggleChecked(this);"';

        $objTable->startRow();
        $objTable->addCell($modCheck->show(),null,null,null,$class);
        $objTable->addCell($icon->show(),null,null,null,$class);
        $objTable->addCell("<div id='link_$module'><b>$modName</b></div>",'50%',null,null,$class);
        $objTable->addCell("<div id='download_$module'>".$link->show()."</div>",'20%',null,null,$class);
        $objTable->addCell('&nbsp;',null,null,'left',$class);
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell('&nbsp;',30,null,'left',$class);
		$objTable->addCell('&nbsp;',30,null,'left',$class);
		$objTable->addCell($modDesc.'<br />&nbsp;',null,null,'left',$class, 'colspan="3"');
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