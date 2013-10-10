<?php
/**
* Template layout for gradebook module
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->_objDBContext = $this->getObject('dbcontext','context');
if($this->_objDBContext->isInContext())
{
    $objContextUtils = & $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('gradebook','none');
} else {
    $cm ='';
}
$toolbar = $this->getObject('contextsidebar', 'context');
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$leftMenu =& $this->newObject('sidemenu','toolbar');
$left = $toolbar->show();
$left = "<div class='gradebook_left'>$left</div>";
$middle = $this->getContent();
$middle = "<div class='gradebook_main'>$middle</div>";
$cssLayout->setLeftColumnContent($left);
$cssLayout->setMiddleColumnContent($middle);
echo $cssLayout->show();
?>