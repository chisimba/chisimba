<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_racemap_profileheader', 'racemap');
$header->type = 1;

// elevation heading
$headere = new htmlHeading();
$headere->str = $this->objLanguage->languageText('mod_racemap_elevationgraphheader', 'racemap');
$headere->type = 3;

// speed heading
$headers = new htmlHeading();
$headers->str = $this->objLanguage->languageText('mod_racemap_speedgraphheader', 'racemap');
$headers->type = 3;

$middleColumn .= $header->show();

$this->raceOps->profileMapFull($metaid, $first['lat'], $first['lon']);
$middleColumn .= '<div id="map" class="smallmap" style="width: 100%; height: 400px; float:left; border: 1px solid black;"></div>';
$middleColumn .= '<br /><br />';
$middleColumn .= $headere->show();
$middleColumn .= '<img src="'.$ele.'" />';
$middleColumn .= $headers->show();
$middleColumn .= '<img src="'.$speed.'" />';

$leftColumn .= $this->leftMenu->show();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
