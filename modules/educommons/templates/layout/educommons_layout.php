<?php

$objCssLayout = $this->newObject('csslayout', 'htmlelements');

$objNav = $this->getObject('contextadminnav', 'contextadmin');
$objCssLayout->setLeftColumnContent($objNav->show());
$objCssLayout->setMiddleColumnContent($this->getContent());

echo $objCssLayout->show();

?>
