<?php
$this->loadClass('link','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');

$objCheck=&$this->newObject('checkbox','htmlelements');
$objH = $this->getObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_heading','modulecatalogue');
$notice = $top = $bot = '';
if (!isset($result)) {
	$modules = $this->objCatalogueConfig->getModuleList($activeCat);//,$letter);
} else {
	$modules = $result; //search results
}

$icon = &$this->getObject('geticon', 'htmlelements');

$objTable = &$this->getObject('htmltable','htmlelements');
$objTable->cellpadding = 2;
$objTable->cellspacing = 3;
$objTable->width='100%';
/*$icon->setIcon('installable','jpg');
$icon->alt = $this->objLanguage->languageText('mod_modulecatalogue_hasregfile','modulecatalogue');
$instbl = $icon->show();
$icon->setIcon('installed','jpg');
$icon->alt = $this->objLanguage->languageText('mod_modulecatalogue_isreg','modulecatalogue');
$instld = $icon->show();
$icon->setIcon('run','jpg');
$icon->alt = $this->objLanguage->languageText('mod_modulecatalogue_runnable','modulecatalogue');
$rnnbl = $icon->show();*/

$head = array('&nbsp;','&nbsp;',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_description','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_textelement','modulecatalogue')
			,$this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue'));

$count = 0;
$localModules = $this->objModFile->getLocalModuleList();
$actiontotake = 'batchinstall';
$root = $this->objConfig->getsiteRootPath();
$defaults = file_get_contents($root.'installer/dbhandlers/default_modules.txt');
$registeredModules = $this->objModule->getAll();
foreach ($registeredModules as $module) {
	$rMods[]=$module['module_id'];	
}

if ($modules) {
	natsort($modules);
	(($count % 2) == 0)? $oddOrEven = 'even' : $oddOrEven = 'odd';
	$objTable->addHeader($head,'heading','align="left"');
	$objTable->row_attributes=" onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='".$oddOrEven."'; \"";
	$batchuninstall = $this->getParm('uninstall');
	if ($batchuninstall) {
		$actiontotake = 'batchuninstall';
		$batchButton = &new Link($this->uri(array('cat'=>$activeCat),'modulecatalogue'));
		$batchButton->link = $this->objLanguage->languageText('mod_modulecatalogue_batchinstall','modulecatalogue');
		$batchButton->extra = "class='pseudobutton'";
		$batchChange = $batchButton->show();
		$batchButton = &new button('formsub');
		$batchButton->setValue($this->objLanguage->languageText('mod_modulecatalogue_uninstallselected','modulecatalogue'));
		$batchButton->setToSubmit();  //If you want to make the button a submit button
		$batchAction = $batchButton->show();
	} else {
		$actiontotake = 'batchinstall';
		$batchButton = &new Link($this->uri(array('cat'=>$activeCat,'uninstall'=>'1'),'modulecatalogue'));
		$batchButton->link = $this->objLanguage->languageText('mod_modulecatalogue_batchuninstall','modulecatalogue');
		$batchButton->extra = "class='pseudobutton'";
		$batchChange = $batchButton->show();
		$batchButton = &new button('formsub');
		$batchButton->setValue($this->objLanguage->languageText('mod_modulecatalogue_installselected','modulecatalogue'));
		$batchButton->setToSubmit();  //If you want to make the button a submit button
		$batchAction = $batchButton->show();
	}
	$topTable = &$this->newObject('htmltable','htmlelements');
	$topTable->cellpadding = 2;
	$topTable->addRow(array($batchChange),null,'align="right"');
	$top = $topTable->show();
	$bottomTable = &$this->newObject('htmltable','htmlelements');
	$bottomTable->cellpadding = 2;
	$bottomTable->startRow();
	$bottomTable->addCell($batchAction,null,null,'right',null);
	$bottomTable->endRow();
	$bot = $bottomTable->show();
	foreach ($modules as $modName) {
		if (in_array($modName,$localModules)){//dont display downloadable modules until that functionality is complete
		//$isRegistered = $hasController = $hasRegFile = '';
		$textButton = &new Link($this->uri(array('action'=>'textelements','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
		$textButton->link = $this->objLanguage->languageText('mod_modulecatalogue_textelement','modulecatalogue');
		$textButton->extra = $instButton->extra = "class=\"pseudobutton\"";
		$class = ($count % 2 == 0)? 'even' : 'odd';
		$count++;
		$ucMod = ucwords($modName);
		$desc = $this->objCatalogueConfig->getModuleDescription($modName);
		if (!$desc) {
			$desc = $this->objLanguage->languageText('mod_modulecatalogue_nodesc','modulecatalogue');
		} else {
			$desc = (string)$desc[0];
			//if (strlen($desc)>40) {
			//	$end = substr($desc,40);
			//	//echo strchr($end,' ');
			//	$end = substr($end,0,strlen($end)-strlen(strchr($end,' '))).'...';
			//	$desc = substr($desc,0,40).$end;
			//}
		}
		$desc = htmlentities($desc);
		$infoButton = &new Link($this->uri(array('action'=>'info','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
		$infoButton->link = $this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue');
		$link = $ucMod;
		$icon->setModuleIcon($modName);
		$icon->alt = $modName;
		$objCheck->checkbox('arrayList[]');
		$objCheck->cssId = 'checkbox_'.$modName;
        $objCheck->setValue($modName);
        //if ($this->objModFile->findController($modName)) {
        //	$icon->setIcon('ok','png');
        //} else {
        //	$icon->setIcon('failed','png');
        //}
        //$hasController = $icon->show();
		if (!in_array($modName,$localModules)){ //module available on server but not locally
			$instButton = &new Link($this->uri(array('action'=>'download','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
			$instButton->link = $this->objLanguage->languageText('word_download');
			$instButtonShow = $instButton->show();
			$checkBox='';
			$texts = '';
			$info = '';//$infoButton->show(); only add this once back end functionality is complete
		} else { //local module
			if ($this->objModFile->findRegisterFile($modName)) {//has regfile
				$texts = $textButton->show();
				$info = $infoButton->show();
			//	$icon->setIcon('ok','png');
			//	$hasRegFile = $icon->show();
				if (!(in_array($modName,$rMods))) { //not registered
					$instButton = &new Link($this->uri(array('action'=>'install','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
					$instButton->link = $this->objLanguage->languageText('word_install');
					$instButtonShow = $instButton->show();
					if (!$batchuninstall) {
						$checkBox=$objCheck->show();
					} else {
						$checkBox='';
					}
					//$icon->setIcon('failed','png');
					//$isRegistered = $icon->show();
				} else {//registered
					if ($this->objModFile->findController($modName)) {
						$link = "<a href='{$this->uri(null,$modName)}'>$ucMod</a>";
					}
					if (!strchr(strtolower($defaults),strtolower($modName))) {
						$instButton = &new Link($this->uri(array('action'=>'uninstall','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'));
						$instButton->link = $this->objLanguage->languageText('word_uninstall');
						$objConfirm = &$this->getObject('confirm','utilities');
						$objConfirm->setConfirm($this->objLanguage->languageText('word_uninstall'),
									$this->uri(array('action'=>'uninstall','mod'=>$modName,'cat'=>$activeCat),'modulecatalogue'),
									str_replace('MODULE',$modName,$this->objLanguage->languageText('mod_modulecatalogue_deregsure','modulecatalogue')));
						$instButtonShow = $objConfirm->show();
    					if ($batchuninstall) {
							$checkBox=$objCheck->show();
						} else {
							$checkBox='';
						}
					} else {
						$checkBox='';
    					$instButtonShow = '';
    				}
					
					//$icon->setIcon('ok','png');
					//$isRegistered = $icon->show();
				}
			} else { //no regfile
				$texts = '';
				$info = '';
				$instButtonShow = '';
				$checkBox='';
				//$icon->setIcon('failed','png');
				//$hasRegFile = $icon->show();

			}
		}
		$icon->setModuleIcon($modName);
		$icon->alt = $desc;
		$objTable->startRow();
		$objTable->addCell($checkBox,null,null,'left',$class);
		$objTable->addCell($icon->show(),null,null,'left',$class);
		$objTable->addCell($link,null,null,'left',$class);
		$objTable->addCell($desc,null,null,'left',$class);
		//$objTable->addCell($hasRegFile,null,null,'left',$class);
		//$objTable->addCell($hasController,null,null,'left',$class);
		//$objTable->addCell($isRegistered,null,null,'left',$class);
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
	if (is_array($output)) {
		$timeOutMsg->message = '';
		foreach ($output as $line) {
			$timeoutMsg->message .= $line.'<br/>';
		}
	} else {
		$timeoutMsg->setMessage($output);
	}
	$notice = $timeoutMsg->show();
	$this->unsetSession('output');
}

$objForm = &new form('batchform',$this->uri(array('action'=>$actiontotake,'cat'=>$activeCat),'modulecatalogue'));
$objForm->displayType = 3;
$objForm->addToForm($notice);
$objForm->addToForm($top);
$objForm->addToForm($objTable->show());
$objForm->addToForm($bot);
//if ($actiontotake == 'batchuninstall') {
//	$objForm->extra = 'onsubmit="javascript: if(confirm(\''.$this->objLanguage->languageText('mod_modulecatalogue_batchconfirm').'\'))
//	 {document.location=\'\'}"';
//}


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
$hTable->addCell($objH->show(),null,null,'left');
$hTable->addCell($srchStr->show(),null,'bottom','right');
$hTable->endRow();
$hTable->startRow();
$hTable->addCell(null,null,null,'left');
$hTable->addCell($srch,null,'top','right');
$hTable->endRow();
$searchForm->addToForm($hTable->show());


echo $searchForm->show().$objForm->show();
//$content = $objH->show().$notice.$topTable->show().$objTable->show().$bottomTable->show();
//echo $content;
?>