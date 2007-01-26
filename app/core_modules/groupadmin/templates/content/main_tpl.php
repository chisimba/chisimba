<?php
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package groupadmin
* @subpackage template
* @version 0.1
* @since 22 November 2004
* @author Prince Mbekwa based on the methods by Jonathan Abrahams
* @filesource
*/
//$this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js','tree'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('domLib.js','htmlelements'));
$this->appendArrayVar('headerParams', $this->getJavascriptFile('domTT.js','htmlelements'));
//$this->appendArrayVar('headerParams', $this->getJavascriptFile('domTT_drag.js','htmlelements'));
//$this->appendArrayVar('headerParams', $this->getJavascriptFile('windowpopup.js','htmlelements'));

$info = isset($rightInfo)?$rightInfo->show():NULL;

// Abstract Path
foreach ($this->abstractionArray as $name=>$value)
{
    $fullPath = str_replace($name, $value, $fullPath);
}

?>

<script src="core_modules/tree/resources/TreeMenu.js" language="JavaScript" type="text/javascript"></script>
<script src="core_modules/groupadmin/resources/sorttable.js" language="JavaScript" type="text/javascript"></script>

<?
$objTable =& $this->newObject('htmltable', 'htmlelements');
$objHead =& $this->newObject('htmlheading', 'htmlelements');
$objLayer =& $this->newObject('layer', 'htmlelements');

$leftCol = $treeNav.$treeControls;
$rightCol = $info;

$objHead->str = $pageTitle.$lnkIcnCreate;
$objHead->type = 1;

$midCol = $objHead->show();
$midCol .= '<b>'.$this->objLanguage->languageText("mod_groupadmin_ttlSelectedGroup",'groupadmin').'</b>: '.$fullPath;

if( isset($groupId) ) {
    $objLayer->str = $nodeList;
    $objLayer->id = 'blog-content';
    $disStr = $objLayer->show();

    $objLayer->str = $nodeControls;
    $objLayer->id = 'blog-footer';
    $disStr .= $objLayer->show();

    $objLayer->str = $disStr;
    $objLayer->id = 'blog';
    $blogStr = $objLayer->show();
} else {
    $objLayer->str = $objLanguage->languageText('mod_groupadmin_hlpGroupAdmin','groupadmin');
    $objLayer->id = 'nodeInfo';
    $blogStr = $objLayer->show();
}

$objLayer->str = $blogStr;
$objLayer->id = 'treecontent';
$objLayer->border = '0; margin:0';
$midCol .= $objLayer->show();

$objTable->width='100%';
$objTable->cellpadding=5;
$objTable->startRow();
$objTable->addCell($leftCol, '200');
$objTable->addCell($midCol, '', 'top');
if(!is_null($rightCol)){
    $objTable->addCell($rightCol, '20%');
}
$objTable->endRow();
echo $objTable->show();
