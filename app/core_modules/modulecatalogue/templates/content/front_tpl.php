<?php
$this->loadClass('link','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('checkbox','htmlelements');

//$objCheck = new checkbox('instCheck');//&$this->newObject('checkbox','htmlelements');
$objH = $this->getObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_heading','modulecatalogue');
$notice = $top = $bot = '';
if (!isset($result)) {
	$modules = $this->objCatalogueConfig->getModuleList($activeCat);//,$letter);
	var_dump($modules);
} else {
	$modules = $result; //search results
}

$icon = &$this->getObject('geticon', 'htmlelements');

$objTable = &$this->getObject('htmltable','htmlelements');
$objTable->cellpadding = 2;
//$objTable->cellspacing = 3;
$objTable->id = 'unpadded';
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
$masterCheck = new checkbox('arrayList[]');
$masterCheck->extra = 'onclick="javascript:baseChecked(this);"';

$head = array($masterCheck->show(),'&nbsp;',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'),
			/*$this->objLanguage->languageText('mod_modulecatalogue_description','modulecatalogue'),*/
			$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_textelement','modulecatalogue')
			,$this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue'));

$count = 0;
$localModules = $this->objModFile->getLocalModuleList();
$actiontotake = 'batchinstall';
$root = $this->objConfig->getsiteRootPath();
//$defaults = file_get_contents($root.'installer/dbhandlers/default_modules.txt'); TODO: replace this with the xml list of core modules
$registeredModules = $this->objModule->getAll();
foreach ($registeredModules as $module) {
	$rMods[]=$module['module_id'];
}

if ($modules) {
	asort($modules);
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
		$batchButton->extra=' onclick="if(confirm(\''.$this->objLanguage->languageText('mod_modulecatalogue_confirmbatchuninst','modulecatalogue').'\'))
							{document.getElementById(\'form_batchform\').submit();}"';
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
	foreach ($modules as $moduleId => $moduleName) {
		if (in_array($moduleId,$localModules)){//dont display downloadable modules until that functionality is complete
		//$isRegistered = $hasController = $hasRegFile = '';
		$textButton = &new Link($this->uri(array('action'=>'textelements','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'));
		$textButton->link = $this->objLanguage->languageText('mod_modulecatalogue_textelement','modulecatalogue');
		$textButton->extra = $instButton->extra = "class=\"pseudobutton\"";
		$class = ($count % 2 == 0)? 'even' : 'odd';
		$count++;
		$desc = $this->objCatalogueConfig->getModuleDescription($moduleId);
		if (isset($desc[0])) {
			$desc = (string)$desc[0];
		} else {
			$desc = $this->objLanguage->languageText('mod_modulecatalogue_nodesc','modulecatalogue');
		}
		$desc = $this->objLanguage->abstractText(htmlentities($desc));
		$infoButton = &new Link($this->uri(array('action'=>'info','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'));
		$infoButton->link = $this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue');
		$link = $moduleName;
		$icon->setModuleIcon($moduleId);
		$icon->alt = $moduleName;
		$objCheck = new checkbox('arrayList[]');
		$objCheck->cssId = 'checkbox_'.$moduleId;
        $objCheck->setValue($moduleId);
        $objCheck->extra = 'onclick="javascript:toggleChecked(this);"';
        //if ($this->objModFile->findController($moduleId)) {
        //	$icon->setIcon('ok','png');
        //} else {
        //	$icon->setIcon('failed','png');
        //}
        //$hasController = $icon->show();
		if (!in_array($moduleId,$localModules)){ //module available on server but not locally
			$instButton = &new Link($this->uri(array('action'=>'download','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'));
			$instButton->link = $this->objLanguage->languageText('word_download');
			$instButtonShow = $instButton->show();
			$checkBox='';
			$texts = '';
			$info = '';//$infoButton->show(); only add this once back end functionality is complete
		} else { //local module
			if ($this->objModFile->findRegisterFile($moduleId)) {//has regfile
				$texts = $textButton->show();
				$info = $infoButton->show();
			//	$icon->setIcon('ok','png');
			//	$hasRegFile = $icon->show();
				if (!(in_array($moduleId,$rMods))) { //not registered
					$instButton = &new Link($this->uri(array('action'=>'install','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'));
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
					if ($this->objModFile->findController($moduleId)) {
						$link = "<a href='{$this->uri(null,$moduleId)}'>$moduleName</a>";
					}
					//if (!strchr(strtolower($defaults),strtolower($moduleId))) { //check if the module can be uninstalled - removed as it was still using default_modules.txt
						$instButton = &new Link($this->uri(array('action'=>'uninstall','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'));
						$instButton->link = $this->objLanguage->languageText('word_uninstall');
						$objConfirm = &$this->getObject('confirm','utilities');
						$objConfirm->setConfirm($this->objLanguage->languageText('word_uninstall'),
									$this->uri(array('action'=>'uninstall','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'),
									str_replace('MODULE',$moduleId,$this->objLanguage->languageText('mod_modulecatalogue_deregsure','modulecatalogue')));
						$instButtonShow = $objConfirm->show();
    					if ($batchuninstall) {
							$checkBox=$objCheck->show();
						} else {
							$checkBox='';
						}
					//} else {
					//	$checkBox='';
    				//	$instButtonShow = '';
    				//}

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
		$icon->setModuleIcon($moduleId);
		$icon->alt = $desc;
		$objTable->startRow();
		//$objTable->addCell(,null,null,'left',$class);
		$objTable->addCell($checkBox,null,null,'left',$class);
		$objTable->addCell($icon->show(),null,null,'left',$class);
		$objTable->addCell('<strong>'.$link.'</strong>',null,null,'left',$class);
		$objTable->addCell($instButtonShow,null,null,'left',$class);
		$objTable->addCell($texts,null,null,'left',$class);
		$objTable->addCell($info,null,null,'left',$class);
		$objTable->endRow();
		$objTable->startRow();
		$objTable->addCell('&nbsp;',20,null,'left',$class);
		$objTable->addCell('&nbsp;',30,null,'left',$class);
		$objTable->addCell($desc.'<br />&nbsp;',null,null,'left',$class, 'colspan="4"');
		//$objTable->addCell($hasRegFile,null,null,'left',$class);
		//$objTable->addCell($hasController,null,null,'left',$class);
		//$objTable->addCell($isRegistered,null,null,'left',$class);

		$objTable->endRow();
		}//temporary if
	}
} else {
	$objTable->startRow();
	$objTable->addCell('<span class="empty">'.$this->objLanguage->languageText('mod_modulecatalogue_noitems','modulecatalogue').'</span>');
	$objTable->endRow();
}
if (($output=$this->getSession('output'))!=null) {
	$error = $this->getParam('lastError');
	if (!isset($error)) {
	    $timeoutMsg = &$this->getObject('timeoutmessage','htmlelements');
	    if (is_array($output)) {
	        $timeoutMsg->setMessage(implode('<br />',$output));
	    } else {
	        $timeoutMsg->setMessage($output);
	    }
	    $notice = $timeoutMsg->show();
	} else {
	    //var_dump($output);
	    if (is_array($output)) {
	        $output = implode('<br />',$output);
	    }
	    $notice = "<span class='error'>$output</span>";
	}
	$this->unsetSession('output');
}

$objForm = &new form('batchform',$this->uri(array('action'=>$actiontotake,'cat'=>$activeCat),'modulecatalogue'));
$objForm->displayType = 3;
$objForm->addToForm($notice);
$objForm->addToForm($top);
$objForm->addToForm($objTable->show());
$objForm->addToForm($bot);

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

$script = "
<script type='text/javascript'>

function toggleChecked(oElement)
{
  oForm = oElement.form;
  oElement = oForm.elements[oElement.name];
  if(oElement.length)
  {
    bChecked = true;
    nChecked = 0;
    for(i = 1; i &lt; oElement.length; i++)
      if(oElement[i].checked)
        nChecked++;
    if(nChecked &lt; oElement.length - 1)
    {
      bChecked = false;
    }
    else
    {
      bChecked = true;
    }
    oElement[0].checked = bChecked;
  }
}

function baseChecked(oElement)
{
  oForm = oElement.form;
  oElement = oForm.elements[oElement.name];
  if(oElement.length)
  {
    bChecked = oElement[0].checked;
    for(i = 1; i &lt; oElement.length; i++)
      oElement[i].checked = bChecked;
  }
}

</script>
";
$this->appendArrayVar('headerParams',$script);
echo $searchForm->show().$objForm->show();
//$content = $objH->show().$notice.$topTable->show().$objTable->show().$bottomTable->show();
//echo $content;
?>