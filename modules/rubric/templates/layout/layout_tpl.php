<?php
$objDBContext = & $this->getObject('dbcontext','context');
if($objDBContext->isInContext())
{
    $objContextUtils = & $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('rubric','show');
} else {
    $cm = '';//$this->getMenu();
}

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
/*if ($this->objUser->isContextLecturer()|| $this->objUser->isContextStudent() ) {
	$userMenuBar=& $this->getObject('sidemenu','toolbar');
}
else if ($this->objUser->isLecturer()) {
	$userMenuBar=& $this->getObject('sidemenu','toolbar');
}
else {
	die('Access denied');
}*/
$userMenuBar=& $this->getObject('sidemenu','toolbar');
$toolbar = $this->getObject('contextsidebar', 'context');
$cssLayout->setLeftColumnContent($toolbar->show());
$ret = $this->getContent();
$ret = "<div class='rubric_main'>$ret</div>";
$cssLayout->setMiddleColumnContent($ret);
echo $cssLayout->show();
?>
