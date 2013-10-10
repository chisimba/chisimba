<?php
/*
* Template for adding/editing an essay.
* @package essayadmin
*/

// set up html elements
//$objTable=$this->objTable;

$this->loadClass('htmltable','htmlelements');
$this->loadClass('layer','htmlelements');
$this->loadClass('textarea','htmlelements');

/*
// javascript
$javascript = "<script language=\"javascript\" type=\"text/javascript\">
function submitExitForm(){
    document.exit.submit();
}
</script>";
echo $javascript;
*/

// check if data array is empty, if not populate form
if (empty($data)) {
    $did='';
    $dtopic='';
    $dnotes='';
} else {
    $did=$data[0]['id'];
    $dtopic=$data[0]['topic'];
    $dnotes=$data[0]['notes'];
}

// Set up language items
$topic=$this->objLanguage->languageText('mod_essayadmin_topic','essayadmin');
$notes=$this->objLanguage->languageText('mod_essayadmin_notes','essayadmin');
$code=$this->objLanguage->languageText('mod_essayadmin_code','essayadmin');
$closeDate=$this->objLanguage->languageText('mod_essayadmin_closedate','essayadmin');
$save=$this->objLanguage->languageText('word_save');
$reset=$this->objLanguage->languageText('word_reset','Reset');
$exit=$this->objLanguage->languageText('word_cancel');
$help='mod_essayadmin_helpcreateessay';
$errEssay = $this->objLanguage->languageText('mod_essayadmin_enteressay');

//$head.=' '.$topic.': '.$topicname;
$heading .= '&nbsp;'.$this->objHelp->show($help);
//$this->setVar('heading',$heading);

$objTable = new htmltable();
//$objTable->border = '1';

// topic
$objTable->startRow();
$objTable->addCell('<b>'.$topic.':</b>','','','','','');
$objInput = new textinput('essaytopic',$dtopic, '', 70);
$objInput->extra='wrap="soft"';
$objTable->addCell($objInput->show(),'','','','',''); //right
$objTable->endRow();

// notes
$objTable->startRow();
$objTable->addCell('<b>'.$notes.':</b>','','','','','colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objText = new textarea('notes',$dnotes,3,70);
$objText->extra='wrap="soft"';
$objTable->addCell($objText->show(),'','','','','colspan="2"'); //right
$objTable->endRow();

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
$returnUrl = $this->uri(array('action' => 'view', 'id'=>$topicid));
$objButton->setOnClick("javascript: window.location='{$returnUrl}';");
/*
$objButton->setOnClick('javascript:submitExitForm();');
*/
$buttons .= '&nbsp;'.$objButton->show();

// Hidden elements

$hidden = '';

$objInput = new textinput('id',$topicid);
$objInput->fldType='hidden';
$hidden .= $objInput->show();

$objInput = new textinput('essay',$did);
$objInput->fldType='hidden';
$hidden .= $objInput->show();

// Form

$this->objForm = new form('essay',$this->uri(array('action'=>'saveessay')));
$this->objForm->addToForm($hidden);
$this->objForm->addToForm($objTable->show());
$this->objForm->addToForm($buttons);
$this->objForm->addRule('essaytopic',$errEssay, 'required');

// Layer

$objLayer = new layer();
//$objLayer->cssClass='odd';
$objLayer->str = $this->objForm->show();
echo $objLayer->show();

/*
// exit form
$objForm = new form('exit',$this->uri(array('action' => 'saveessay')));
$objInput = new textinput('save', $exit);
$objInput->fldType = 'hidden';
$objForm->addToForm($hidden);
$objForm->addToForm($objInput->show());
echo $objForm->show();
*/

?>