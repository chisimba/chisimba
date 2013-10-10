<?php
/******************** LAYOUT & DISPLAY ***********************/

//Create an instance of the css layout class
//$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Set columns to 2
//$cssLayout->setNumColumns(2);

//Add Left column
$this->lrsNav->addFromDb();

//$cssLayout->setLeftColumnContent($this->lrsNav->show());
//Add Right Column
//$cssLayout->setMiddleColumnContent($this->getContent());
//echo $cssLayout->show();

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->cellpadding = '10';
$objTable->startRow();
if (!isset($pageSuppressNav)) {
    $objTable->addCell($this->lrsNav->show(),'122px','top',null,'awardnav');
}
$objTable->addCell($this->getContent(),null,'top',null,'maincont','id = "maincontent"');
$objTable->endRow();

echo $objTable->show();

?>