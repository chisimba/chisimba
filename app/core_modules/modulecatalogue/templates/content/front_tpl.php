<?php
$this->loadClass('link','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('checkbox','htmlelements');

$objH = $this->getObject('htmlheading','htmlelements');
$objH->type=2;
$objH->str = $this->objLanguage->languageText('mod_modulecatalogue_heading','modulecatalogue');
$notice = $top = $bot = '';
if (!isset($result)) {
	$modules = $this->objCatalogueConfig->getCategoryList($activeCat);
} else {
	$modules = $result; //search results
}

$missingModules = false;
$icon = $this->getObject('geticon', 'htmlelements');

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellpadding = 2;

$objTable->id = 'unpadded';
$objTable->width='100%';

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

$alink = new link();

if ($modules) {
    //asort($modules);
    natcasesort($modules);
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

    $objRemoteTable = $this->newObject('htmltable','htmlelements');
    $objRemoteTable->cellpadding = 2;
    $rhead = array('&nbsp;','&nbsp;',$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue'),
			$this->objLanguage->languageText('mod_modulecatalogue_install','modulecatalogue'));
    $objRemoteTable->addHeader($rhead,'heading','align="left"');
    if (!$connected) {
        $objRemoteTable->startRow();
        $objRemoteTable->addCell('<i>'.$this->objLanguage->languageText('mod_modulecatalogue_rpcerror','modulecatalogue').'</i>',null,null,'left',null,'colspan="4"');
        $objRemoteTable->endRow();
        $objRemoteTable->startRow();
        $objRemoteTable->addCell('&nbsp;',null,null,'left',null,'colspan="4"');
        $objRemoteTable->endRow();
    }

    $topTable = $this->newObject('htmltable','htmlelements');
    $topTable->cellpadding = 2;
    $topTable->addRow(array($batchChange),null,'align="right"');
    $top = $topTable->show();
    $bottomTable = $this->newObject('htmltable','htmlelements');
    $bottomTable->cellpadding = 2;
    $bottomTable->startRow();
    $bottomTable->addCell($batchAction,null,null,'right',null);
    $bottomTable->endRow();
    $bot = $bottomTable->show();

    $rClass = 'odd';
    foreach ($modules as $moduleId => $moduleName) {
        //echo $moduleId;
        $icon->setModuleIcon($moduleId);
        $icon->alt = $moduleId;
        if (in_array($moduleId,$localModules)) { //dont display downloadable modules until that functionality is complete
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
            $desc = $this->objLanguage->abstractText(($desc));
            $objWasher = $this->getObject('bbcodeparser', 'utilities');
    		$desc = $objWasher->parse4BBcode($desc);
            $infoButton = &new Link($this->uri(array('action'=>'info','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'));
            $infoButton->link = $this->objLanguage->languageText('mod_modulecatalogue_info2','modulecatalogue');
            $link = $moduleName = ucfirst($moduleName);
            $objCheck = new checkbox('arrayList[]');
            $objCheck->cssId = 'checkbox_'.$moduleId;
            $objCheck->setValue($moduleId);
            $objCheck->extra = 'onclick="javascript:toggleChecked(this);"';

            if ($this->objModFile->findRegisterFile($moduleId)) {//has regfile
                $texts = $textButton->show();
                $info = $infoButton->show();
                if (!(in_array($moduleId,$rMods))) { //not registered
                    $instButton = &new Link($this->uri(array('action'=>'install','mod'=>$moduleId,'cat'=>$activeCat),'modulecatalogue'));
                    $instButton->link = $this->objLanguage->languageText('word_install');
                    $instButtonShow = $instButton->show();
                    if (!$batchuninstall) {
                        $checkBox=$objCheck->show();
                    } else {
                        $checkBox='';
                    }
                } else {//registered
                    if ($this->objModFile->findController($moduleId)) {
                        $link = "<a href='{$this->uri(null,$moduleId)}'>$moduleName</a>";
                    }
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

                }
            } else { //no regfile
                $texts = '';
                $info = '';
                $instButtonShow = '';
                $checkBox='';


            }
            $objTable->startRow();
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


            $objTable->endRow();
        } else {
            $missingModules = true;
            if (!$connected) {
                $moduleName = ucfirst($moduleId);
                $desc = $this->objLanguage->languageText('mod_modulecatalogue_nodesc','modulecatalogue');
                $actions = false;
            } else {
                $doc = simplexml_load_string($this->objRPCClient->getModuleDescription($moduleId));
                $moduleName = ucfirst((string)$doc->array->data->value[0]->string);
                $desc = (string)$doc->array->data->value[1]->string;

                $alink->link('javascript:;');
                $alink->extra = "onclick = 'javascript:downloadModule(\"$moduleId\",\"$moduleName\");'";
                $alink->link = $this->objLanguage->languageText('mod_modulecatalogue_dlandinstall','modulecatalogue');
                $actions = $alink->show();

                if ($moduleName == '') {
                    $moduleName = ucfirst($moduleId);
                    $desc = $this->objLanguage->languageText('mod_modulecatalogue_nodesc','modulecatalogue');
                    $actions = false;
                }
            }

            $rClass = ($rClass == 'odd')? 'even' : 'odd';
            $objRemoteTable->startRow();
            $objRemoteTable->addCell('&nbsp;',20,null,'left',$rClass);
            $objRemoteTable->addCell($icon->show(),30,null,'left',$rClass);
            $objRemoteTable->addCell("<div id='link_$moduleId'><strong>".$moduleName.'</strong></div>',null,null,'left',$rClass);
            $objRemoteTable->addCell("<div id='download_$moduleId'>$actions</div>",'40%',null,'left',$rClass);
            $objRemoteTable->endRow();
            $objRemoteTable->startRow();
            $objRemoteTable->addCell('&nbsp;',20,null,'left',$rClass);
            $objRemoteTable->addCell('&nbsp;',30,null,'left',$rClass);
            $objRemoteTable->addCell($desc.'<br />&nbsp;',null,null,'left',$rClass, 'colspan="2"');
            $objRemoteTable->endRow();
        }
    }
} else {
	$objTable->startRow();
	$objTable->addCell('<span class="empty">'.$this->objLanguage->languageText('mod_modulecatalogue_noitems','modulecatalogue').'</span>');
	$objTable->endRow();
}
if (($output=$this->getSession('output'))!=null) {
	if (!isset($error)) {
	    $error = $this->getParam('lastError');
	}
	if (!isset($error)) {
	    $timeoutMsg = $this->getObject('timeoutmessage','htmlelements');
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
$srchType->addOption('tags',$this->objLanguage->languageText('mod_modulecatalogue_tags', 'modulecatalogue'));
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

$this->appendArrayVar('headerParams',"<script type='text/javascript' src='core_modules/modulecatalogue/resources/remote.js'></script>");

$objSelectFile = new textinput('archive',null,'file',30);
$uploadSize = new textinput('MAX_FILE_SIZE','8000000','hidden');
$fButton = new button('bupload',$this->objLanguage->languageText('word_upload'));//,"javascript:uploadArchive($('form_fupload').archive.value,'$moduleId');");
$fButton->setToSubmit();
$objUForm = new form('fupload',$this->uri(array('action'=>'uploadarchive','cat'=>$activeCat)));
$objUForm->extra = 'enctype="multipart/form-data"';
$objUForm->addToForm($uploadSize->show());
$objUForm->addToForm($objSelectFile->show());
$objUForm->addToForm($fButton->show());

$objUploadTable = $this->newObject('htmlTable','htmlelements');
$objUploadTable->startRow();
$objUploadTable->addCell('&nbsp;');
$objUploadTable->addCell($this->objLanguage->languageText('mod_modulecatalogue_uploadmod','modulecatalogue')."<br />".$objUForm->show(),null,null,'right');
$objUploadTable->endRow();

if ($modules && $missingModules) {
    $remoteEx = new htmlHeading();
    $remoteEx->type = 4;
    $remoteEx->str = $this->objLanguage->languageText('mod_modulecatalogue_rpcex','modulecatalogue');
    $remote = $remoteEx->show()."<br />".$objUploadTable->show().$objRemoteTable->show();
} else {
    $remote ='';
}

echo $searchForm->show().$objForm->show().$remote;
//$content = $objH->show().$notice.$topTable->show().$objTable->show().$bottomTable->show();
//echo $content;
?>