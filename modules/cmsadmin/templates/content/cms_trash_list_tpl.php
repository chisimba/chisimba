<?php
/**
* Template to display the list of trashed content pages.
*/

//Get Selectall js
echo $this->getJavascriptFile('selectall.js');

$objHead = $this->newObject('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objEditIcon = $this->newObject('geticon', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');

$head = $this->objLanguage->languageText('mod_cmsadmin_archive', 'cmsadmin');
$pageHead = $this->objLanguage->languageText('mod_cmsadmin_archivedpages', 'cmsadmin');
$sectionHead = $this->objLanguage->languageText('mod_cmsadmin_archivedsections', 'cmsadmin');
$objSectionInfo = $this->newObject('dbsections','cmsadmin');
$lbFilter = $this->objLanguage->languageText('word_filter');
$lbGo = $this->objLanguage->languageText('word_go');
$lbReset = $this->objLanguage->languageText('word_reset');
$lbNoTrash = $this->objLanguage->languageText('mod_cmsadmin_noitemsinarchive', 'cmsadmin');
$lbNoTrash2 = $this->objLanguage->languageText('mod_cmsadmin_nosectionsinarchive', 'cmsadmin');

// table headings
$hdPageTitle = $this->objLanguage->languageText('mod_cmsadmin_pagetitle', 'cmsadmin');
$hdDate = $this->objLanguage->languageText('mod_cmsadmin_articledate', 'cmsadmin');
$hdFolderTitle = $this->objLanguage->languageText('mod_cmsadmin_menuname', 'cmsadmin');
$hdSection = $this->objLanguage->languageText('word_section');
$hdOptions = $this->objLanguage->languageText('word_options');
$lbSelectAll = $this->objLanguage->languageText('phrase_selectall');

// Heading
$objIcon->setIcon('trash', 'png', 'icons/cms/');

$objHead->str = $objIcon->show().'&nbsp;'.$head;
$objHead->type = 1;
$objLayer->str = $objHead->show();
//$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_left';
$headStr = $objLayer->show();

$objLayer->str = $topNav;
//$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_right';
$headStr .= $objLayer->show();

$objLayer->str = '';
//$objLayer->border = '; clear:both; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_clear';
$objLayer->cssClass = 'clearboth';
$navStr = $objLayer->show();

$objLayer->id = 'cmsvspacer';
$objLayer->str = '&nbsp;';
$vspacer = $objLayer->show();

$str = $headStr.$navStr.$vspacer;

// Filters

// Text filter
$objLabel = new label($lbFilter.': ', 'input_txtfilter');
$objInput = new textinput('txtfilter');
$filterStr = $objLabel->show().'&nbsp;'.$objInput->show();

$objButton = new button('save', $lbGo);
$objButton->setToSubmit();
$filterStr .= '&nbsp;&nbsp;'.$objButton->show();

$objButton = new button('reset', $lbReset);
$objButton->setOnClick("javascript:document.getElementById('input_txtfilter').value='';");
$objButton->setToSubmit();
$filterStr .= '&nbsp;&nbsp;'.$objButton->show();

$objForm = new form('filter', $this->uri(array('action' => 'trashmanager')));
$objForm->addToForm($filterStr);
$str .= '<p>'.$objForm->show().'</p>';


/* ** archived content pages ** */

$objHead->str = $pageHead;
$objHead->type = 3;
$str .= $objHead->show();

if(!empty($data)){
    $class = 'odd';
  
    $objTable = new htmltable();
    $objTable->cellpadding = '5';
    $objTable->cellspacing = '2';
    
    $objCheck = new checkbox('toggle');
    $objCheck->extra = "onclick=\"javascript:ToggleCheckBoxes('select', 'arrayList[]', 'toggle');\"";
       
    $hdArr = array();
    $hdArr[] = $objCheck->show().' '.$lbSelectAll;
    $hdArr[] = $hdPageTitle;
    $hdArr[] = $hdDate;
    $hdArr[] = $hdSection;
    $hdArr[] = $hdOptions;
    
    $objTable->addHeader($hdArr);
    
    foreach($data as $item){
        $class = ($class == 'odd') ? 'even':'odd';
        $sectionInfo = $objSectionInfo->getSection($item['sectionid']);
        
        $objCheck = new checkbox('arrayList[]');
        $objCheck->setValue($item['id']);
        $objCheck->extra = "onclick=\"javascript: ToggleMainBox('select', 'toggle', this.checked);\"";
        
        // Create delete icon
		$delArray = array('action' => 'deletecontent', 'confirm'=>'yes', 'id'=>$item['id']);
		$deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelsection', 'cmsadmin');
		$delIcon = $objIcon->getDeleteIconWithConfirm($item['id'], $delArray,'cmsadmin',$deletephrase);
	    
        $options = '&nbsp;'.$delIcon;
        
        $row = array();
        $row[] = $objCheck->show();
        $row[] = $item['title'];
        if(!empty($item['start_publish'])){
            $row[] = @date('r', $item['start_publish']);
        }else{
            $row[] = @date('r', $item['created']);
        }
        $row[] = $sectionInfo['title'];
        $row[] = $options;
        
        $objTable->addRow($row, $class);
    }
    
    $objInput = new textinput('task', '', 'hidden');
    $hidden = $objInput->show();
    
    $objForm = new form('select', $this->uri(array('action' => 'restore')));
    $objForm->addToForm($objTable->show().$hidden);
    $str .= $objForm->show();
}else{
    $str .= '<p class="noRecordsMessage">'.$lbNoTrash.'</p>';
}

/* ** archived sections ** */

$objHead->str = $sectionHead;
$objHead->type = 3;
$str .= $objHead->show();

 if(!empty($sectionData)){
    $class = 'odd';
  
    $objTable = new htmltable();
    $objTable->cellpadding = '5';
    $objTable->cellspacing = '2';
    
    $objCheck = new checkbox('toggle');
    $objCheck->extra = "onclick=\"javascript:ToggleCheckBoxes('selectsections', 'arrayList[]', 'toggle');\"";
       
    $hdArr = array();
    $hdArr[] = $objCheck->show().' '.$lbSelectAll;
    $hdArr[] = $hdSection;
    $hdArr[] = $hdFolderTitle;
    $hdArr[] = $hdOptions;
    
    $objTable->addHeader($hdArr);
    
    foreach($sectionData as $item){
        $class = ($class == 'odd') ? 'even':'odd';
        
        $objCheck = new checkbox('arrayList[]');
        $objCheck->setValue($item['id']);
        $objCheck->extra = "onclick=\"javascript: ToggleMainBox('selectsections', 'toggle', this.checked);\"";
        
        //Create delete icon
		$delArray = array('action' => 'removesection', 'confirm'=>'yes', 'id'=>$item['id']);
		$deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelsection', 'cmsadmin');
		$delIcon = $objIcon->getDeleteIconWithConfirm($item['id'], $delArray,'cmsadmin',$deletephrase);
        $options = '&nbsp;'.$delIcon;
        
        $row = array();
        $row[] = $objCheck->show();
        $row[] = $item['title'];
        $row[] = $item['menutext'];
        $row[] = $options;
        
        $objTable->addRow($row, $class);
    }
    
    $objInput = new textinput('task', '', 'hidden');
    $hidden = $objInput->show();
    
    $objForm = new form('selectsections', $this->uri(array('action' => 'restoresections')));
    $objForm->addToForm($objTable->show().$hidden);
    $str .= $objForm->show();
}else{
    $str .= '<p class="noRecordsMessage">'.$lbNoTrash2.'</p>';
}

echo $str;
?>
