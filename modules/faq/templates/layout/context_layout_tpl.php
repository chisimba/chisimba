<?php
/*$this->_objDBContext =& $this->getObject('dbcontext','context');
if($this->_objDBContext->isInContext())
{
    $objContextUtils = & $this->getObject('utilities','context');
    $leftMenu =& $this->newObject('sidemenu','toolbar');
    $cm = $leftMenu->menuUser('context');//.$objContextUtils->getHiddenContextMenu('faq','none');
} else {
    $cm ='';
}*/
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$this->objContext = $this->getObject('dbcontext','context');
$isInContext=$this->objContext->isInContext();
if ($isInContext) {
    $toolbar = $this->getObject('contextsidebar', 'context');
    $cssLayout->setLeftColumnContent($toolbar->show());
} else {
    $objBlock = $this->getObject('blocks', 'blocks');
    $ret = $objBlock->showBlock('latestcats', 'faq');
    $ret .= $objBlock->showBlock('latestquestions', 'faq');
    // Put a temp link until I have written some blocks DWK.
    $cssLayout->setLeftColumnContent($ret);
}
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>