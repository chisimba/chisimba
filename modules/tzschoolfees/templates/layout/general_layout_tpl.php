<?php


/**
 * @Author john richard
 * @Copyright (c) UCC
 * @Version 1.0
 * @Package smis fee module
 */

//load css layout class
$this->loadClass('csslayout','htmlelements');

$lef_nav = '<ul><li><a href="?module=tzschoolfees">Home</a></li>';
$lef_nav .= '<li><a href="?module=tzschoolfees&action=add_details">Add Payment Details</a></li>';
$lef_nav .= '<li><a href="?module=tzschoolfees&action=view_details">View Payment Details</a></li>';
$lef_nav .= '<li><a href="?module=tzschoolfees&action=generate_receipt">Generate receipt</a></li></ul>';

//$cssLayout = $this->newObject('csslayout','htmlelements');
$cssLayout = $this->newObject('csslayout','htmlelements');
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($lef_nav);
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>
