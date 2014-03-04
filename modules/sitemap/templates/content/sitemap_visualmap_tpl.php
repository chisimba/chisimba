<?php

$this->loadClass('treenode','tree');
$this->loadClass('treemenu','tree');
$this->loadClass('freemindmap','tree');

$this->setPageTemplate(NULL);
header('Content-type: application/xml');

$showModule = $this->getParam('showmodule', 'all');

if (!in_array($showModule, $modules)) {
    $showModule = 'all';
}

$menu = new treemenu();

if ($showModule != 'all') {
    $modules = array($showModule);
} else {
    $rootNode = new treenode(array('text'=>'Site Map'));
    $menu->addItem($rootNode);
}


foreach ($modules as $module)
{
    
    $modObject = $this->getObject('modulelinks_'.$module, $module);
    
    if ($showModule == 'all') {
        $rootNode->addItem($modObject->show());
    } else {
        $menu->addItem($modObject->show());
    }
    
    
}
$freemindmap  = &new freemindmap($menu);

    
    echo $freemindmap->getMenu();




?>