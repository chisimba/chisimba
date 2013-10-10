<?php
/*
* Template for adding / editing a topic.
* @package essayadmin
*/

// set up html elements
//$this->loadClass('htmlheading','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('layer','htmlelements');

// Set up language items
$topicArea=$this->objLanguage->languageText('mod_essayadmin_topicarea', 'essayadmin');
$description=$this->objLanguage->languageText('mod_essayadmin_description', 'essayadmin');
$instructions=$this->objLanguage->code2Txt('mod_essayadmin_instructions','essayadmin');
$closeDate=$this->objLanguage->languageText('mod_essayadmin_closedate','essayadmin');
$bypass=$this->objLanguage->languageText('mod_essayadmin_bypass','essayadmin');
$force=$this->objLanguage->code2Txt('mod_essayadmin_force','essayadmin');
$save=$this->objLanguage->languageText('word_save');
$reset=$this->objLanguage->languageText('word_reset');
$exit=$this->objLanguage->languageText('word_cancel');
$percentLbl=$this->objLanguage->languageText('mod_essayadmin_percentyrmark', 'essayadmin');
$errPercent=$this->objLanguage->languageText('mod_essayadmin_numericpercent');
$help=$this->objLanguage->LanguageText('help_essayadmin_overview_addtopic', 'essayadmin');
$errTopic = $this->objLanguage->languageText('mod_essayadmin_entertopic','essayadmin');

/*
// JavaScript
$javascript = "<script language=\"JavaScript\" type=\"text/javascript\">
function submitExitForm(){
    document.exit.submit();
}
</script >";
echo $javascript;
*/

$heading .= '&nbsp;'.$this->objHelp->show($help);

// Set up data, passed as a variable from controller
if(empty($data)){
    $did=NULL;
    $dTopic='';
    $dDescription='';
    $dInstructions='';
    $dDate=date('Y-m-d H:i:s');
    $dBypass='';
    $dForce='';
    $dPercent=0;
}else {
// put date in correct format
    $did=$data[0]['id'];
    $dTopic=$data[0]['name'];
    $dDescription=$data[0]['description'];
    $dInstructions=$data[0]['instructions'];
    $dDate=$data[0]['closing_date']; //$this->objDateformat->formatDate($data[0]['closing_date']);
    //echo "($dDate)";
    //echo gettype($data[0]['bypass']);
    //echo gettype($data[0]['forceone']);
    $dBypass=$data[0]['bypass'];
    $dForce=$data[0]['forceone'];
    $dPercent=$data[0]['percentage'];
}

$objTable = new htmltable();
//$objTable->border = '1';

// topic area
$objTable->startRow();
$objTable->addCell('<b>'.$topicArea.':</b>','','','','','');
//$objTable->endRow();
//$objTable->startRow();
$objInput = new textinput('topicarea', $dTopic, '', 70);
$objInput->extra='wrap="soft"';
$objTable->addCell($objInput->show(),'','','','','');
$objTable->endRow();

// topic description
$objTable->startRow();
$objTable->addCell('<b>'.$description.':</b>','','','','','colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objText = new textarea('description',$dDescription,3,70);
$objText->extra='wrap="soft"';
$objTable->addCell($objText->show(),'','','','','colspan="2"');
$objTable->endRow();

// learner instructions
$objTable->startRow();
$objTable->addCell('<b>'.$instructions.':</b>','','','','','colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objText = new textarea('instructions',$dInstructions,3,70);
$objText->extra='wrap="soft"';
$objTable->addCell($objText->show(),'','','','','colspan="2"');
$objTable->endRow();

// closing date
/*
$this->objInput = new textinput('timestamp', $dDate);
$this->objInput->extra = 'readonly=" readonly"';
$this->objIcon->setIcon('select_date');
$this->objIcon->alt=$this->objLanguage->languageText('mod_essayadmin_datepick','essayadmin');
*/

$objpopcal = $this->getObject('datepickajax','popupcalendar');
//$objpopcal->show('closing_date','yes','no',$dDate);

//$this->objessaydate = $this->newObject('datepicker','htmlelements');
//$name = 'closing_date';
//$date = date('Y-m-d');
//$format = 'YYYY-MM-DD';
//$this->objessaydate->setName($name);
//$this->objessaydate->setDefaultDate($date);
//$this->objessaydate->setDateFormat($format);
//$url = "javascript:show_calendar('document.topic.timestamp', document.topic.timestamp.value);";
//$url = $this->uri(array('action'=>'', 'field'=>'document.topic.timestamp', 'fieldvalue'=>$dDate, 'showtime'=>'no'), 'popupcalendar');
//$onclick = "javascript:window.open('" .$url."', 'popupcal', 'width="320", height="410", scrollbars="1", resize=yes')";
//$this->objLink = new link('#');
//$this->objLink->extra = "onclick=\"$onclick\"";
//$this->objLink->link = $this->objIcon->show();

// Force one essay per student
$objCheck = new checkbox('force','',$dForce=='1');
$fcheck=$objCheck->show();

$objCheck = new checkbox('bypass','',$dBypass=='1');
$bycheck=$objCheck->show();

//$objTable->row_attributes=' height="25"';
$objTable->startRow();
$objTable->addCell('<b>'.$closeDate.':</b>');
$objTable->addCell($objpopcal->show('closing_date','yes','no',$dDate));
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>'.$force.':</b>');
$objTable->addCell($fcheck);
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>'.$bypass.':</b>');
$objTable->addCell($bycheck);
$objTable->endRow();

$objTable->startRow();
$objDrop = new dropdown('percentage');
for($x=0; $x<=100; $x++){
    $objDrop->addOption($x, $x);
}
$objDrop->setSelected($dPercent);
$percent = $objDrop->show();
$objTable->addCell('<b>'.$percentLbl.':</b>');
$objTable->addCell($percent.'%');
$objTable->endRow();

if (is_null($did)) {
	$hidden='';
} else {
	$objInput = new textinput('id', $did);
	$objInput->fldType='hidden';
	$hidden = $objInput->show();
}

//$objTable->row_attributes=' height="10"';
//$objTable->startRow();
//$objTable->addCell($hidden);
//$objTable->endRow();

$buttons = '<br />';

$objButton = new button('save', $save);
$objButton->setToSubmit();
$buttons .= $objButton->show();

/*
$this->objInput = new textinput('reset',$reset);
$this->objInput->fldType='reset';
$this->objInput->setCss('button');
$buttons.='&nbsp;&nbsp;&nbsp;'.$this->objInput->show();
*/

$objButton = new button('exit', $exit);
if (is_null($did)) {
    $returnUrl = $this->uri(array());
} else {
    $returnUrl = $this->uri(array('action' => 'view', 'id'=>$did));
}
$objButton->setOnClick("javascript: window.location='{$returnUrl}';");
$buttons .= '&nbsp;'.$objButton->show();

$objForm = new form('topic',$this->uri(array('action'=>'savetopic')));
$objForm->addToForm($hidden);
$objForm->addToForm($objTable->show());
$objForm->addToForm($buttons);
$objForm->addRule('topicarea', $errTopic, 'required');
//$objForm->addRule('percentage', $errPercent, 'numeric');

// Layer

$objLayer = new layer;
//$objLayer->cssClass = 'odd';
$objLayer->str = $objForm->show();
echo $objLayer->show();

/*
// exit form -- Cancel button
$this->objForm = new form('exit',$this->uri(array('action' => 'savetopic')));
$this->objInput = new textinput('save', $exit);
$this->objInput->fldType = 'hidden';
$this->objForm->addToForm($this->objInput->show());
$this->objForm->addToForm($hidden);
echo $this->objForm->show();
*/

?>