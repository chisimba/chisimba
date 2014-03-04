<?php
$this->loadClass('link', 'htmlelements');

$objLandingPage = new link($this->uri(array('action'=>'home')));
$objLandingPage->link='home';
$landinging = $objLandingPage->show();

$objFeedbk = new link($this->uri(array('action'=>'tellus')));
$objFeedbk->link='Tell us Feed back';
$fdb = $objFeedbk->show();


$objperiodical = new link($this->uri(array('action'=>'periodicalbooks')));
$objperiodical->link='ILL: Periodical books';
$pd =$objperiodical->show();



$objbookthesis = new link($this->uri(array('action'=>'thesisbooks')));
$objbookthesis->link='ILL: Thesis books';
$bt =$objbookthesis->show();

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent('<br />'.$landinging.'<br />'.$fdb.'<br />'.$pd.'<br />'.$bt);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();

?>






