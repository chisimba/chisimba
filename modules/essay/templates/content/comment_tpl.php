<?php
/*
* Template to display essay notes or marker's comment.
* @package essay
*/

$this->setVar('pageSuppressIM', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);

$objHeading = $this->newObject('htmlheading','htmlelements');
$objHeading->type = 3;
$objHeading->str = $topic;
echo $objHeading->show();

$objLayer1 = $this->newObject('layer','htmlelements');
$objLayer1->border = '1px solid; border-bottom: 0';
$objLayer1->str = '<font size="2"><b>'.$heading.'</b></font>';

$objLayer2 = $this->newObject('layer','htmlelements');
$objLayer2->align = 'justify';
$objLayer2->border = '1px solid; border-top: 0';
$objLayer2->str = $comment;

$objLayer3 = $this->newObject('layer','htmlelements');
$objLayer3->align = 'center';
$objLayer3->border = '1px solid; border-top: 0';
$objIcon = $this->newObject('geticon','htmlelements');
$objIcon->setIcon('close');
$objIcon->extra = "onclick='javascript: window.close();'";
$objLayer3->str = $objIcon->show();

$objLayer = $this->newObject('layer','htmlelements');
$objLayer->cssClass = 'content';
$objLayer->border = '1px solid';
$objLayer->str =
    $objLayer1->show()
    .$objLayer2->show()
    .$objLayer3->show();

echo $objLayer->show();
?>