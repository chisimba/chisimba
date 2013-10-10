<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$headern = new htmlHeading();
$headern->str = $this->objLanguage->languageText('mod_qrreview_prodnotfound', 'qrreview');
$headern->type = 1;

$homelink = new href($this->uri('', 'qrreview'),$this->objLanguage->languageText("mod_qrreview_home", "qrreview"));

$middleColumn .= $headern->show();
$middleColumn .= $homelink->show();

$leftColumn .= $this->leftMenu->show();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
