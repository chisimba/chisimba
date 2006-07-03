<?php
$this->loadClass('link','htmlelements');
$objH = $this->getObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_heading',modulecatalogue);
$notice = '';
//$modules = $this->objDBModCat->getModules($activeCat);
$modules = $this->objModFile->getModuleList($activeCat,$letter);
$icon = &$this->getObject('geticon', 'htmlelements');

$objTable = &$this->getObject('htmltable','htmlelements');
$objTable->cellpadding = 2;

$head = array(' ',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue','modname'),
			$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue','instal'),$this->objLanguage->languageText('mod_modulecatalogue_text','modulecatalogue','txt'),
			$this->objLanguage->languageText('mod_modulecatalogue_info','modulecatalogue','inf0'));
$objTable->addHeader($head,'heading');
$objTable->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
        
$count = 0;
foreach ($modules as $modName => $details) {
	$class = ($count % 2 == 0)? 'even' : 'odd';
	$count++;
	$ucMod = ucwords($modName);
	$icon->setModuleIcon($modName);
	$icon->alt = $mod['description'];
	if ($registered) {
		$instButton = &new Link($this->uri(array('action'=>'install','mod'=>$modName),'modulecatalogue'));
		$instButton->link = $this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue','install');
		$instButtonShow = $instButton->show();
	} else if ($details['hasRegFile']) {
		$instButton = &new Link($this->uri(array('action'=>'uninstall','mod'=>$modName),'modulecatalogue'));
		$instButton->link = $this->objLanguage->languageText('mod_modulecatalogue_uninstall','modulecatalogue','uninstall');
		$instButtonShow = $instButton->show();
	} else {
		$instButtonShow = '';
	}
	$textButton = &new Link($this->uri(array('action'=>'textelements','mod'=>$modName),'modulecatalogue'));
	$textButton->link = $this->objLanguage->languageText('mod_modulecatalogue_text','modulecatalogue','text elements');
	$infoButton = &new Link($this->uri(array('action'=>'moduleinfo','mod'=>$modName),'modulecatalogue'));
	$infoButton->link = $this->objLanguage->languageText('mod_modulecatalogue_info','modulecatalogue','module info');
	$infoButton->extra = $textButton->extra = $instButton->extra = "class=\"pseudobutton\"";
	$objTable->startRow();
	$objTable->addCell($icon->show(),null,null,'left',$class);
	$objTable->addCell("<a href='{$this->uri(null,$modName)}'>$ucMod</a>",null,null,'left',$class);
	$objTable->addCell($instButtonShow,null,null,'left',$class);
	$objTable->addCell($textButton->show(),null,null,'left',$class);
	$objTable->addCell($infoButton->show(),null,null,'left',$class);
	$objTable->endRow();
}

$msg = $this->getParam('msg');
if (isset($msg)) {
	$timeoutMsg = &$this->getObject('timeoutmessage','htmlelements');
	switch ($msg) {
		case '1': 
			$timeoutMsg->setMessage($this->objLanguage->languageText('mod_modulecatalogue_registered'));
			break;
		default:
			$timeoutMsg->setMessage($this->objLanguage->languageText('mod_modulecatalogue_uknownerror'));
			break;
	}
	$notice = $timeoutMsg->show();
}

$content = $objH->show().$notice.$objTable->show();
echo $content;
?>