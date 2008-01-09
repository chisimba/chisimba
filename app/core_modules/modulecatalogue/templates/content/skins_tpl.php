<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');

//set columns to 2
$cssLayout->setNumColumns(2);

//add left column
$cssLayout->setLeftColumnContent($this->objSideMenu->show($activeCat).$this->objTagCloud);

$this->appendArrayVar('headerParams',"<script type='text/javascript' src='core_modules/modulecatalogue/resources/remote.js'></script>");


//set middle content
$cssLayout->setMiddleColumnContent($this->objCatalogueConfig->skinRemoter($skins));

echo $cssLayout->show(); 

?>