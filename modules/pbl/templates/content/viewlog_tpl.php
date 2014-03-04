<?php
/**
* Template to view the pbl chat log
* @package pbl
*/

/**
* Template to view the pbl chat log
*/

// Suppress Page Variables
//$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
//$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$bodyParams='class="container" '; 
$this->setVarByRef('bodyParams',$bodyParams);

// set up html elements
$objHead = $this->newObject('htmlheading','htmlelements');
$objLayer = $this->newObject('layer','htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');
$this->loadClass('htmltable', 'htmlelements');

$lbView = $this->objLanguage->languageText('phrase_viewpbllog');

/*************** set up chat data ****************/

$chat = $this->dbchat->getChat();

$objTable = new htmltable();
$objTable->row_attributes=' height="15"';

$i=0;
if($chat){
    foreach($chat as $line){
        $class = (($i++ % 2) == 0) ? 'even':'odd';
        $objTable->startRow();
        $objTable->addCell('&nbsp;['.$i.']','10%','','',$class);
        $objTable->addCell($line['msg'],'','','',$class);
        $objTable->endRow();
    }
}

/**************** set up display page ********************/

$objIcon->setIcon('close');
$objIcon->extra=" onclick='javascript:window.close()'";

$objHead->type=3;
$objHead->str = $lbView.'&nbsp;&nbsp;'.$objIcon->show();

echo $objHead->show();

$objLayer->align='justify';
$objLayer->border='1px solid;';
$objLayer->str = $objTable->show();
$layer2 = $objLayer->show();

$objLayer->align='center';
$objLayer->str=$objIcon->show();
$layer3 = $objLayer->show();

$objLayer->cssClass='content';
$objLayer->border='1px solid';
$objLayer->align='center';
$objLayer->str=$layer2.'<br />'.$layer3;

echo $objLayer->show();
?>