<?php

$this->loadClass('treemenu','tree');
$this->loadClass('htmllist','tree');
$objIcon = $this->getObject('geticon', 'htmlelements');

$this->loadClass('link','htmlelements');
$this->loadClass('htmlheading','htmlelements');

$showModule = $this->getParam('showmodule', 'all');

if (!in_array($showModule, $modules)) {
    $showModule = 'all';
}

if ($showModule != 'all') {
    $htmlHeading = new htmlheading();
    $htmlHeading->type = 1;
    $htmlHeading->str = $this->objLanguage->languageText('phrase_sitemap', 'sitemap').' - '.$this->objLanguage->languageText('mod_'.$showModule.'_name', $showModule);
    echo $htmlHeading->show();
    
    
    $modulesList = array($showModule);
} else {
    $htmlHeading = new htmlheading();
    $htmlHeading->type = 1;
    $htmlHeading->str = $this->objLanguage->languageText('mod_sitemap_allmodules', 'sitemap');
    echo $htmlHeading->show();
    
    $modulesList = $modules;
}

echo $this->generateDropdownNavigation($modules, $showModule, 'text');

$counter = 0;

foreach ($modulesList as $module)
{
    $menu = new treemenu();
    $modObject = $this->getObject('modulelinks_'.$module, $module);
    $menu->addItem($modObject->show());
    
    $htmllist  = &new htmllist($menu);
    //echo '<div style="width:49%; float: left; overflow: hidden;">';
    
    $objIcon->setModuleIcon($module);
    $objIcon->extra = ' style="vertical-align: middle;"';
    
    $headingLink = new link ($this->uri(NULL, $module));
    $headingLink->link = $this->objLanguage->languageText('mod_'.$module.'_name', $module);
    
    $htmlHeading = new htmlheading();
    $htmlHeading->type = 3;
    $htmlHeading->str = $objIcon->show().' '.$headingLink->show();
    echo $htmlHeading->show();
    
    $result = $htmllist->getMenu();
    
    // Remove First <ul><li> - since this comes from the heading
    $result = preg_replace('/\\A<ul><li.*?><a.*?>.*?<\/a>/', '', $result, PREG_PATTERN_ORDER);
    // Remove the Last </li></ul> - since this comes from the heading
    $result = preg_replace('/(<\/li><\/ul>)\\z/', '', $result, PREG_PATTERN_ORDER);
    
    echo $result;
    $counter++;
    
    if ($counter < count($modulesList)) {
        echo '<hr style="width: 70%; height:2px; margin-left: 50px;" />';
    }
}





?>