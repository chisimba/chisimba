<?php

$this->loadClass('htmlheading', 'htmlelements');

$showModule = $this->getParam('showmodule', 'all');

if (!in_array($showModule, $modules)) {
    $showModule = 'all';
}

$objFreeMind = $this->getObject('flashfreemind', 'files');
$objFreeMind->setMindMap($this->uri(array('action'=>'visualmap', 'showmodule'=>$showModule)));

if ($showModule == 'all') {
    $objFreeMind->startCollapsedToLevel = 1;
    
    $htmlHeading = new htmlheading();
    $htmlHeading->type = 1;
    $htmlHeading->str = $this->objLanguage->languageText('mod_sitemap_allmodules', 'sitemap');
    echo $htmlHeading->show();
} else {
    $objFreeMind->startCollapsedToLevel = 2;
    
    $htmlHeading = new htmlheading();
    $htmlHeading->type = 1;
    $htmlHeading->str = $this->objLanguage->languageText('phrase_sitemap', 'sitemap').' - '.$this->objLanguage->languageText('mod_'.$showModule.'_name', $showModule);
    echo $htmlHeading->show();
}

echo $this->generateDropdownNavigation($modules, $showModule, 'visual');

echo $objFreeMind->show();

?>