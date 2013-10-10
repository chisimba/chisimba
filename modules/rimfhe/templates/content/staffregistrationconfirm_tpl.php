<?php
// Thi template displays the confirmation of registered staff members

$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('word_text', 'rimfhe', 'Registration Confirmation.');

$objLayer->str = $h3->show();
$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$header = $objLayer->show();

$display = '<p>'.$header.'</p><hr />';

//Show Header
echo $display;

echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_rimfhe_confirmregistration', 'rimfhe').'</div>';
?>
