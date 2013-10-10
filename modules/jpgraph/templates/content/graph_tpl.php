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
$header->str = $this->objLanguage->languageText('mod_jpgraph_graphheader', 'jpgraph');
$header->type = 1;

$middleColumn .= $header->show();

$src = $this->objGraphOps->drawGraph($graph);

$middleColumn .= '<img src="'.$src.'"/>';


$leftColumn .= $this->leftMenu->show();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
