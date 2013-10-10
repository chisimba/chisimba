<?php

$cssLayout=&$this->newObject('csslayout','htmlelements');

$userMenuBar=& $this->getObject('sidemenu','toolbar');
$toolbar = $this->getObject('contextsidebar', 'context');
$cssLayout->setNumColumns(2);

$cssLayout->setLeftColumnContent($userMenuBar->menuUser());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>