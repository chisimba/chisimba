<?php
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$uxcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('js/ux/css/ColumnNodeUI.css','speak4free').'"/>';
$uxjs = '<script language="JavaScript" src="'.$this->getResourceUri('js/ux/ColumnNodeUI.js','speak4free').'" type="text/javascript"></script>';
$contentadminjs = '<script language="JavaScript" src="'.$this->getResourceUri('js/contentadmin.js','speak4free').'" type="text/javascript"></script>';
$treeloadercss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/xml-tree-loader.css','speak4free').'"/>';
$treeloaderjs = '<script language="JavaScript" src="'.$this->getResourceUri('js/ux/XmlTreeLoader.js','speak4free').'" type="text/javascript"></script>';
$treecolumncss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/column-tree.css','speak4free').'"/>';


$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $uxcss);
$this->appendArrayVar('headerParams', $uxjs);

$this->appendArrayVar('headerParams', $treeloadercss);
$this->appendArrayVar('headerParams', $treeloaderjs);
$this->appendArrayVar('headerParams', $treecolumncss);
$this->appendArrayVar('headerParams', $contentadminjs);
// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$leftSideColumn = 'Instructions';
$cssLayout->setLeftColumnContent($leftSideColumn);
$rightSideColumn='<div id="sections"></div>';

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
echo $cssLayout->show();

?>
