<?php
/**
* Template to display the list of menu items
*/

//Get Selectall js
echo $this->getJavascriptFile('selectall.js');

$objHead = $this->newObject('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objEditIcon = $this->newObject('geticon', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$objRound = $this->newObject('roundcorners','htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link','htmlelements');

$head = $this->objLanguage->languageText('mod_cmsadmin_menu', 'cmsadmin');
$lbFilter = $this->objLanguage->languageText('word_filter');
$lbGo = $this->objLanguage->languageText('word_go');
$lbReset = $this->objLanguage->languageText('word_reset');
$lbNoTrash = $this->objLanguage->languageText('mod_cmsadmin_noitems', 'cmsadmin');
$lnConfigureBlocks = $this->objLanguage->languageText('mod_cmsadmin_configureleftblocks', 'cmsadmin');

// table headings
$hdPageTitle = $this->objLanguage->languageText('mod_cmsadmin_pagetitle', 'cmsadmin');
$hdPub = $this->objLanguage->languageText('word_unpublished');
$hdunPub = $this->objLanguage->languageText('word_published');
$hdOptions = $this->objLanguage->languageText('word_options');

// Heading
$objIcon->setIcon('menu', 'png', 'icons/cms/');

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
//border = '; clear:both; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_clear';
$objLayer->cssClass = 'clearboth';
$navStr = $objLayer->show();

$str = $objRound->show($headStr.$navStr);

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
$filterStr .= '&nbsp;&nbsp;'.$objButton->show();

$objForm = new form('filter', $this->uri(array('action' => 'managemenus')));
$objForm->addToForm($filterStr);
$str .= '<p>'.$objForm->show().'</p>';

if(!empty($content)){
    $class = 'even';
  
    $objTable = new htmltable();
    $objTable->cellpadding = '5';
    $objTable->cellspacing = '2';
    
    $objCheck = new checkbox('toggle');
    $objCheck->extra = "onclick=\"javascript:SetAllCheckBoxes('select', 'arrayList[]', true);\"";
    $link = new link();   
    $hdArr = array();
    $hdArr[] = $objCheck->show();
    $hdArr[] = $hdPageTitle;
    $hdArr[] = $hdPub;
    $hdArr[] = $hdunPub;
    $hdArr[] = $hdOptions;
    
    $objTable->addHeader($hdArr);
    
    foreach($content as $item){
        $class = ($class == 'odd') ? 'even':'odd';
        $objCheck = new checkbox('arrayList[]');
        $objCheck->setValue($item['id']);
        //Create delete icon
		$delArray = array('action' => 'deletemenu', 'confirm'=>'yes', 'id'=>$item['id']);
		$deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelmenu', 'cmsadmin');
		$delIcon = $objIcon->getDeleteIconWithConfirm($item['id'], $delArray,'cmsadmin',$deletephrase);
	
		//edit icon
		$editIcon = $objEditIcon->getEditIcon($this->uri(array('action' => 'addnewmenu','pageid'=>$item['id'])));
 	    $options = $editIcon.'&nbsp;'.$delIcon;
 	    
 	    //url
 	    $url = $this->uri(array('action' => 'addnewmenu','pageid'=>$item['id'],'add'=>'TRUE'), 'cmsadmin');
        $link->link = $item['title'];
	    $link->href = $url;
        $row = array();
        $row[] = $objCheck->show();
        $row[] = $link->show();
        $row[] = $item['link_reference'];
        $row[] = $item['layout'];
        $row[] = $options;
        
        $objTable->addRow($row, $class);
    }
    $objForm = new form('select', $this->uri(array('action' => 'managemenus')));
    $objForm->addToForm($objTable->show());
    $str .= $objForm->show();
}else{
    $str .= '<br><br><p class="noRecordsMessage" <br><br><br> >'.$lbNoTrash.'</p>';
}

$objLink = new link($this->uri(array('action' => 'configleftblocks')));
$objLink->link = $lnConfigureBlocks;
$str .= '<p><br />'.$objLink->show().'</p>';

echo $str;
?>
