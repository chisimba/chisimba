<?php
/**
* Template for adding and editing worksheets.
* @package worksheetadmin
*/

/**
* @param $sheet Worksheet information for editing
* @param $mode Either add or edit
* @param $nodes The context nodes
*/

$this->setLayoutTemplate('worksheetadmin_layout_tpl.php');

// set up html elements
$objHeading = $this->getObject('htmlheading', 'htmlelements');
$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable2 =$objTable;
$objIcon = $this->newObject('geticon', 'htmlelements');
$objLayer = $this->newObject('layer','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->popUp = $this->getObject('datepickajax','popupcalendar');




// set up language items
$worksheet = $objLanguage->languageText('mod_worksheetadmin_worksheet','worksheetadmin');
$editHead=$objLanguage->languageText('word_edit').' '.$worksheet;
$addHead=$objLanguage->languageText('mod_worksheetadmin_add','worksheetadmin').' '
.$objLanguage->languageText('word_new').' '.$worksheet;
$nameLabel=$objLanguage->languageText('mod_worksheetadmin_nameof','worksheetadmin').' '.$worksheet;
$chapterLabel=$objLanguage->languageText('mod_worksheetadmin_contentchapter','worksheetadmin');
$example=$objLanguage->languageText('mod_worksheetadmin_example','worksheetadmin');
$statusLabel=$objLanguage->languageText('mod_worksheetadmin_activitystatus','worksheetadmin');
$notActLabel=$objLanguage->languageText('mod_worksheetadmin_activityinactive','worksheetadmin');

$openLabel=$objLanguage->languageText('mod_worksheetadmin_activityopen','worksheetadmin');
$closedLabel=$objLanguage->languageText('mod_worksheetadmin_activityclosed','worksheetadmin');
$markedLabel=$objLanguage->languageText('mod_worksheetadmin_activitymarked','worksheetadmin');
$percentLabel=$objLanguage->languageText('mod_worksheetadmin_percentageoffinalmark','worksheetadmin');
$dateLabel=$objLanguage->languageText('mod_worksheetadmin_closingdate','worksheetadmin');
$selectLabel=$objLanguage->languageText('mod_worksheetadmin_selectdate','worksheetadmin');
$descriptionLabel=$objLanguage->languageText('mod_worksheetadmin_description','worksheetadmin');
$saveLabel=$objLanguage->languageText('word_save').' '.$worksheet;
$exitLabel=$objLanguage->languageText('word_cancel');

$errName = $objLanguage->languageText('mod_worksheetadmin_entername','worksheetadmin');
$errPercent = $objLanguage->languageText('mod_worksheetadmin_percentnumeric','worksheetadmin');
$errDate = $objLanguage->languageText('mod_worksheetadmin_errdate','worksheetadmin');

// exit form - javascript
$javascript = "<script language=\"javascript\" type=\"text/javascript\">
    function submitExitForm(){
        document.exit.submit();
    }
</script>";

echo $javascript;

if ($mode == 'edit') {
    $heading=$editHead.': '.$sheet['name'];
    $paramArray=array('action'=>'updateworksheet');
} else {
    $heading=$addHead;
    $paramArray=array('action'=>'addworksheet');
}
$this->setVarByRef('heading',$heading);

$formAction = $this->uri($paramArray);
$objForm = new form('tbl_worksheet');
//Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
//Set the displayType to 3 for freeform
$objForm->displayType=3;

if($mode == 'edit'){
    $wsName = $sheet['name'];
    $wsChapter = $sheet['chapter'];
    $wsPercent = $sheet['percentage'];
    $wsStatus = $sheet['activity_status'];
    $wsDate = $sheet['closing_date'];
    $wsDescription = $sheet['description'];
    $wsId = $sheet['id'];
}else{
    $wsName = '';
    $wsChapter = '';
    $wsPercent = 0;
    $wsDate = date('Y-m-d');
    $wsDescription = '';
}

    //var_dump($wsDate);

$objTable->cellpadding='5';
$objTable->cellspacing='2';
$objTable->width='100%';
//$objTable->attributes='align="center"';

$objTable->startRow();

$label = new label($nameLabel.':', 'input_worksheet_name');
$objTable->addCell($label->show(), '25%');

$objElement = new textinput ('worksheet_name', $wsName);
$objElement->size = 70;
$objForm->addRule('worksheet_name', $errName, 'required');

$objTable->addCell($objElement->show());

$objTable->endRow();

$objTable->startRow();

$label = new label($chapterLabel.':', 'input_chapter');
$objTable->addCell($label->show());

$objElement = new dropdown('chapter');
$objElement->addFromDB($nodes,'chapter_title','chapter_id');
$objElement->setSelected($wsChapter);
$objElement->label='User list';
$objTable->addCell($objElement->show());

$objTable->endRow();

$objTable->startRow();

$label = new label($statusLabel.':', 'input_activity_status');
$objTable->addCell($label->show());

if ($mode == 'edit') {
    $objElement = new radio('activity_status');
    $objElement->addOption('inactive',$notActLabel);
    $objElement->addOption('open',$openLabel);
    $objElement->addOption('closed',$closedLabel);
    $objElement->addOption('marked',$markedLabel);

    $objElement->setSelected($wsStatus);
    $objElement->setBreakSpace('<br />');
    $active = $objElement->show();
} else {
		//$objElement->setSelected('inactive');
    	$active = $notActLabel;
}
$objTable->addCell($active);
$objTable->endRow();

$objTable->startRow();

$label = new label($percentLabel.':', 'input_percentage');
$objTable->addCell($label->show());

$objElement = new dropdown('percentage');
for($x=0; $x<=100; $x++){
    $objElement->addOption($x, $x);
}
$objElement->setSelected($wsPercent);
$objTable->addCell($objElement->show().'&nbsp;%');

$objTable->endRow();

$objTable->startRow();

$label = new label($dateLabel.':', 'input_closing_date');
$objTable->addCell($label->show());

//$objElement = new textinput ('closing_date', $wsDate);
//$objElement->extra = 'READONLY = "readonly"';

//$objIcon->setIcon('select_date');
//$objIcon->title=$selectLabel;

// $url = "javascript:show_calendar('document.tbl_worksheet.closing_date', document.tbl_worksheet.closing_date.value);";

//$url = $this->uri(array('action'=>'', 'field'=>'document.tbl_worksheet.closing_date', 'fieldvalue'=>$wsDate), 'popupcalendar');
//$onclick = "javascript:window.open('" .$url."', 'popupcal', 'width=320, height=410, scrollbars=1, resize=yes')";

//$selectDateLink = new link('#');
//$selectDateLink->extra = "onclick=\"$onclick\"";
//$selectDateLink->link = $objIcon->show().' '.$selectLabel;

//$name = 'closing_date';
//$date = date('Y-m-d');
//$format = 'YY-MM-DD';
//$this->wsDate->setName($name);
//$this->wsDate->setDefaultDate($date);
//$this->wsDate->setDateFormat($format);

//$objTable->addCell($this->wsDate->show());
$dateField = $this->popUp->show('closing_date', 'yes', 'no', $wsDate);
$objTable->addCell($dateField);
//$objTable->addCell($objElement->show().'&nbsp;&nbsp;&nbsp;'.$this->wsDate->show());

$objTable->endRow();


$objTable->startRow();

$label = new label($descriptionLabel.':', 'input_description');
$objTable->addCell($label->show());

$objElement = new textarea ('description', $wsDescription);
$objTable->addCell($objElement->show());

$objTable->endRow();

$objTable->startRow();

$submitButton = new button('save', $saveLabel);
$submitButton->setIconClass("save");
$submitButton->setToSubmit();
$btnSave = $submitButton->show();
$exitBtn = new button('cancel', $exitLabel);
$exitBtn->setIconClass("cancel");
$exitBtn->setOnClick('javascript:submitExitForm()');
$btnCancel = $exitBtn->show();

$objTable->addCell($btnSave,'','','right');
$objTable->addCell('&nbsp;&nbsp;&nbsp;&nbsp;'.$btnCancel,'','','left');

$objTable->endRow();

$objForm->addToForm($objTable->show());

$hidden = '';
if ($mode == 'edit') {
    $objElement = new textinput ('id', $wsId);
    $objElement->fldType = 'hidden';
    $objForm->addToForm($objElement->show());
    $hidden .= $objElement->show();
}

// Add Context
$objElement = new textinput ('context');
$objElement->fldType = 'hidden';
$objElement->value = $contextCode;

$objForm->addToForm($objElement->show());
$hidden .= $objElement->show();

$objLayer->cssClass='even';
$objLayer->align='left';
$objLayer->str=$objForm->show();

echo $objLayer->show();

// exit form
$objForm = new form('exit', $formAction);
$objForm->addToForm($hidden);

$objInput = new textinput('cancel', $exitLabel);
$objInput->fldType = 'hidden';

$objForm->addToForm($objInput->show());
echo $objForm->show();
?>