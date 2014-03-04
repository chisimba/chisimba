<?php
/**
 * This template will list all the sections 
 */

//initiate objects
$table =  $this->newObject('htmltable', 'htmlelements');
$objH = $this->newObject('htmlheading', 'htmlelements');
$link =  $this->newObject('link', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$objRound =$this->newObject('roundcorners','htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');

$selectbutton=$this->newObject('button','htmlelements'); 
$selectbutton->setOnClick("javascript:SetAllCheckBoxes('SelectAll', 'arrayList[]', true);"); 
$selectbutton->setValue('Select All');
$selectbutton->setToSubmit(); 

$tbl = $this->newObject('htmltable', 'htmlelements');
$tbl->cellpadding = 3;
$tbl->align = "left";

//create a heading
$objH->type = '3';


//Create the filter form
    
    //Filter objects
	$lbl_filter = new label($this->objLanguage->languageText('mod_cmsadmin_filter', 'cmsadmin').': ','input_txtfilter');
	$txt_filter = new textinput('txtfilter',null,null,20);
	$filterStr = $lbl_filter->show().$txt_filter->show();
	
	$filter_submit = new button('save', $this->objLanguage->languageText('word_go'));
	$filter_submit->id = 'save';
    $filter_submit->setToSubmit();
    $filterStr .= '&nbsp;'.$filter_submit->show();
    
    $task = new textinput('task',null,'hidden');
    $taskStr = $task->show();
   
	$filter_reset = new button('reset', $this->objLanguage->languageText('word_reset'));
	$filter_reset->id = 'reset';
	$filter_reset->setOnClick("javascript:document.getElementById('input_txtfilter').value='';");
	$filter_reset->setToSubmit();
	$filterStr .= '&nbsp;'.$filter_reset->show();
   
    
	$drp_filter = new dropdown('drp_filter');
	$drp_filter->addOption('', '-'.$this->objLanguage->languageText('phrase_selectstate').'-');
	$drp_filter->addOption('any', $this->objLanguage->languageText('word_any'));
	$drp_filter->addOption('published', $this->objLanguage->languageText('word_published'));
	$drp_filter->addOption('unpublished', $this->objLanguage->languageText('word_unpublished'));
	$drp_filter->extra = "onchange=\"javascript: if (document.getElementById('input_drp_filter').options[selectedIndex].value != ''){submitbutton('filter','filter');}\"";
	$drp_filter->setSelected('');
	$dropStr = $drp_filter->show();
	
	//Setup filter display
	$tbl_filter = new htmltable();
	$tbl_filter->startRow();
	$tbl_filter->addCell($filterStr);
	$tbl_filter->addCell($dropStr,null,null,'right');
	$tbl_filter->endRow();
	
	//Set up filter form
	$frm_filter = new form('filter', $this->uri(array('action' => 'filter'), 'cmsadmin'));
    $frm_filter->id = 'filter';
	$frm_filter->addToForm($taskStr);
    $frm_filter->addToForm($tbl_filter->show());
	$filterTable = $frm_filter->show();
	
    
//counter for records
$cnt = 1;
//Heading box
$objIcon->setIcon('section_small', 'png', 'icons/cms/');
$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_sectionmanager', 'cmsadmin');
$objH->str =  $objIcon->show().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_sectionmanager', 'cmsadmin');
$tbl->startRow();
$tbl->addCell($objH->show(), '', 'center');
$tbl->addCell($topNav, '','center','right');
$tbl->endRow();


$objLayer->str = $objH->show();
//$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_left';
$header = $objLayer->show();

$objLayer->str = $topNav;
//$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_right';
$header .= $objLayer->show();

$objLayer->str = '';
//$objLayer->border = '; clear:both; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_clear';
$objLayer->cssClass = 'clearboth';
$headShow = $objLayer->show();

$objLayer->str = $header.$headShow;
$objLayer->id = 'cms_main';

//Get Selectall js
print $this->getJavascriptFile('selectall.js');
//echo $objRound->show($header.$headShow);//$tbl->show());
echo $objLayer->show();//$tbl->show());
//get the sections

//Get cms type
$cmsType = 'treeMenu';
//set up select
// Buttons to Select All


$txt_task = new textinput('task',null,'hidden');
//$objCheck->setOnClick("javascript:SetAllCheckBoxes('document.getElementById('form_select')'), 'arrayList[]', true);"); 

$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

//setup the table headings
$table->startHeaderRow();
$table->addHeaderCell("<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"javascript:ToggleCheckBoxes('select', 'arrayList[]', 'toggle');\" />" . " " . $this->objLanguage->languageText("mod_cms_selectall", "cmsadmin"));
$table->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_menuname', 'cmsadmin'));
$table->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_nameofsection', 'cmsadmin'));
$table->addHeaderCell($this->objLanguage->languageText('word_pages'));
$table->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_displaytype', 'cmsadmin'));
$table->addHeaderCell($this->objLanguage->languageText('word_order'));
$table->addHeaderCell($this->objLanguage->languageText('word_published'));
$table->addHeaderCell($this->objLanguage->languageText('word_options'));
$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
if (is_array($arrSections)) {
	foreach($arrSections as $section) {
	    //Set odd even row colour
	    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
	    if($viewType == 'all') {
	        $pref = "";
	        $matches = split('<', $section['title']);
	        $img = split('>', $matches[1]);
	        $image = '<'.$img[0].'>';
	        $linkText = $img[1];
	        $noSpaces = strlen($matches[0]);
	
	        for ($i = 1; $i < $noSpaces; $i++) {
	            $pref .= '&nbsp;&nbsp;';
	        }
	        $pref .= $image;
	
	        $section = $this->_objSections->getSection($section['id']);
	        //View section link
	        $link->link = $linkText;
	        $link->href = $this->uri(array('action' => 'viewsection', 'id' => $section['id']));
	        $viewSectionLink = $pref.$link->show();
	    } else {
	        $link->link = $section['menutext'];
	        $link->href = $this->uri(array('action' => 'viewsection', 'id' => $section['id']));
	        $viewSectionLink = $link->show();
	    }
		
		//Set up select form
		$objCheck = new checkbox('arrayList[]');
		$objCheck->setValue($section['id']);
		$objCheck->extra = "onclick=\"javascript: ToggleMainBox('select', 'toggle', this.checked);\"";
		
	    //publish, visible
	    if($section['published']){
	       $url = $this->uri(array('action' => 'sectionpublish', 'id' => $section['id'], 'mode' => 'unpublish'));
	       $icon = $this->_objUtils->getCheckIcon(TRUE);
	    }else{
	       $url = $this->uri(array('action' => 'sectionpublish', 'id' => $section['id'], 'mode' => 'publish'));
	       $icon = $this->_objUtils->getCheckIcon(FALSE);
	    }
	    $objLink = new link($url);
	    $objLink->link = $icon;
	    $visibleLink = $objLink->show();
	
	    //Create delete icon
		if ($this->_objSecurity->canUserWriteSection($section['id'])){
		    $delArray = array('action' => 'deletesection', 'confirm'=>'yes', 'id'=>$section['id']);
		    $deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelsection', 'cmsadmin');
		    $delIcon = $objIcon->getDeleteIconWithConfirm($section['id'], $delArray,'cmsadmin',$deletephrase);
		} else {
			$delIcon = '';
		}
		
	
	    //edit icon
		if ($this->_objSecurity->canUserWriteSection($section['id'])){
	    	$editIcon = $objIcon->getEditIcon($this->uri(array('action' => 'addsection', 'id' => $section['id'])));
		} else {
			$editIcon = '';
		}
	    
	
	    $tableRow = array();
	    $tableRow[] = $objCheck->show();
	    $tableRow[] = $viewSectionLink;
	    $tableRow[] = html_entity_decode($section['title']);
	    $tableRow[] = $this->_objContent->getNumberOfPagesInSection($section['id']);
	    $tableRow[] = $this->_objLayouts->getLayoutDescription($section['layout']);
	    $tableRow[] = $this->_objSections->getOrderingLink($section['id']);//$this->_objSections->getPageOrderType($section['ordertype']);

	    if (!$this->_objSecurity->canUserWriteSection($section['id'])){
		    $editIcon = '';
		    $deleteIcon = '';
		    $visibleLink = '';
	    }

	    $tableRow[] = $visibleLink;
	    $tableRow[] = '<nobr>'.$editIcon.$delIcon.'</nobr>';

	    $table->addRow($tableRow, $oddOrEven);
	
	    $rowcount = ($rowcount == 0) ? 1 : 0;

	}
	
}else{
	echo  '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_cmsadmin_nopagesfoundinthissection', 'cmsadmin').'</div>';
}


//Link to switch between root nodes and all nodes
$objViewAllLink =& $this->newObject('link', 'htmlelements');
if($viewType == 'root') {
    $objViewAllLink->link = $this->objLanguage->languageText('mod_cmsadmin_viewsummaryallsections', 'cmsadmin');
    $objViewAllLink->href = $this->uri(array('action' => 'sections', 'viewType' => 'all'), 'cmsadmin');
} else {
    $objViewAllLink->link = $this->objLanguage->languageText('mod_cmsadmin_viewrootsectionsonly', 'cmsadmin');
    $objViewAllLink->href = $this->uri(array('action' => 'sections', 'viewType' => 'root'), 'cmsadmin');
}
//Create new section link
$objAddSectionLink =& $this->newObject('link', 'htmlelements');
$objAddSectionLink->href = $this->uri(array('action' => 'addsection'), 'cmsadmin');
$objAddSectionLink->link = $this->objLanguage->languageText('mod_cmsadmin_createnewsection', 'cmsadmin');


$frm_select = new form('select', $this->uri(array('action' => 'select'), 'cmsadmin'));
$frm_select->id = 'select';
    
$frm_select->addToForm($table->show());
$frm_select->addToForm($txt_task->show());
//print out the page
$middleColumnContent = "<hr />";
$middleColumnContent .= $filterTable;
$middleColumnContent .= $frm_select->show();
$middleColumnContent .= '&nbsp;'.'<br/>';
$middleColumnContent .= "<hr />";
$middleColumnContent .= $objViewAllLink->show().'&nbsp;';//'/'.'&nbsp;'.$objAddSectionLink->show();
echo "<br><br><br><br><br><br><br>";
echo $middleColumnContent;

?>
