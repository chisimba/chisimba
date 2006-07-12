<?php
$this->loadClass('link','htmlelements');
$objCheck=&$this->newObject('checkbox','htmlelements');
$objH = $this->getObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_heading',modulecatalogue);
$notice = '';
$modules = $this->objCatalogueConfig->getModuleList($activeCat);//,$letter);
$icon = &$this->getObject('geticon', 'htmlelements');

$objTable = &$this->getObject('htmltable','htmlelements');
$objTable->cellpadding = 2;

$head = array(' ',' ',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'),$this->objLanguage->languageText('mod_modulecatalogue_hasregfile','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_runnable','modulecatalogue'),$this->objLanguage->languageText('mod_modulecatalogue_isreg','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue'),$this->objLanguage->languageText('mod_modulecatalogue_text','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue'));
        
$count = 0;
$localModules = $this->objModFile->getLocalModuleList();
if ($modules) {
	$objTable->addHeader($head,'heading','align="left"');
	$objTable->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
	foreach ($modules as $modName ) {
		if (in_array($modName,$localModules)){//dont display downloadable modules until that functionality is complete
		$isRegistered = $hasController = $hasRegFile = '';
		$textButton = &new Link($this->uri(array('action'=>'textelements','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
		$textButton->link = $this->objLanguage->languageText('mod_modulecatalogue_text','modulecatalogue');
		$infoButton = &new Link($this->uri(array('action'=>'info','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
		$infoButton->link = $this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue');
		$infoButton->extra = $textButton->extra = $instButton->extra = "class=\"pseudobutton\"";
		$class = ($count % 2 == 0)? 'even' : 'odd';
		$count++;
		$ucMod = ucwords($modName);
		$link = $ucMod;
		$icon->setModuleIcon($modName);
		$icon->alt = $mod['description'];
		$objCheck->checkbox('arrayList[]');
        $objCheck->setValue($modName);
        if ($this->objModFile->findController($modName)) {
        	$icon->setIcon('ok','png');
        } else {
        	$icon->setIcon('failed','png');
        }
        $hasController = $icon->show();
		if (!in_array($modName,$localModules)){ //module available on server but not locally
			$instButton = &new Link($this->uri(array('action'=>'download','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
			$instButton->link = $this->objLanguage->languageText('word_download','modulecatalogue');
			$instButtonShow = $instButton->show();
			$checkBox='';
			$texts = '';
			$info = '';//$infoButton->show(); only add this once back end functionality is complete
		} else { //local module
			if ($this->objModFile->findRegisterFile($modName)) {//has regfile
				$texts = $textButton->show();
				$info = $infoButton->show();
				$icon->setIcon('ok','png');
				$hasRegFile = $icon->show();
				if (!$this->objModule->checkIfRegistered($modName, $modName)) { //not registered
					$instButton = &new Link($this->uri(array('action'=>'install','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
					$instButton->link = $this->objLanguage->languageText('word_install','modulecatalogue');
					$instButtonShow = $instButton->show();
					$checkBox=$objCheck->show();
					$icon->setIcon('failed','png');
					$isRegistered = $icon->show();     
				} else {//registered
					if ($this->objModFile->findController($modName)) {
						$link = "<a href='{$this->uri(null,$modName)}'>$ucMod</a>";
					}
					$instButton = &new Link($this->uri(array('action'=>'uninstall','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
					$instButton->link = $this->objLanguage->languageText('word_uninstall','modulecatalogue');
					$instButtonShow = $instButton->show();
					$checkBox='';
					$icon->setIcon('ok','png');
					$isRegistered = $icon->show();
				}
			} else { //no regfile
				$texts = '';
				$info = '';
				$instButtonShow = '';
				$checkBox='';
				$icon->setIcon('failed','png');
				$hasRegFile = $icon->show();
				
			}
		}
		$icon->setModuleIcon($modName);
		$icon->alt = $mod['description'];
		$objTable->startRow();
		$objTable->addCell($checkBox,null,null,'left',$class);
		$objTable->addCell($icon->show(),null,null,'left',$class);
		$objTable->addCell($link,null,null,'left',$class);
		$objTable->addCell($hasRegFile,null,null,'left',$class);
		$objTable->addCell($hasController,null,null,'left',$class);
		$objTable->addCell($isRegistered,null,null,'left',$class);
		$objTable->addCell($instButtonShow,null,null,'left',$class);
		$objTable->addCell($texts,null,null,'left',$class);
		$objTable->addCell($info,null,null,'left',$class);
		$objTable->endRow();
		}//temporary if
	}
} else {
	$objTable->startRow();
	$objTable->addCell('<span class="empty">'.$this->objLanguage->languageText('mod_modulecatalogue_noitems','modulecatalogue').'</span>');
	$objTable->endRow();
}
if (($output=$this->getSession('output'))!=null) {
	$timeoutMsg = &$this->getObject('timeoutmessage','htmlelements');
	$timeoutMsg->setMessage($output);
	$notice = $timeoutMsg->show();
	$this->unsetSession('output');
}

$content = $objH->show().$notice.$objTable->show();
echo $content;
?>