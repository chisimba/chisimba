<?php
/**
* Template layout for practicals management module
* @package practicals
*/

/**
* Template layout for practicals management module
*/

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$toolbar = $this->getObject('contextsidebar', 'context');

$cssLayout->setLeftColumnContent($toolbar->show());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>