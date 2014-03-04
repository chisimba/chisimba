<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

$objIcon = $this->newObject('geticon', 'htmlelements');
$tbl = $this->newObject('htmltable', 'htmlelements');
$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
$objRound =$this->newObject('roundcorners','htmlelements');
$objIcon->setIcon('templates_small', 'png', 'icons/cms/');



if(!isset($formId)) {
    $formId = $this->getParam('id');
	$formDetails = $this->objFormRecords->getRecord($formId);
}

if($formId != '')
{
	$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_forms_viewrecords', 'forms'). ' : ' . $formDetails['name'];	
}

$objLayer->str = $h3->show();
$objLayer->cssClass = 'headleft';
$header = $objLayer->show();

$objLayer->str = '';
$objLayer->cssClass = 'headclear';
$headShow = $objLayer->show();

$display = '<p>'.$header.$headShow.'</p><hr />';
//Show Header
$middleColumnContent = $display;
// Show Form

$middleColumnContent .= $formDisplay;

$this->setVar('middleContent', $middleColumnContent);


?>
