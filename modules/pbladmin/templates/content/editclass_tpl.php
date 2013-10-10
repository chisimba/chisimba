<?php
/**
* @package pbladmin
*/

/*
* Template for PBL Admin: Page to add or edit classroom details.
* @param array $lecturers A list of lecturers in the course.
* @param array $cases A list of cases in the course.
* @param string $mode The current mode: edit/add
* @param array $class The details of the class being edited.
* @param array $students The List of students in the class.
*/

// set header params to contain javascript
// $headerParams=$this->getJavascriptFile('ts_picker.js','htmlelements');
// $headerParams.="<script>/*Script by Denis Gritcyuk: tspicker@yahoo.com
// Submitted to JavaScript Kit (http://javascriptkit.com)
// Visit http://javascriptkit.com for this script*/ </script>";
// $this->appendArrayVar('headerParams',$headerParams);

$this->setLayoutTemplate('admin_layout_tpl.php');

// set up html elements
$this->loadClass('htmltable','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('label','htmlelements');
$this->loadClass('radio','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');
$objHead = $this->newObject('htmlheading','htmlelements');
$objLayer = $this->newObject('layer','htmlelements');

// set up language items
$classname = $this->objLanguage->languageText('word_new');
$inlabel = $this->objLanguage->languageText('word_in');

$editLabel = $this->objLanguage->languageText('phrase_editclass');
$caseHead = $this->objLanguage->languageText('mod_pbladmin_createnewclass', 'pbladmin');
$classHead = $this->objLanguage->languageText('phrase_classroomdetails');
$studentLabel = ucwords($this->objLanguage->code2Txt('word_students'));
$dateLabel = $this->objLanguage->languageText('phrase_startdate');
$pickLabel = $this->objLanguage->languageText('mod_pbladmin_datepick', 'pbladmin');
$openLabel = $this->objLanguage->languageText('word_open');
$closedLabel = $this->objLanguage->languageText('word_closed');
$classNameLabel = $this->objLanguage->languageText('phrase_classname');
$noneLabel = $this->objLanguage->languageText('word_none');
$scribeLabel = $this->objLanguage->languageText('word_scribe');
$chairLabel = $this->objLanguage->languageText('word_chair');
$facilitatorLabel = $this->objLanguage->languageText('word_facilitator');
$caseLabel = $this->objLanguage->languageText('word_case');
$virtualLabel = $this->objLanguage->languageText('word_virtual');

$saveLabel = $this->objLanguage->languageText('word_save');
$exitLabel = $this->objLanguage->languageText('word_cancel');
$editStudentsLabel = $this->objLanguage->code2Txt('mod_pbladmin_saveandeditstudents', 'pbladmin');

if(!isset($mode)){
    $mode = 'add';
}

if($mode=='edit'){
    $heading = $editLabel;
    if(!empty($class)){
        $id = $class['id'];
        $name = $class['name'];
        $case = $class['caseid'];
        $facilitator = $class['facilitator'];
        $chair = $class['chair'];
        $scribe = $class['scribe'];
        $status = $class['status'];
        $date = $class['opentime'];
    }
}else{
    $heading = $caseHead;
    $name = '';
    $case = '';
    $facilitator = '';
    $chair = '';
    $scribe = '';
    $status = 'o';
    $date = date('Y-m-d H:i');
}

$this->setVarByRef('heading',$heading);

$objTable = new htmltable();
$objTable->cellpadding = "2";
$objTable->row_attributes = 'height="30"';

// Class name
$objLabel = new label('<b>'.$classNameLabel.'</b>', 'input_class');

$objInput = new textinput('class', $name);

$objTable->addRow(array($objLabel->show(), $objInput->show()));

// Case
$objLabel = new label('<b>'.$caseLabel.'</b>', 'input_case');

$objDrop = new dropdown('case');
$objDrop->addOption(Null,$noneLabel);
if(!empty($cases)){
    foreach($cases as $line){
        $objDrop->addOption($line['id'], $line['name']);
    }
    $objDrop->setSelected($case);
}else{
    $objDrop->addOption(Null,$noneLabel);
}
$objTable->addRow(array($objLabel->show(), $objDrop->show()));

// Facilitator
$objLabel = new label('<b>'.$facilitatorLabel.'</b>', 'input_facilitator');

$objDrop = new dropdown('facilitator');
$objDrop->addOption('virtual', $virtualLabel);
if(!empty($lecturers)){
    foreach($lecturers as $line){
        $objDrop->addOption($line['id'], $line['firstname'].' '.$line['surname']);
    }
    $objDrop->setSelected($facilitator);
}
$objTable->addRow(array($objLabel->show(), $objDrop->show()));

// Chair
$objLabel = new label('<b>'.$chairLabel.'</b>', 'input_chair');

$objDrop = new dropdown('chair');
$objDrop->addOption(Null,$noneLabel);
if($mode == 'edit'){
    if(!empty($students)){
        foreach($students as $line){
            $objDrop->addOption($line['id'], $line['name']);
        }
        $objDrop->setSelected($chair);
    }
}
$objTable->addRow(array($objLabel->show(), $objDrop->show()));

// Scribe
$objLabel->label('<b>'.$scribeLabel.'</b>', 'input_scribe');

$objDrop = new dropdown('scribe');
$objDrop->addOption(Null,$noneLabel);
if($mode == 'edit'){
    if(!empty($students)){
        foreach($students as $line){
            $objDrop->addOption($line['id'], $line['name']);
        }
        $objDrop->setSelected($scribe);
    }
}

$objTable->addRow(array($objLabel->show(), $objDrop->show()));

// Status
$objRadio = new radio('status');
$objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
$objRadio->addOption('o','<b>'.$openLabel.'</b>');
$objRadio->addOption('c','<b>'.$closedLabel.'</b>');
$objRadio->setSelected($status);

$objTable->startRow();
$objTable->addCell($objRadio->show(),'','','center','','colspan="2"');
$objTable->endRow();

// Start Date
$objLabel->label('<b>'.$dateLabel.'</b>', 'input_timestamp');

//$url = "javascript:show_calendar('document.addclass.timestamp', document.addclass.timestamp.value);";
/*
$url = $this->uri(array('action'=>'', 'field'=>'document.addclass.timestamp', 'fieldvalue'=>$date), 'popupcalendar');
$onclick = "javascript:window.open('" .$url."', 'popupcal', 'width=320, height=410, scrollbars=1, resize=yes')";

$objInput->textinput('timestamp', $date);
$objIcon->setIcon('select_date');
$objIcon->title = $pickLabel;
$objLink = new link('#');
$objLink->extra = "onclick=\"$onclick\"";
$objLink->link = $objIcon->show();
$dateIcon = $objLink->show();
*/

$objPopupcal = &$this->getObject('datepickajax', 'popupcalendar');
$dateField = $objPopupcal->show('timestamp', 'yes', 'no', $date);

$objTable->addRow(array($objLabel->show(), $dateField)); // objInput->show().$dateIcon));

// Hidden fields
$hidden = '';
if($mode == 'edit'){
    // class id
    $objInput = new textinput('id', $id, 'hidden');
    $hidden .= $objInput->show();
}
// mode
$objInput = new textinput('mode', $mode, 'hidden');
$hidden .= $objInput->show();

// Submit Buttons
$objButton = new button('save', $saveLabel);
$objButton->setToSubmit();
$objButton->setIconClass("save");
$btns = $objButton->show();

$objButton = new button('exit', $exitLabel);
$objButton->setIconClass("cancel");
$objButton->setToSubmit();
$btns .= '&nbsp;&nbsp;'.$objButton->show();

$objButton = new button('edit', $editStudentsLabel);
$objButton->setToSubmit('');
$objButton->setIconClass("edit");

$btns .= '&nbsp;&nbsp;'.$objButton->show();

$objTable->startRow();
$objTable->addCell($btns.$hidden,'','','center','','colspan="2"');
$objTable->endRow();

// Set up form and display
$objForm = new form('addclass', $this->uri(array('action'=>'saveclass')));
$objForm->addToForm($objTable->show());

$objHead->type = 4;
$objHead->str = $classHead;

$objLayer->cssClass = 'odd';
$objLayer->str = $objHead->show().$objForm->show();

$classLayer = $objLayer->show();

$objHead->type = 4;
$objHead->str = $studentLabel;

$str = '';
if(!empty($students)){
    foreach($students as $line){
        $str .= $line['name'].'<br />';
    }
}else{
    $str .= '<p>&nbsp;</p>';
}
$objLayer->str = $objHead->show().$str.'<p>&nbsp;</p>';
$objLayer->align = 'center';

$studentLayer = $objLayer->show();

$objTable2 = new htmltable();
$objTable2->startRow();
$objTable2->addCell($classLayer, '55%');
$objTable2->addCell('','5%');
$objTable2->addCell("<div class='outerwrapper'>$studentLayer</div>", '40%');
$objTable2->endRow();

echo $objTable2->show();
?>