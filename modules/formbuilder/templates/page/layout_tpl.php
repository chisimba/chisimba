<?php

$cssLayout=&$this->newObject('csslayout','htmlelements');

//$userMenuBar=& $this->getObject('sidemenu','toolbar');
//$toolbar = $this->getObject('contextsidebar', 'context');

//$objBlocks = $this->getObject('blocks', 'blocks');

$cssLayout->setNumColumns(2);

///$leftColumnContent = $userMenuBar->menuUser();
//$leftColumnContent .= $objBlocks->showBlock('fullprofile', 'fullprofile');

$cssLayout->setLeftColumnContent("sdfsdfsdffsdfsfsf");
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>