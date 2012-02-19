<?php
/**
* @package contextgroups
*/

/**
* Layout template for the contextgroups module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$leftMenu = $this->newObject('contextsidebar','context');


$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($this->getContent());

 $objModule = $this->getObject('modules', 'modulecatalogue');
$isRegistered = $objModule->checkIfRegistered('oer');
if ($isRegistered) {
    echo '<div id="onecolumn">' . $cssLayout->show() . '</div>';
} else {
    echo $cssLayout->show();
}
?>