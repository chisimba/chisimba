<?php

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

if ($this->objContext->getContextCode() == '') {
    $toolbar = $this->getObject('postloginmenu_elearn', 'toolbar');
} else {
    $toolbar = $this->getObject('contextsidebar', 'context');
}


// Initialize left column
$leftSideColumn = $toolbar->show();

$cssLayout->setLeftColumnContent($leftSideColumn);
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>
