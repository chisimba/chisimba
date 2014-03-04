<?php
// Thi template displays the confirmation of registered staff members

$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('mod_rimfhe_dataentryerror', 'rimfhe');

$objLayer->str = $h3->show();
$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$header = $objLayer->show();

$display = '<p>'.$header.'</p><hr />';

//Show Header
echo $display;

echo '<div class="noRecordsMessage">'.$title.'</div>';
?>
