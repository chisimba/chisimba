<?php
$objDBContext = & $this->getObject('dbcontext','context');
if($objDBContext->isInContext())
{
    $objContextUtils = & $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('readinglist','none');
} else {
    $cm = '';
}

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
	$userMenuBar=& $this->getObject('sidemenu','toolbar');

	$cssLayout->setLeftColumnContent($userMenuBar->menuContext().$cm);
	$cssLayout->setMiddleColumnContent($this->getContent());

	echo $cssLayout->show() ;
?>