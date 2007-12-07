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

$updateAll = $this->getObject('link','htmlelements');
$updateAll->link($this->uri(array('action'=>'makepatch')));
$updateAll->link = $this->objLanguage->languageText('mod_modulecatalogue_makepatch','modulecatalogue');
$makePatch = $updateAll->show();

$updateAll->link($this->uri(array('action'=>'updatexml')));
$updateAll->link = str_replace('[DATE]',date('Y/m/d',filemtime($this->objConfig->getsiteRootPath()."config/catalogue.xml")),$this->objLanguage->languageText('mod_modulecatalogue_updatexml','modulecatalogue'));
$updateXML = $updateAll->show();

$updateAll->link($this->uri(array('action'=>'updateall')));
$updateAll->link = $this->objLanguage->languageText('mod_modulecatalogue_updateall','modulecatalogue');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->startRow();
$objTable->addCell($h2->show(),null,null,'left');
$objTable->addCell($updateAll->show()."<br />$updateXML",null,null,'right');//<br/>$makePatch
$objTable->endRow();
$tString = '';
if (isset($output)) {
	$msg = $this->getObject('timeoutmessage','htmlelements');
	$msg->message = '';

	if (isset($output['unMetDep'])) {

		$msg->message = $this->objLanguage->languageText('mod_modulecatalogue_unmetdependencies','modulecatalogue').":<br />";
		if (count($output['modules'])>0) {
			$text = str_replace('{MODULE}',$output['unMetDep'],$this->objLanguage->languageText('mod_modulecatalogue_updatedeps','modulecatalogue'));
			$link = "<a href='".$this->uri(array('action'=>'updatedeps','modname'=>$output['unMetDep']),'modulecatalogue')."'>".$text."</a>";
			foreach ($output['modules'] as $dep) {
				$dep = ucfirst($dep);
				$msg->message .= "<b>$dep</b><br />";
			}
		}
		if (count($output['missing']) > 0) {
			$link = $this->objLanguage->languageText('mod_modulecatalogue_downloadmissing','modulecatalogue');
			foreach ($output['missing'] as $dep) {
				$dep = ucfirst($dep);
				$notPresent = $this->objLanguage->languageText('mod_modulecatalogue_needdownload','modulecatalogue');
				$msg->message .= "<b>$dep</b> - $notPresent<br />";
			}
		}
		$tString = "<span class='error'>$msg->message</span>$link";
	} else {
		if (array_key_exists('current',$output)) {
				$success = str_replace('[OLDVER]',"<b>{$output['old']}</b>",$this->objLanguage->languageText('mod_modulecatalogue_updatesuccess','modulecatalogue'));
				$success = str_replace('[NEWVER]',"<b>{$output['current']}</b>",$success);
				$msg->message .= "<b>{$output['modname']}</b> $success<br />";
		} else {
			foreach ($output as $value) {
				if (is_array($value)) {
					$success = str_replace('[OLDVER]',"<b>{$value['old']}</b>",$this->objLanguage->languageText('mod_modulecatalogue_updatesuccess','modulecatalogue'));
					$success = str_replace('[NEWVER]',"<b>{$value['current']}</b>",$success);
					$msg->message .= "<b>{$value['modname']}</b> $success<br />";

				} else {
				    $pError = $this->objLanguage->languageText('mod_modulecatalogue_patcherror','modulecatalogue');
	                $tString .= "<span class='error'>$pError: $value</span><br />";
				}
			}
		}
		$tString .= $msg->show();
	}
	if (!is_array($output)) {
	    $pError = $this->objLanguage->languageText('mod_modulecatalogue_patcherror','modulecatalogue');
	    $tString = "<span class='error'>$pError: $output</span>";
	}
}
$out = $this->getParam('message');
if (isset($out)) {
    //var_dump($out);
	$msg = $this->getObject('timeoutmessage','htmlelements');
	$msg->message = $out;
	$tString .= "<br />".$msg->show();
}
if (isset($error)) {
    $tString .= "<br /><span class='error'>$error</span>";
}

$updateAll->link($this->uri(array('action'=>'patchall')));
$updateAll->link = $this->objLanguage->languageText('mod_modulecatalogue_patchall','modulecatalogue');
$objT2 = $this->newObject('htmltable','htmlelements');
$objT2->startRow();
$objT2->addCell($updateAll->show(),null,null,'right');
$objT2->endRow();

$str = '';
if (!empty($patchArray)) {
	foreach ($patchArray as $patch) {
		$uri = $this->uri(array('action'=>'update','mod'=>$patch['module_id'],'patchver'=>$patch['new_version']),'modulecatalogue');
		$link = &new Link($uri);
		$pIcon = $this->getObject('geticon','htmlelements');
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
$srchType->addOption('tags',$this->objLanguage->languageText('mod_modulecatalogue_tags', 'modulecatalogue'));
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