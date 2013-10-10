<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
 * This form will list all the forms
 */

$arrForms = $this->objForms->getForms();
$topNav = $this->objUi->topNav('viewforms');

$middleColumnContent = '';

//initiate objects
$table =  $this->newObject('htmltable', 'htmlelements');
$objH = $this->newObject('htmlheading', 'htmlelements');
$link =  $this->newObject('link', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
$this->loadClass('form', 'htmlelements');
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

$objIcon = $this->newObject('geticon', 'htmlelements');
$tbl = $this->newObject('htmltable', 'htmlelements');
$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
//$objRound =$this->newObject('roundcorners','htmlelements');
$objIcon->setIcon('forms_small', 'png', 'icons/cms/');
$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_forms_form', 'forms');

$objLayer->str = $h3->show();
$objLayer->cssClass = 'headleft';
$header = $objLayer->show();

$objLayer->str = $topNav;
$objLayer->cssClass = 'headright';
$header .= $objLayer->show();

$objLayer->str = '';
$objLayer->cssClass = 'headclear';
$headShow = $objLayer->show();

$display = '<p>'.$header.$headShow.'</p><hr />';
//Show Header
$middleColumnContent .= $display;
// Show Form

//Get Selectall js
print $this->getJavascriptFile('selectall.js');
//echo $objRound->show($header.$headShow);//$tbl->show());
echo $objLayer->show();//$tbl->show());
//get the sections

$txt_task = new textinput('task',null,'hidden');
//$objCheck->setOnClick("javascript:SetAllCheckBoxes('document.getElementById('form_select')'), 'arrayList[]', true);"); 

$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

//setup the table headings
$table->startHeaderRow();
//$table->addHeaderCell("<input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"javascript:ToggleCheckBoxes('select', 'arrayList[]', 'toggle');\" />" . " " . $this->objLanguage->languageText("mod_cms_selectall", "forms"));
//$table->addHeaderCell($this->objLanguage->languageText('word_image', 'system'));
$table->addHeaderCell($this->objLanguage->languageText('word_title', 'system'), '', 'top', 'left', '', 'style="text-align:left"');
$table->addHeaderCell($this->objLanguage->languageText('word_description'), '', 'top', 'left', '', 'style="text-align:left"');
$table->addHeaderCell($this->objLanguage->languageText('word_publish', 'system'), '', 'top', 'left', '', 'style="text-align:left"');
$table->addHeaderCell($this->objLanguage->languageText('word_options'), '', 'top', 'left', '', 'style="text-align:left"');
$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
if (count($arrForms) > 0) {
	foreach($arrForms as $form) {
	    //Set odd even row colour
	    $oddOrEven = ($rowcount == 0) ? "even" : "odd";
		
        //$formThumb = '<img src="'.$form['image'].'" width="100px" height="70px"/>';

		//Set up select form
		$objCheck = new checkbox('arrayList[]');
		$objCheck->setValue($form['id']);
		$objCheck->extra = "onclick=\"javascript: ToggleMainBox('select', 'toggle', this.checked);\"";
		
	    //publish, visible
	    if($form['published']){
	       $url = $this->uri(array('action' => 'formpublish', 'id' => $form['id'], 'mode' => 'unpublish'));
	       $icon = $this->objUi->getCheckIcon(TRUE);
	    }else{
	       $url = $this->uri(array('action' => 'formpublish', 'id' => $form['id'], 'mode' => 'publish'));
	       $icon = $this->objUi->getCheckIcon(FALSE);
	    }
	    $objLink = new link($url);
	    $objLink->link = $icon;
	    $visibleLink = $objLink->show();
	
	    //Create delete icon
        //TODO: Enable Security
		//if ($this->_objSecurity->canUserWriteSection($form['id'])){
		    $delArray = array('action' => 'deleteform', 'confirm'=>'yes', 'id'=>$form['id']);
		    $deletephrase = $this->objLanguage->languageText('mod_forms_confirmdelform', 'forms');
		    $delIcon = $objIcon->getDeleteIconWithConfirm($form['id'], $delArray,'forms',$deletephrase);
		//} else {
		//	$delIcon = '';
		//}
		
	    
	    //edit icon
        //TODO: Enable Security
		//if ($this->_objSecurity->canUserWriteSection($form['id'])){
	    	$editIcon = $objIcon->getEditIcon($this->uri(array('action' => 'addform', 'id' => $form['id'])));
		//} else {
		//	$editIcon = '';
		//}

        //Preview Icon	    
        $objIcon->title = "Preview";
        $viewIcon = $objIcon->getViewIcon('javascript::void(0)', 'filter_'.$form['id']);

        //Setting up the Filter Code Display Box
        $innerHtml = $this->objUi->getFilterCodeForm($form['id']);
        $this->objBox->setHtml($innerHtml);
        $this->objBox->setTitle('Filter Code');
        $this->objBox->attachClickEvent('filter_'.$form['id']);

        //View Records Icon
        $objIcon->title = "View Records";
        $objIcon->setIcon('addsibling');

	    $objLink = new link('?module=forms&action=viewrecords&id='.$form['id']);
	    $objLink->link = $objIcon->show();

        $recordsIcon = $objLink->show();

	    $tableRow = array();
	    //$tableRow[] = $objCheck->show();
        //$tableRow[] = $formThumb;
	    $tableRow[] = html_entity_decode($form['title']);
        $tableRow[] = $form['description'];
	    $tableRow[] = $visibleLink;
        
        //TODO: enable security at form node level
        /*
        if (!$this->_objSecurity->canUserWriteSection($form['id'])){
            $editIcon = '';
            $deleteIcon = '';
            $visibleLink = '';
        }
        */

        $tableRow[] = '<nobr>'.$editIcon.$delIcon.$viewIcon.$recordsIcon.'</nobr>';

	    $table->addRow($tableRow, $oddOrEven);
	
	    $rowcount = ($rowcount == 0) ? 1 : 0;

	}
    $noRecords = false;
}else{
    echo  '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_forms_noformsfound', 'forms').'</div>';
	$noRecords = true;
}


//Link to switch between root nodes and all nodes
$objViewAllLink =& $this->newObject('link', 'htmlelements');
if (isset($viewType)) {
    if($viewType == 'root') {
        $objViewAllLink->link = $this->objLanguage->languageText('mod_forms_viewsummaryallsections', 'forms');
        $objViewAllLink->href = $this->uri(array('action' => 'sections', 'viewType' => 'all'), 'forms');
    } else {
        $objViewAllLink->link = $this->objLanguage->languageText('mod_forms_viewrootsectionsonly', 'forms');
        $objViewAllLink->href = $this->uri(array('action' => 'sections', 'viewType' => 'root'), 'forms');
    }
}
//Create new section link
$objAddSectionLink =& $this->newObject('link', 'htmlelements');
$objAddSectionLink->href = $this->uri(array('action' => 'addsection'), 'forms');
$objAddSectionLink->link = $this->objLanguage->languageText('mod_forms_createnewsection', 'forms');

$frm_select = new form('select', $this->uri(array('action' => 'select'), 'forms'));
$frm_select->id = 'select';

$objLayer = new layer();
$objLayer->id = 'formListTable';
$objLayer->str = $table->show();

$frm_select->addToForm($objLayer->show());
$frm_select->addToForm($txt_task->show());

if (!$noRecords) {
    $middleColumnContent .= $frm_select->show();
}

$middleColumnContent .= '&nbsp;'.'<br/>';

$objFormsTree =$this->newObject('simpletreemenu', 'forms');

$this->setVar('leftContent', $objFormsTree->show());
$this->setVar('middleContent', $middleColumnContent);

?>
