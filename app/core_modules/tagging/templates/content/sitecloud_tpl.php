<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

// Set columns to 1
$cssLayout->setNumColumns(1);
$leftMenu = NULL;
$rightSideColumn = NULL;
$leftCol = NULL;
$middleColumn = NULL;

$middleColumn .= $objFeatureBox->show($this->objLanguage->languageText("mod_tagging_sitecloud", "tagging"), $cloud);

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>