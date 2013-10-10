<?
/*
* Template to display the lecturers comment on an assignment.
* @package assignment
*/

/**
* @param array $data Array contains the assignment name and comments.
*/

// Suppress Default Page Settings
$this->setVar('pageSuppressContainer',TRUE);
$this->setVar('pageSuppressIM',TRUE);
$this->setVar('pageSuppressBanner',TRUE);
$this->setVar('pageSuppressToolbar',TRUE);
$this->setVar('suppressFooter',TRUE);

$name = $data[0]['name'];
$comment = $data[0]['comment'];

// set up html elements
$this->loadClass('htmlheading','htmlelements');
$this->loadClass('layer','htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');

// set up language items
$head = $this->objLanguage->languageText('mod_assignment_comment');

/**************** set up display page ********************/

$objHead = new htmlheading();
$objHead->type=3;
$objHead->str=$name;

echo $objHead->show();

$objLayer1 = new layer();
$objLayer1->cssClass='even';
$objLayer1->align='center';
$objLayer1->border='1px solid; border-bottom: 0';
$objLayer1->str='<font size=2><b>'.$head.'</b></font>';
$layerStr = $objLayer1->show();

$objLayer2 = new layer();
$objLayer2->cssClass='odd';
$objLayer2->align='justify';
$objLayer2->border='1px solid; border-top: 0';
$objLayer2->str=$comment;
$layerStr .= $objLayer2->show();

$objIcon->setIcon('close');
$objIcon->extra=" onclick='javascript:window.close()'";
$objLayer3 = new layer();
$objLayer3->align='center';
$objLayer3->str=$objIcon->show();
$layerStr .= '<p>'.$objLayer3->show();

$objLayer = new layer();
$objLayer->cssClass='content';
$objLayer->border='1px solid';
$objLayer->align='center';
$objLayer->str = $layerStr;

echo $objLayer->show();

?>